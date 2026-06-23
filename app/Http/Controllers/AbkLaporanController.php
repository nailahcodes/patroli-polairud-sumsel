<?php

namespace App\Http\Controllers;

use App\Models\AbkLaporan;
use App\Models\Patroli;
use App\Models\RiksaKapal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use iio\libmergepdf\Merger;
use App\Models\AbkLampiran;

class AbkLaporanController extends Controller
{
    private array $anggaranItems = [
        'Uang Lauk Pauk (ULP)',
        'Uang Saku Komandan Kapal',
        'Uang Saku (Pa Nautika)',
        'Uang Saku (Pa Teknika)',
        'Uang Saku (ABK)',
        'Uang Saku Air Tawar',
    ];

    private array $logistikItems = [
        'Pertamax',
        'Prima XP',
        'Rored EPA',
    ];

    private array $lampiranPdf = [
        'absensi_personel',
        'daftar_nama_personel',
        'berita_acara_penyerahan_materil',
    ];

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, [
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

        $query = AbkLaporan::with([
            'patroli.kapal',
            'patroli.personels',
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

                $q->orWhereHas('user', function ($userQuery) use ($search) {
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

        $laporans = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $kapals = \App\Models\Kapal::orderBy('kode_kapal')->get();

        return view('abk-laporan.index', compact(
            'laporans',
            'kapals',
            'search',
            'status',
            'kapal'
        ));
    }

    public function edit(Patroli $patroli)
    {
        $this->ensureAbkCanAccess($patroli);

        $laporan = AbkLaporan::firstOrCreate(
            ['patroli_id' => $patroli->id],
            [
                'user_id' => auth()->id(),
                'status' => 'draft',
            ]
        );

        foreach ($this->anggaranItems as $item) {
            $laporan->anggarans()->firstOrCreate(
                ['komponen' => $item],
                ['nominal' => 0]
            );
        }

        foreach ($this->logistikItems as $item) {
            $laporan->logistiks()->firstOrCreate(
                ['jenis' => $item],
                ['jumlah_liter' => 0]
            );
        }

        $laporan->koordinats()->firstOrCreate(
            ['jenis' => 'bertolak'],
            ['koordinat' => null]
        );

        $laporan->koordinats()->firstOrCreate(
            ['jenis' => 'bersandar'],
            ['koordinat' => null]
        );

        $laporan->load([
            'patroli.kapal.komandan',
            'patroli.personels',
            'patroli.sopProgress.sop',
            'anggarans',
            'logistiks',
            'koordinats',
            'riksaKapals',
            'lampirans',
        ]);

        return view('abk-laporan.edit', compact('laporan', 'patroli'));
    }

    public function update(Request $request, Patroli $patroli)
    {

        $this->ensureAbkCanAccess($patroli);

        $laporan = AbkLaporan::firstOrCreate(
            ['patroli_id' => $patroli->id],
            [
                'user_id' => auth()->id(),
                'status' => 'draft',
            ]
        );

        $request->validate([
            'anggaran' => ['nullable', 'array'],
            'anggaran.*' => ['nullable', 'numeric', 'min:0'],

            'logistik' => ['nullable', 'array'],
            'logistik.*' => ['nullable', 'numeric', 'min:0'],

            'koordinat_bertolak' => ['nullable', 'string', 'max:255'],
            'koordinat_bersandar' => ['nullable', 'string', 'max:255'],

            'total_pengisian_bbm' => ['nullable', 'numeric', 'min:0'],
            'total_stock_bbm_tangki' => ['nullable', 'numeric', 'min:0'],
            'total_jarak_tempuh' => ['nullable', 'numeric', 'min:0'],
            'total_pemakaian_bbm' => ['nullable', 'numeric', 'min:0'],
            'pemakaian_bbm_selama_layar' => ['nullable', 'numeric', 'min:0'],
            'kecepatan_rata_rata' => ['nullable', 'numeric', 'min:0'],
            'sisa_bbm_selesai_patroli' => ['nullable', 'numeric', 'min:0'],

            'riksa' => ['nullable', 'array'],
            'riksa.*.id' => ['nullable', 'exists:riksa_kapals,id'],
            'riksa.*.nama_kapal' => ['nullable', 'string', 'max:255'],
            'riksa.*.nama_nahkoda' => ['nullable', 'string', 'max:255'],
            'riksa.*.dari_tujuan' => ['nullable', 'string', 'max:255'],
            'riksa.*.muatan' => ['nullable', 'string', 'max:255'],
            'riksa.*.titik_koordinat' => ['nullable', 'string', 'max:255'],
            'riksa.*.kategori' => ['nullable', 'in:aman,tindak_pidana,pelanggaran'],
            'riksa.*.penjelasan' => ['nullable', 'string'],

            'riksa.*.foto_riksa' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'riksa.*.foto_binluh' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'riksa.*.surat_hasil_pemeriksaan' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            'lampiran.absensi_personel' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'lampiran.daftar_nama_personel' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'lampiran.berita_acara_penyerahan_materil' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'lampiran.foto_pengisian_air_tawar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $laporan, $patroli) {
            foreach ($this->anggaranItems as $item) {
                $laporan->anggarans()->updateOrCreate(
                    ['komponen' => $item],
                    ['nominal' => $request->input('anggaran.' . $this->key($item), 0)]
                );
            }

            foreach ($this->logistikItems as $item) {
                $laporan->logistiks()->updateOrCreate(
                    ['jenis' => $item],
                    ['jumlah_liter' => $request->input('logistik.' . $this->key($item), 0)]
                );
            }

            $laporan->koordinats()->updateOrCreate(
                ['jenis' => 'bertolak'],
                ['koordinat' => $request->koordinat_bertolak]
            );

            $laporan->koordinats()->updateOrCreate(
                ['jenis' => 'bersandar'],
                ['koordinat' => $request->koordinat_bersandar]
            );

            $laporan->update([
                'total_pengisian_bbm' => $request->total_pengisian_bbm ?? 0,
                'total_stock_bbm_tangki' => $request->total_stock_bbm_tangki ?? 0,
                'total_jarak_tempuh' => $request->total_jarak_tempuh ?? 0,
                'total_pemakaian_bbm' => $request->total_pemakaian_bbm ?? 0,
                'pemakaian_bbm_selama_layar' => $request->pemakaian_bbm_selama_layar ?? 0,
                'kecepatan_rata_rata' => $request->kecepatan_rata_rata ?? 0,
                'sisa_bbm_selesai_patroli' => $request->sisa_bbm_selesai_patroli ?? 0,
                'status' => 'tersimpan',
                'user_id' => auth()->id(),
            ]);

            $this->syncRiksaKapals($request, $laporan);

            $this->syncKronologis($request, $laporan);

            foreach ($this->lampiranPdf as $jenis) {

                if ($request->hasFile("lampiran.$jenis")) {

                    $file = $request->file("lampiran.$jenis");

                    $path = $file->store(
                        'lampiran-abk',
                        'public'
                    );

                    $laporan->lampirans()->create([
                        'jenis' => $jenis,
                        'file_path' => $path,
                    ]);
                }
            }

            if ($request->hasFile('lampiran.foto_pengisian_air_tawar')) {

                $file = $request->file(
                    'lampiran.foto_pengisian_air_tawar'
                );

                $path = $file->store(
                    'lampiran-abk',
                    'public'
                );

                $laporan->lampirans()->create([
                    'jenis' => 'foto_pengisian_air_tawar',
                    'file_path' => $path,
                ]);
            }

            $this->markSop14Done($patroli);
            $this->resetStatusJikaPerbaiki($patroli);
        });

        return redirect()
            ->route('abk-laporan.edit', $patroli)
            ->with('success', 'Laporan ABK berhasil disimpan dan SOP 14 diperbarui.');
    }

    public function export(Patroli $patroli)
    {
        $this->ensureCanExport($patroli);

        $patroli->load([
            'kapal.komandan',
            'personels',
            'sopProgress.sop',
        ]);

        $laporan = AbkLaporan::with([
            'patroli.kapal.komandan',
            'patroli.personels',
            'patroli.sopProgress.sop',
            'anggarans',
            'logistiks',
            'koordinats',
            'riksaKapals',
            'lampirans',
        ])
            ->where('patroli_id', $patroli->id)
            ->firstOrFail();

        $mainPdf = Pdf::loadView('abk-laporan.pdf', [
            'laporan' => $laporan,
            'patroli' => $patroli,
            'durasiPelayaran' => $this->hitungDurasiPelayaran($patroli),
            'timezone' => config('app.timezone', 'Asia/Jakarta'),
        ])->setPaper('a4', 'portrait');

        $fileName = 'laporan-abk-' . ($patroli->kapal->kode_kapal ?? 'kapal') . '.pdf';

        return $this->downloadMergedPdf(
            $mainPdf->output(),
            $laporan,
            $patroli,
            $fileName
        );
    }

    public function hapusLampiran(AbkLampiran $lampiran)
    {
        if ($lampiran->file_path) {
            Storage::disk('public')->delete($lampiran->file_path);
        }

        $lampiran->delete();

        return back()->with(
            'success',
            'Lampiran berhasil dihapus.'
        );
    }

    private function downloadMergedPdf(string $mainPdfContent, AbkLaporan $laporan, Patroli $patroli, string $fileName)
    {
        if (! class_exists(Merger::class)) {
            return response($mainPdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        $tempDir = storage_path('app/temp-pdf');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mainPath = $tempDir . '/laporan-utama-' . $laporan->id . '-' . time() . '.pdf';

        file_put_contents($mainPath, $mainPdfContent);

        $merger = new Merger();
        $merger->addFile($mainPath);

        foreach ($this->collectLampiranPdfPaths($laporan, $patroli) as $path) {

            if (
                $path &&
                file_exists($path) &&
                strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf'
            ) {
                $merger->addFile($path);
            }
        }

        foreach ($this->lampiranPdf as $jenis) {

            foreach (
                $laporan->lampirans
                    ->where('jenis', $jenis)
                as $lampiran
            ) {

                if (! empty($lampiran->file_path)) {

                    $paths[] = $this->publicStoragePath(
                        $lampiran->file_path
                    );

                }
            }
        }

        try {
            $mergedPdf = $merger->merge();
        } catch (\Throwable $e) {
            if (file_exists($mainPath)) {
                unlink($mainPath);
            }

            return response($mainPdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        if (file_exists($mainPath)) {
            unlink($mainPath);
        }

        return response($mergedPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function collectLampiranPdfPaths(
        AbkLaporan $laporan,
        Patroli $patroli
    ): array {

        $paths = [];

        $sop4 = $patroli->sopProgress
            ->first(fn ($progress)
                => optional($progress->sop)->urutan == 4);

        if ($sop4) {

            if (! empty($sop4->bukti_file)) {
                $paths[] = $this->publicStoragePath(
                    $sop4->bukti_file
                );
            }

            if (! empty($sop4->bukti_file_2)) {
                $paths[] = $this->publicStoragePath(
                    $sop4->bukti_file_2
                );
            }
        }

        foreach ($laporan->riksaKapals as $riksa) {

            if (! empty($riksa->surat_hasil_pemeriksaan)) {

                $paths[] = $this->publicStoragePath(
                    $riksa->surat_hasil_pemeriksaan
                );
            }
        }

        foreach ($this->lampiranPdf as $jenis) {

            foreach (
                $laporan->lampirans
                    ->where('jenis', $jenis)
                as $lampiran
            ) {

                if (! empty($lampiran->file_path)) {

                    $paths[] = $this->publicStoragePath(
                        $lampiran->file_path
                    );
                }
            }
        }

        return array_values(
            array_unique(
                array_filter($paths)
            )
        );
    }

    private function publicStoragePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return storage_path('app/public/' . $path);
    }

    private function syncRiksaKapals(Request $request, AbkLaporan $laporan): void
    {
        $existingIds = $laporan->riksaKapals()->pluck('id')->toArray();
        $incomingIds = [];

        foreach ($request->input('riksa', []) as $index => $item) {
            if (empty($item['nama_kapal']) && empty($item['nama_nahkoda'])) {
                continue;
            }

            $riksa = null;

            if (! empty($item['id'])) {
                $riksa = $laporan->riksaKapals()
                    ->where('id', $item['id'])
                    ->first();

                if ($riksa) {
                    $incomingIds[] = $riksa->id;
                }
            }

            if (! $riksa) {

                $riksa = new RiksaKapal();

                $riksa->abk_laporan_id = $laporan->id;

                $riksa->waktu_kejadian = now();
            }

            $riksa->nama_kapal = $item['nama_kapal'] ?? null;
            $riksa->nama_nahkoda = $item['nama_nahkoda'] ?? null;
            $riksa->dari_tujuan = $item['dari_tujuan'] ?? null;
            $riksa->muatan = $item['muatan'] ?? null;
            $riksa->titik_koordinat = $item['titik_koordinat'] ?? null;
            $riksa->kategori = $item['kategori'] ?? 'aman';
            $riksa->penjelasan = $item['penjelasan'] ?? null;

            if ($request->hasFile("riksa.$index.foto_riksa")) {
                if ($riksa->foto_riksa) {
                    Storage::disk('public')->delete($riksa->foto_riksa);
                }

                $riksa->foto_riksa = $request->file("riksa.$index.foto_riksa")
                    ->store('riksa/foto-riksa', 'public');
            }

            if ($request->hasFile("riksa.$index.foto_binluh")) {
                if ($riksa->foto_binluh) {
                    Storage::disk('public')->delete($riksa->foto_binluh);
                }

                $riksa->foto_binluh = $request->file("riksa.$index.foto_binluh")
                    ->store('riksa/foto-binluh', 'public');
            }

            if ($request->hasFile("riksa.$index.surat_hasil_pemeriksaan")) {
                if ($riksa->surat_hasil_pemeriksaan) {
                    Storage::disk('public')->delete($riksa->surat_hasil_pemeriksaan);
                }

                $riksa->surat_hasil_pemeriksaan = $request->file("riksa.$index.surat_hasil_pemeriksaan")
                    ->store('riksa/surat-hasil-pemeriksaan', 'public');
            }

            $riksa->save();

            if (! in_array($riksa->id, $incomingIds)) {
                $incomingIds[] = $riksa->id;
            }
        }

        $deleteIds = array_diff($existingIds, $incomingIds);

        if (! empty($deleteIds)) {
            $items = $laporan->riksaKapals()
                ->whereIn('id', $deleteIds)
                ->get();

            foreach ($items as $item) {
                if ($item->foto_riksa) {
                    Storage::disk('public')->delete($item->foto_riksa);
                }

                if ($item->foto_binluh) {
                    Storage::disk('public')->delete($item->foto_binluh);
                }

                if ($item->surat_hasil_pemeriksaan) {
                    Storage::disk('public')->delete($item->surat_hasil_pemeriksaan);
                }

                $item->delete();
            }
        }
    }

    private function syncKronologis(Request $request, AbkLaporan $laporan): void
    {
        foreach ($request->input('kronologi', []) as $item) {

            if (blank($item['uraian'] ?? null)) {
                continue;
            }

            // Sudah ada → update saja
            if (!empty($item['id'])) {

                $kronologi = $laporan->kronologis()
                    ->where('id', $item['id'])
                    ->first();

                if ($kronologi) {
                    $kronologi->update([
                        'uraian' => $item['uraian'],
                    ]);
                }

                continue;
            }

            // Baru → create
            $laporan->kronologis()->create([
                'uraian' => $item['uraian'],
                'waktu_input' => now(),
            ]);
        }
    }

    private function ensureAbkCanAccess(Patroli $patroli): void
    {
        if (auth()->user()->role !== 'abk') {
            abort(403, 'Menu laporan ini hanya untuk ABK Kapal.');
        }

        $isPersonel = $patroli->personels()
            ->where('users.id', auth()->id())
            ->exists();

        if (! $isPersonel) {
            abort(403, 'Anda tidak terdaftar sebagai ABK pada patroli ini.');
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

        abort(403, 'Anda tidak memiliki akses ke laporan ini.');
    }

    private function replaceLampiran(AbkLaporan $laporan, string $jenis, string $path): void
    {
        $old = $laporan->lampirans()
            ->where('jenis', $jenis)
            ->first();

        if ($old && $old->file_path) {
            Storage::disk('public')->delete($old->file_path);
        }

        $laporan->lampirans()->updateOrCreate(
            ['jenis' => $jenis],
            ['file_path' => $path]
        );
    }

    private function markSop14Done(Patroli $patroli): void
    {
        $progress = $patroli->sopProgress()
            ->whereHas('sop', fn ($q) => $q->where('urutan', 14))
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

    private function hitungDurasiPelayaran(Patroli $patroli): string
    {
        $sop7 = $patroli->sopProgress()
            ->whereHas('sop', fn ($q) => $q->where('urutan', 7))
            ->first();

        $sop13 = $patroli->sopProgress()
            ->whereHas('sop', fn ($q) => $q->where('urutan', 13))
            ->first();

        if (! $sop7 || ! $sop7->waktu_selesai || ! $sop13 || ! $sop13->waktu_selesai) {
            return '-';
        }

        $start = Carbon::parse($sop7->waktu_selesai)
            ->timezone(config('app.timezone', 'Asia/Jakarta'));

        $end = Carbon::parse($sop13->waktu_selesai)
            ->timezone(config('app.timezone', 'Asia/Jakarta'));

        return $start->diffForHumans($end, true);
    }

    private function key(string $value): string
    {
        return strtolower(str_replace([' ', '/', '(', ')'], '_', $value));
    }
}