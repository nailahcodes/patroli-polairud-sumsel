<?php

namespace App\Http\Controllers;

use App\Models\Kapal;
use App\Models\User;
use Illuminate\Http\Request;

class KapalController extends Controller
{
    private function ensureAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengelola data kapal.');
        }
    }

    public function index(Request $request)
    {
        $sort = $request->get('sort', 'asc');
        $sort = in_array($sort, ['asc', 'desc']) ? $sort : 'asc';

        $kode = $request->get('kode_kapal');
        $zona = $request->get('zona_patroli');
        $search = trim($request->get('search', ''));

        $query = Kapal::with('komandan');

        if ($kode) {
            $query->where('kode_kapal', 'like', '%' . $kode . '%');
        }

        if ($zona) {
            $query->where('zona_patroli', $zona);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_kapal', 'like', '%' . $search . '%')
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
            });
        }

        $kapals = $query
            ->orderBy('kode_kapal', $sort)
            ->paginate(10)
            ->withQueryString();

        $zonaList = Kapal::select('zona_patroli')
            ->whereNotNull('zona_patroli')
            ->distinct()
            ->orderBy('zona_patroli')
            ->pluck('zona_patroli');

        return view('kapal.index', compact(
            'kapals',
            'zonaList',
            'sort',
            'kode',
            'zona',
            'search'
        ));
    }

    public function create()
    {
        $this->ensureAdmin();

        $komandans = User::where('role', 'komandan')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $zonaOptions = [
            'Zona 1 30 Ilir dan Lematang' => 'Perairan Sei 30 Ilir, Sei Buaya, Bom Baru',
            'Zona 2 Muara Kumbang dan Upang' => 'Perairan Sei Musi Muara Kumbang, Sebaling, Gasing',
            'Zona 3 Simpang PU' => 'Perairan Sei Talang Simpang PU, Bunga Tanjung, Karang Anyar',
            'Zona 4 Sungsang' => 'Perairan Laut Sungsang, Sungsang 1, Sungsang 2',
            'Zona 5 Sungai Lilin' => 'Perairan Sungai Mukut, Sungai Tobo, Sumber Rejo',
            'Zona 6 Sujian' => 'Perairan Laut Sujian, Tj. Baru, Tj. Selokan',
            'Zona 7 Muara Lalan' => 'Perairan Sungai Lalan, Muara Prime, Selat Kuningan',
            'Zona 8 P13' => 'Perairan P11, P12, P13',
            'Zona 9 Kepahyang' => 'Perairan Kepahyang, Muara Medak, Mendis',
            'Zona 10 Sembilang' => 'Perairan Sembilang, Sungai Benawang, Sungai Merawan',
            'Zona 11 Sei Benu' => 'Perairan Sei Bunu, Terusan Dalam, Terusan Luar',
            'Zona 12 Penuguan' => 'Perairan Karang Agung, Sei Penuguan, Lilin, Pulau Rimau',
            'Zona 13 Sungai Baung' => 'Perairan Jalur 21, Jalur 20, Jalur 19',
        ];

        return view('kapal.create', compact(
            'komandans',
            'zonaOptions'
        ));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'kelompok' => ['nullable', 'string', 'max:100'],
            'kode_kapal' => ['required', 'string', 'max:100'],
            'zona_patroli' => ['nullable', 'string', 'max:100'],
            'wilayah_patroli' => ['nullable', 'string', 'max:255'],
            'komandan_id' => ['nullable', 'exists:users,id'],
            'komandan_kapal' => ['nullable', 'string', 'max:255'],
        ]);

        Kapal::create($validated);

        return redirect()
            ->route('kapal.index')
            ->with('success', 'Data kapal berhasil ditambahkan.');
    }

    public function edit(Kapal $kapal)
    {
        $this->ensureAdmin();

        $komandans = User::where('role', 'komandan')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $zonaOptions = [
            'Zona 1 30 Ilir dan Lematang' => 'Perairan Sei 30 Ilir, Sei Buaya, Bom Baru',
            'Zona 2 Muara Kumbang dan Upang' => 'Perairan Sei Musi Muara Kumbang, Sebaling, Gasing',
            'Zona 3 Simpang PU' => 'Perairan Sei Talang Simpang PU, Bunga Tanjung, Karang Anyar',
            'Zona 4 Sungsang' => 'Perairan Laut Sungsang, Sungsang 1, Sungsang 2',
            'Zona 5 Sungai Lilin' => 'Perairan Sungai Mukut, Sungai Tobo, Sumber Rejo',
            'Zona 6 Sujian' => 'Perairan Laut Sujian, Tj. Baru, Tj. Selokan',
            'Zona 7 Muara Lalan' => 'Perairan Sungai Lalan, Muara Prime, Selat Kuningan',
            'Zona 8 P13' => 'Perairan P11, P12, P13',
            'Zona 9 Kepahyang' => 'Perairan Kepahyang, Muara Medak, Mendis',
            'Zona 10 Sembilang' => 'Perairan Sembilang, Sungai Benawang, Sungai Merawan',
            'Zona 11 Sei Benu' => 'Perairan Sei Bunu, Terusan Dalam, Terusan Luar',
            'Zona 12 Penuguan' => 'Perairan Karang Agung, Sei Penuguan, Lilin, Pulau Rimau',
            'Zona 13 Sungai Baung' => 'Perairan Jalur 21, Jalur 20, Jalur 19',
        ];

        return view('kapal.edit', compact(
            'kapal',
            'komandans',
            'zonaOptions'
        ));
    }

    public function update(Request $request, Kapal $kapal)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'kelompok' => ['nullable', 'string', 'max:100'],
            'kode_kapal' => ['required', 'string', 'max:100'],
            'zona_patroli' => ['nullable', 'string', 'max:100'],
            'wilayah_patroli' => ['nullable', 'string', 'max:255'],
            'komandan_id' => ['nullable', 'exists:users,id'],
            'komandan_kapal' => ['nullable', 'string', 'max:255'],
        ]);

        $kapal->update($validated);

        return redirect()
            ->route('kapal.index')
            ->with('success', 'Data kapal berhasil diperbarui.');
    }

    public function destroy(Kapal $kapal)
    {
        $this->ensureAdmin();

        $kapal->delete();

        return redirect()
            ->route('kapal.index')
            ->with('success', 'Data kapal berhasil dihapus.');
    }
}
