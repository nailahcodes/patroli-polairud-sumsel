<?php

namespace App\Http\Controllers;

use App\Models\Kapal;
use App\Models\Patroli;
use App\Models\Sop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatroliController extends Controller
{
    private function ensureAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengelola data patroli.');
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $sort = $request->get('sort', 'desc');
        $sort = in_array($sort, ['asc', 'desc']) ? $sort : 'desc';

        $status = $request->get('status');
        $kapalId = $request->get('kapal_id');
        $search = trim($request->get('search', ''));

        $query = Patroli::with(['kapal.komandan', 'personels', 'sopProgress.sop']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($kapalId) {
            $query->where('kapal_id', $kapalId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_sprin', 'like', '%' . $search . '%')
                    ->orWhere('wilayah_patroli', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%')
                    ->orWhereHas('kapal', function ($kapalQuery) use ($search) {
                        $kapalQuery->where('kode_kapal', 'like', '%' . $search . '%')
                            ->orWhere('kelompok', 'like', '%' . $search . '%')
                            ->orWhere('zona_patroli', 'like', '%' . $search . '%')
                            ->orWhere('wilayah_patroli', 'like', '%' . $search . '%')
                            ->orWhere('komandan_kapal', 'like', '%' . $search . '%')
                            ->orWhereHas('komandan', function ($komandanQuery) use ($search) {
                                $komandanQuery->where('nama', 'like', '%' . $search . '%')
                                    ->orWhere('nrp', 'like', '%' . $search . '%')
                                    ->orWhere('pangkat', 'like', '%' . $search . '%')
                                    ->orWhere('jabatan', 'like', '%' . $search . '%');
                            });
                    })
                    ->orWhereHas('personels', function ($personelQuery) use ($search) {
                        $personelQuery->where('nama', 'like', '%' . $search . '%')
                            ->orWhere('nrp', 'like', '%' . $search . '%')
                            ->orWhere('pangkat', 'like', '%' . $search . '%')
                            ->orWhere('jabatan', 'like', '%' . $search . '%');
                    });
            });
        }

        $patrolis = $query
            ->orderBy('created_at', $sort)
            ->paginate(10)
            ->withQueryString();

        $kapals = Kapal::orderBy('kode_kapal')->get();

        return view('patroli.index', compact(
            'patrolis',
            'kapals',
            'sort',
            'status',
            'kapalId',
            'search'
        ));
    }

    public function create()
    {
        $this->ensureAdmin();

        $kapals = Kapal::with('komandan')
            ->where('status', '!=', 'perawatan')
            ->whereNotIn('id', function ($query) {
                $query->select('kapal_id')
                    ->from('patrolis')
                    ->where('status', 'berjalan');
            })
            ->orderBy('kode_kapal')
            ->get();

        $abks = User::where('role', 'abk')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('patroli.create', compact('kapals', 'abks'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $kapal = Kapal::findOrFail($request->kapal_id);

            if ($kapal->status === 'perawatan') {
                return back()->withErrors([
                    'kapal_id' => 'Kapal sedang dalam perawatan.'
                ]);
            }

            $dipakaiPatroli = Patroli::where('kapal_id', $kapal->id)
                ->where('status', 'berjalan')
                ->exists();

            if ($dipakaiPatroli) {
                return back()->withErrors([
                    'kapal_id' => 'Kapal sedang digunakan pada patroli lain.'
                ]);
            }

        $validated = $request->validate([
            'nomor_sprin' => ['nullable', 'string', 'max:255'],
            'kapal_id' => ['required', 'exists:kapals,id'],
            'wilayah_patroli' => ['required', 'string', 'max:255'],
            'tanggal_persiapan' => ['required', 'date'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable', 'string'],
            'personel_ids' => ['nullable', 'array'],
            'personel_ids.*' => ['exists:users,id'],
        ]);

        DB::transaction(function () use ($request, $validated, &$patroli) {
            $patroli = Patroli::create([
                'nomor_sprin' => $validated['nomor_sprin'] ?? null,
                'kapal_id' => $validated['kapal_id'],
                'wilayah_patroli' => $validated['wilayah_patroli'],
                'tanggal_persiapan' => $validated['tanggal_persiapan'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'status' => 'draft',
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            $syncData = [];

            foreach ($request->input('personel_ids', []) as $userId) {
                $syncData[$userId] = [
                    'posisi' => 'ABK Kapal',
                ];
            }

            $patroli->personels()->sync($syncData);

            $sops = Sop::orderBy('urutan')->get();

            foreach ($sops as $sop) {
                $patroli->sopProgress()->create([
                    'sop_id' => $sop->id,
                    'status' => 'belum',
                ]);
            }
        });

        return redirect()
            ->route('patroli.show', $patroli)
            ->with('success', 'Patroli berhasil dibuat dan personel ABK berhasil disimpan.');
    }

    public function show(Patroli $patroli)
    {
        $patroli->load([
            'kapal.komandan',
            'personels',
            'sopProgress.sop',
            'abkLaporan.riksaKapals',
            'abkLaporan.lampirans',
            'abkAnev',
            'validatorPimpinan',
        ]);
    
        return view('patroli.show', compact('patroli'));
    }

    public function edit(Patroli $patroli)
    {
        $this->ensureAdmin();

        $patroli->load(['kapal.komandan', 'personels']);

        $kapals = Kapal::with('komandan')
            ->where(function ($query) use ($patroli) {

                $query->where('status', '!=', 'perawatan')

                    ->whereNotIn('id', function ($sub) use ($patroli) {

                        $sub->select('kapal_id')
                            ->from('patrolis')
                            ->where('status', 'berjalan')
                            ->where('id', '!=', $patroli->id);
                    });

            })

            ->orWhere('id', $patroli->kapal_id)

            ->orderBy('kode_kapal')
            ->get();

        $abks = User::where('role', 'abk')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('patroli.edit', compact('patroli', 'kapals', 'abks'));
    }

    public function update(Request $request, Patroli $patroli)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'nomor_sprin' => ['nullable', 'string', 'max:255'],
            'kapal_id' => ['required', 'exists:kapals,id'],
            'wilayah_patroli' => ['required', 'string', 'max:255'],
            'tanggal_persiapan' => ['required', 'date'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable', 'string'],
            'personel_ids' => ['nullable', 'array'],
            'personel_ids.*' => ['exists:users,id'],
        ]);

        DB::transaction(function () use ($request, $validated, $patroli) {
            $patroli->update([
                'nomor_sprin' => $validated['nomor_sprin'] ?? null,
                'kapal_id' => $validated['kapal_id'],
                'wilayah_patroli' => $validated['wilayah_patroli'],
                'tanggal_persiapan' => $validated['tanggal_persiapan'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            $syncData = [];

            foreach ($request->input('personel_ids', []) as $userId) {
                $syncData[$userId] = [
                    'posisi' => 'ABK Kapal',
                ];
            }

            $patroli->personels()->sync($syncData);
        });

        return redirect()
            ->route('patroli.show', $patroli)
            ->with('success', 'Data patroli dan personel ABK berhasil diperbarui.');
    }

    public function destroy(Patroli $patroli)
    {
        $this->ensureAdmin();

        $patroli->delete();

        return redirect()
            ->route('patroli.index')
            ->with('success', 'Data patroli berhasil dihapus.');
    }
}