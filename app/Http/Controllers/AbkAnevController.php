<?php

namespace App\Http\Controllers;

use App\Models\AbkAnev;
use App\Models\Patroli;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AbkAnevController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, [
            'admin',
            'pimpinan',
            'komandan',
            'abk'
        ])) {
            abort(403);
        }

        $search = trim($request->search ?? '');
        $status = $request->status;
        $kapal = $request->kapal;

        $query = AbkAnev::with([
            'patroli.kapal',
            'pembuatLaporan',
            'user'
        ]);

        if ($search !== '') {

            $query->where(function ($q) use ($search) {

                $q->whereHas('patroli.kapal', function ($kapalQuery) use ($search) {
                    $kapalQuery->where('kode_kapal', 'like', "%{$search}%");
                });

                $q->orWhereHas('patroli', function ($patroliQuery) use ($search) {
                    $patroliQuery->where('wilayah_patroli', 'like', "%{$search}%");
                });

                $q->orWhereHas('pembuatLaporan', function ($userQuery) use ($search) {
                    $userQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('nrp', 'like', "%{$search}%");
                });

            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($kapal) {
            $query->whereHas('patroli.kapal', function ($q) use ($kapal) {
                $q->where('id', $kapal);
            });
        }

        $anevs = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $kapals = \App\Models\Kapal::orderBy('kode_kapal')->get();

        return view('abk-anev.index', compact(
            'anevs',
            'kapals',
            'search',
            'status',
            'kapal'
        ));
    }

    public function edit(Patroli $patroli)
    {
        $this->ensureAbkCanAccess($patroli);

        $anev = AbkAnev::firstOrCreate(
            ['patroli_id' => $patroli->id],
            [
                'user_id' => auth()->id(),
                'status' => 'draft',
            ]
        );

        $patroli->load([
            'kapal.komandan',
            'personels',
            'abkLaporan.riksaKapals',
            'sopProgress.sop',
        ]);

        $users = User::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('abk-anev.edit', compact('anev', 'patroli', 'users'));
    }

    public function update(Request $request, Patroli $patroli)
    {
        $this->ensureAbkCanAccess($patroli);

        $request->validate([
            'hambatan' => ['nullable', 'string'],
            'kendala' => ['nullable', 'string'],
            'pembuat_laporan_id' => ['required', 'exists:users,id'],
            'foto_anev' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'pembuat_laporan_id.required' => 'Nama pembuat laporan wajib dipilih.',
            'foto_anev.image' => 'Bukti ANEV harus berupa gambar.',
            'foto_anev.mimes' => 'Bukti ANEV harus berformat JPG, JPEG, atau PNG.',
            'foto_anev.max' => 'Ukuran bukti ANEV maksimal 5MB.',
        ]);

        $anev = AbkAnev::firstOrCreate(
            ['patroli_id' => $patroli->id],
            [
                'user_id' => auth()->id(),
                'status' => 'draft',
            ]
        );

        DB::transaction(function () use ($request, $anev, $patroli) {
            $data = [
                'user_id' => auth()->id(),
                'pembuat_laporan_id' => $request->pembuat_laporan_id,
                'hambatan' => $request->hambatan,
                'kendala' => $request->kendala,
                'status' => 'tersimpan',
            ];

            if ($request->hasFile('foto_anev')) {
                if ($anev->foto_anev) {
                    Storage::disk('public')->delete($anev->foto_anev);
                }

                $data['foto_anev'] = $request->file('foto_anev')
                    ->store('anev/foto', 'public');
            }

            $anev->update($data);

            $this->markSop15Done($patroli);

            $this->resetStatusJikaPerbaiki($patroli);
        });

        return redirect()
            ->route('abk-anev.edit', $patroli)
            ->with('success', 'ANEV berhasil disimpan dan SOP 15 diperbarui.');
    }

    public function export(Patroli $patroli)
    {
        $this->ensureCanExport($patroli);

        $anev = AbkAnev::with([
            'patroli.kapal.komandan',
            'patroli.personels',
            'patroli.abkLaporan.riksaKapals',
            'pembuatLaporan',
        ])
            ->where('patroli_id', $patroli->id)
            ->firstOrFail();

        $pdf = Pdf::loadView('abk-anev.pdf', [
            'anev' => $anev,
            'patroli' => $patroli,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('anev-abk-' . ($patroli->kapal->kode_kapal ?? 'kapal') . '.pdf');
    }

    private function ensureAbkCanAccess(Patroli $patroli): void
    {
        if (!in_array(auth()->user()->role, [
            'admin',
            'pimpinan',
            'komandan',
            'abk'
        ])) {
            abort(403);
        }

        $isPersonel = $patroli->personels()
            ->where('users.id', auth()->id())
            ->exists();

        if (! $isPersonel) {
            abort(403, 'Anda tidak terdaftar sebagai ABK pada patroli ini.');
        }

        $sop14Selesai = $patroli->sopProgress()
            ->where('status', 'selesai')
            ->whereHas('sop', function ($query) {
                $query->where('urutan', 14);
            })
            ->exists();

        if (! $sop14Selesai) {
            abort(403, 'ANEV baru bisa diisi setelah SOP 14 selesai.');
        }
    }

    private function ensureCanExport(Patroli $patroli): void
    {
        $user = auth()->user();

        if (in_array($user->role, [
            'admin',
            'pimpinan',
            'komandan',
            'abk'
        ])) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke ANEV ini.');
    }

    private function markSop15Done(Patroli $patroli): void
    {
        $progress = $patroli->sopProgress()
            ->whereHas('sop', fn ($query) => $query->where('urutan', 15))
            ->first();

        if ($progress) {
            $progress->update([
                'status' => 'selesai',
                'waktu_mulai' => $progress->waktu_mulai ?? now(),
                'waktu_selesai' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    }

    private function resetStatusJikaPerbaiki(Patroli $patroli): void
    {
        if ($patroli->status === 'perbaiki') {
            $patroli->update([
                'status' => 'selesai',
                'validasi_pimpinan_status' => null,
                'validasi_pimpinan_user_id' => null,
                'validasi_pimpinan_at' => null,
            ]);
        }
    }
}