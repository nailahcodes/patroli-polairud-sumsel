@extends('layouts.app')

@section('content')
    @php
        $role = auth()->user()->role;

        $totalTahapan = $patroli->sopProgress->count();
        $selesai = $patroli->sopProgress->where('status', 'selesai')->count();
        $persen = $totalTahapan > 0 ? round(($selesai / $totalTahapan) * 100) : 0;

        $progressList = $patroli->sopProgress->sortBy('sop.urutan');

        $lastDone = $patroli->sopProgress
            ->filter(fn($item) => $item->status === 'selesai')
            ->sortByDesc(fn($item) => $item->sop->urutan ?? 0)
            ->first();

        $nextStep = $patroli->sopProgress
            ->filter(fn($item) => $item->status === 'belum')
            ->sortBy(fn($item) => $item->sop->urutan ?? 0)
            ->first();

        $adminSelesai = $patroli->sopProgress
            ->filter(fn($item) => $item->sop && $item->sop->urutan >= 1 && $item->sop->urutan <= 3)
            ->every(fn($item) => $item->status === 'selesai');

        $sop4Selesai = $patroli->sopProgress
            ->filter(fn($item) => $item->sop && $item->sop->urutan === 4)
            ->every(fn($item) => $item->status === 'selesai');

        $sop14Selesai = $patroli->sopProgress
            ->filter(fn($item) => $item->sop && $item->sop->urutan === 14)
            ->every(fn($item) => $item->status === 'selesai');

        $sop1Sampai15Selesai = $patroli->sopProgress
            ->filter(fn($item) => $item->sop && $item->sop->urutan >= 1 && $item->sop->urutan <= 15)
            ->every(fn($item) => $item->status === 'selesai');

        $sarprasLabels = [
            'kapal' => 'Kapal',
            'ht' => 'HT',
            'rig' => 'RIG',
            'gps' => 'GPS',
            'lampu_sorot' => 'Lampu Sorot',
            'lampu_navigasi' => 'Lampu Navigasi',
            'radar' => 'Radar',
            'kompas' => 'Kompas',
            'echo_sounder' => 'Echo Sounder',
            'life_jacket' => 'Life Jacket',
            'ring_buoy' => 'Ring Buoy',
            'racun_api' => 'Racun Api',
            'kotak_p3k' => 'Kotak P3K',
            'tali_tambang' => 'Tali Tambang',
            'dapra' => 'Dapra',
            'air_tawar' => 'Air Tawar',
            'alat_masak_ransum' => 'Alat Masak/Ransum',
            'senter' => 'Senter',
            'tool_kit' => 'Tool Kit',
            'bbm' => 'BBM',
            'senpi_amunisi' => 'Senpi beserta Amunisi',
            'borgol' => 'Borgol',
            'anggaran' => 'Anggaran',
        ];
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Detail Patroli</h2>
            <p class="text-muted mb-0">
                Monitoring progress SOP untuk kapal {{ $patroli->kapal->kode_kapal ?? '-' }}.
            </p>
        </div>

        <div class="d-flex gap-2">
            @if($role === 'admin')
                <a href="{{ route('patroli.edit', $patroli) }}" class="btn btn-outline-primary rounded-3">
                    Edit
                </a>
            @endif

            <a href="{{ route('patroli.index') }}" class="btn btn-outline-secondary rounded-3">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4">
            <strong>Data belum valid.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($role === 'pimpinan')
        <div class="section-panel mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h5 class="section-title mb-1">Validasi Pimpinan</h5>
                    <p class="text-muted mb-0">
                        Pimpinan dapat memeriksa detail patroli, seluruh bukti SOP, laporan ABK, dan ANEV sebelum melakukan validasi.
                    </p>
                </div>

                <span class="badge-status status-{{ $patroli->status }}">
                    {{ ucfirst($patroli->status) }}
                </span>
            </div>

            @if($patroli->validasi_pimpinan_status)
                <div class="alert alert-info rounded-4 mt-3">
                    <strong>Status Validasi:</strong> {{ ucfirst($patroli->validasi_pimpinan_status) }}<br>
                    <strong>Validator:</strong> {{ $patroli->validatorPimpinan->nama ?? '-' }}<br>
                    <strong>Waktu:</strong> {{ optional($patroli->validasi_pimpinan_at)->format('d/m/Y H:i') ?? '-' }}

                    @if($patroli->validasi_pimpinan_catatan)
                        <hr>
                        <strong>Catatan Perbaikan:</strong><br>
                        {{ $patroli->validasi_pimpinan_catatan }}
                    @endif
                </div>
            @endif

            <div class="row g-3 mt-2">
                <div class="col-lg-6">
                    <div class="bukti-box h-100">
                        <strong class="d-block mb-2">Laporan ABK</strong>

                        @if($patroli->abkLaporan)
                            <p class="text-muted small mb-2">
                                Laporan ABK sudah tersedia di sistem.
                            </p>

                            <a href="{{ route('abk-laporan.export', $patroli) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                Lihat / Export Laporan ABK
                            </a>
                        @else
                            <p class="text-muted mb-0">
                                Laporan ABK belum tersedia.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="bukti-box h-100">
                        <strong class="d-block mb-2">ANEV ABK</strong>

                        @if($patroli->abkAnev)
                            <p class="text-muted small mb-2">
                                ANEV ABK sudah tersedia di sistem.
                            </p>

                            <a href="{{ route('abk-anev.export', $patroli) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                Lihat / Export ANEV
                            </a>
                        @else
                            <p class="text-muted mb-0">
                                ANEV ABK belum tersedia.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            @if(in_array($patroli->status, ['selesai', 'perbaiki', 'valid']))
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <form action="{{ route('pimpinan.validasi.valid', $patroli) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin memvalidasi patroli ini?')">
                            @csrf
                            @method('PATCH')

                            <button class="btn btn-success rounded-3 w-100">
                                Validasi Patroli
                            </button>
                        </form>
                    </div>

                    <div class="col-md-8">
                        <form action="{{ route('pimpinan.validasi.perbaiki', $patroli) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <label class="form-label">Keterangan Jika Tidak Valid / Perlu Perbaikan</label>
                            <textarea
                                name="validasi_pimpinan_catatan"
                                class="form-control mb-2"
                                rows="3"
                                placeholder="Tuliskan bagian yang harus diperbaiki..."
                            >{{ old('validasi_pimpinan_catatan', $patroli->validasi_pimpinan_catatan) }}</textarea>

                            <button class="btn btn-warning rounded-3">
                                Tandai Perlu Perbaikan
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-warning rounded-4 mt-3 mb-0">
                    Patroli belum bisa divalidasi karena statusnya belum selesai.
                </div>
            @endif
        </div>
    @endif

    <div class="detail-grid mb-4">
        <div class="section-panel">
            <h5 class="section-title mb-3">Identitas Patroli</h5>

            <div class="detail-info-list">
                <div class="detail-info-item">
                    <span>No. Sprin</span>
                    <strong>{{ $patroli->nomor_sprin ?? '-' }}</strong>
                </div>

                <div class="detail-info-item">
                    <span>Kapal</span>
                    <strong>{{ $patroli->kapal->kode_kapal ?? '-' }}</strong>
                </div>

                <div class="detail-info-item">
                    <span>Komandan Kapal</span>

                    @if($patroli->kapal && $patroli->kapal->komandan)
                        <strong>{{ $patroli->kapal->komandan->nama }}</strong>
                        <small>
                            {{ $patroli->kapal->komandan->pangkat ?? '-' }}
                            —
                            NRP: {{ $patroli->kapal->komandan->nrp }}
                        </small>
                    @else
                        <strong>{{ $patroli->kapal->komandan_kapal ?? '-' }}</strong>
                    @endif
                </div>

                <div class="detail-info-item">
                    <span>Wilayah Patroli</span>
                    <strong>{{ $patroli->wilayah_patroli }}</strong>
                </div>

                <div class="detail-info-item">
                    <span>Tanggal Persiapan</span>
                    <strong>{{ \Carbon\Carbon::parse($patroli->tanggal_persiapan)->format('d/m/Y') }}</strong>
                </div>

                <div class="detail-info-item">
                    <span>Periode Patroli</span>
                    <strong>
                        {{ \Carbon\Carbon::parse($patroli->tanggal_mulai)->format('d/m/Y') }}
                        -
                        {{ \Carbon\Carbon::parse($patroli->tanggal_selesai)->format('d/m/Y') }}
                    </strong>
                </div>

                <div class="detail-info-item">
                    <span>Status Patroli</span>
                    <strong>
                        <span class="badge-status status-{{ $patroli->status }}">
                            {{ ucfirst($patroli->status) }}
                        </span>
                    </strong>
                </div>
            </div>
        </div>

        <div class="section-panel">
            <h5 class="section-title mb-3">Progress SOP</h5>

            <div class="big-progress mb-3">
                <div class="big-progress-number">{{ $persen }}%</div>

                <div class="flex-grow-1">
                    <div class="progress progress-soft mb-2">
                        <div class="progress-bar" style="width: {{ $persen }}%;">
                            {{ $persen }}%
                        </div>
                    </div>

                    <p class="text-muted mb-0">
                        {{ $selesai }} dari {{ $totalTahapan }} tahapan SOP telah selesai.
                    </p>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="mini-summary">
                        <strong>{{ $patroli->sopProgress->where('status', 'belum')->count() }}</strong>
                        <span>Belum</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mini-summary">
                        <strong>{{ $patroli->sopProgress->where('status', 'selesai')->count() }}</strong>
                        <span>Selesai</span>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="stage-card done">
                        <small>Tahapan Terakhir Selesai</small>

                        @if($lastDone)
                            <h5>Tahap {{ $lastDone->sop->urutan }}</h5>
                            <p>{{ $lastDone->sop->tahapan }}</p>
                        @else
                            <h5>Belum ada</h5>
                            <p>Belum ada tahapan yang selesai.</p>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="stage-card next">
                        <small>Tahapan Berikutnya</small>

                        @if($nextStep)
                            <h5>Tahap {{ $nextStep->sop->urutan }}</h5>
                            <p>{{ $nextStep->sop->tahapan }}</p>
                        @else
                            <h5>Selesai</h5>
                            <p>Seluruh tahapan SOP telah selesai.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <div class="section-panel mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="section-title mb-0">Personel ABK Kapal</h5>
                <small class="text-muted">Personel yang ditugaskan dalam patroli ini.</small>
            </div>

            <span class="badge-status status-berjalan">
                {{ $patroli->personels->count() }} Personel
            </span>
        </div>

        <div class="row g-3">
            @forelse($patroli->personels as $personel)
                <div class="col-md-4">
                    <div class="personel-card">
                        @if($personel->profile_photo_url)
                            <img src="{{ $personel->profile_photo_url }}" class="personel-avatar-img" alt="Foto Profil">
                        @else
                            <div class="personel-avatar">
                                {{ strtoupper(substr($personel->nama, 0, 1)) }}
                            </div>
                        @endif

                        <div>
                            <strong>{{ $personel->nama }}</strong><br>
                            <small class="text-muted">
                                {{ $personel->pangkat ?? '-' }} — NRP: {{ $personel->nrp }}
                            </small><br>
                            <span class="small text-muted">
                                {{ $personel->pivot->posisi ?? 'ABK Kapal' }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning rounded-4 mb-0">
                        Belum ada personel ABK yang dipilih untuk patroli ini.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="section-panel">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h5 class="section-title mb-1">Daftar Tahapan SOP</h5>

                @if($role === 'admin')
                    <p class="text-muted mb-0">
                        Anda dapat melihat seluruh SOP dan mengerjakan checklist SOP nomor 1 sampai 3.
                    </p>
                @elseif($role === 'komandan')
                    <p class="text-muted mb-0">
                        Anda dapat melihat seluruh SOP, tetapi hanya dapat mengerjakan checklist SOP nomor 4 sampai 13.
                    </p>
                @elseif($role === 'abk')
                    <p class="text-muted mb-0">
                        Anda dapat melihat seluruh SOP. SOP 14 untuk laporan ABK, SOP 15 untuk ANEV, dan SOP 16 untuk arsip fisik.
                    </p>
                @else
                    <p class="text-muted mb-0">
                        Anda dapat melihat seluruh tahapan SOP tanpa mengubah checklist.
                    </p>
                @endif
            </div>

            <span class="badge-status status-{{ $patroli->status }}">
                {{ ucfirst($patroli->status) }}
            </span>
        </div>

        <div class="alert alert-info rounded-4 mb-4">
            <strong>Alur checklist SOP:</strong><br>
            Admin menyelesaikan SOP 1-3 terlebih dahulu. Setelah itu Komandan Kapal dapat melanjutkan SOP 4-13.
            SOP 4 wajib menyertakan scan PDF Surat Perintah dan Berita Acara. Setelah SOP 4 selesai, ABK dapat mulai mengisi SOP 14.
            SOP 15 dapat diisi setelah SOP 14 selesai. SOP 16 khusus checklist arsip fisik setelah SOP 1-15 selesai.
        </div>

        <div class="sop-card-list">
            @forelse($progressList as $progress)
                @php
                    $urutan = $progress->sop->urutan ?? 0;

                    $bolehUpdate =
                        ($role === 'admin' && $urutan >= 1 && $urutan <= 3) ||
                        ($role === 'komandan' && $urutan >= 4 && $urutan <= 13 && $adminSelesai) ||
                        ($role === 'abk' && $urutan === 14) ||
                        ($role === 'abk' && $urutan === 15 && $sop14Selesai) ||
                        ($role === 'abk' && $urutan === 16 && $sop1Sampai15Selesai);

                    $terkunciKomandan =
                        $role === 'komandan' &&
                        $urutan >= 4 &&
                        $urutan <= 13 &&
                        ! $adminSelesai;

                    $terkunciKomandanSop5Plus =
                        $role === 'komandan' &&
                        $urutan >= 5 &&
                        $urutan <= 13 &&
                        $adminSelesai &&
                        ! $sop4Selesai;

                    $terkunciAbk =
                        $role === 'abk' &&
                        (
                            ($urutan === 15 && ! $sop14Selesai) ||
                            ($urutan === 16 && ! $sop1Sampai15Selesai)
                        );

                    $checkedSarpras = $progress->checklist_sarpras ?? [];

                    $bukanHakRole =
                        ($role === 'admin' && !($urutan >= 1 && $urutan <= 3)) ||
                        ($role === 'komandan' && !($urutan >= 4 && $urutan <= 13)) ||
                        ($role === 'abk' && !($urutan >= 14 && $urutan <= 16)) ||
                        ($role === 'pimpinan');
                @endphp

                <div class="sop-detail-card {{ $bukanHakRole ? 'sop-readonly-card' : '' }}">
                    <div class="sop-detail-main">
                        <div class="sop-number-lg">
                            {{ $progress->sop->urutan ?? '-' }}
                        </div>

                        <div class="sop-detail-text">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <h5 class="mb-0">
                                    {{ $progress->sop->tahapan ?? '-' }}
                                </h5>

                                <span class="badge-status status-{{ $progress->status }}">
                                    {{ ucfirst($progress->status) }}
                                </span>
                            </div>

                            <div class="sop-meta-grid">
                                <div>
                                    <span>Pelaksana</span>
                                    <strong>{{ $progress->sop->pelaksana ?? '-' }}</strong>
                                </div>

                                <div>
                                    <span>Waktu Standar</span>
                                    <strong>{{ $progress->sop->waktu ?? '-' }}</strong>
                                </div>

                                <div>
                                    <span>Output</span>
                                    <strong>{{ $progress->sop->output ?? '-' }}</strong>
                                </div>
                            </div>

                            @if($progress->sop->kelengkapan)
                                <div class="sop-description mt-3">
                                    <span>Kelengkapan</span>
                                    <p>{{ $progress->sop->kelengkapan }}</p>
                                </div>
                            @endif

                            @if($progress->catatan)
                                <div class="sop-description mt-2">
                                    <span>Catatan</span>
                                    <p>{{ $progress->catatan }}</p>
                                </div>
                            @endif

                            @if($progress->waktu_selesai)
                                <div class="small text-muted mt-2">
                                    Selesai pada:
                                    {{ \Carbon\Carbon::parse($progress->waktu_selesai)->format('d/m/Y H:i') }}
                                </div>
                            @endif

                            @if($progress->bukti_file_url)
                                <a href="{{ $progress->bukti_file_url }}" target="_blank" class="small d-inline-block mt-2">
                                    @if($urutan === 4)
                                        Lihat Scan Surat Perintah
                                    @elseif($urutan === 6)
                                        Lihat Bukti APP
                                    @else
                                        Lihat Bukti
                                    @endif
                                </a>
                            @endif

                            @if($progress->bukti_file_2_url)
                                <a href="{{ $progress->bukti_file_2_url }}" target="_blank" class="small d-inline-block mt-2 ms-2">
                                    Lihat Berita Acara
                                </a>
                            @endif

                            @if($progress->air_tawar_file_url)
                                <a href="{{ $progress->air_tawar_file_url }}" target="_blank" class="small d-inline-block mt-2 ms-2">
                                    Lihat Foto Air Tawar
                                </a>
                            @endif

                            @if($urutan === 14 && $patroli->abkLaporan)

                                @php
                                    $lampirans = $patroli->abkLaporan->lampirans ?? collect();
                                @endphp

                                @foreach($lampirans->where('jenis', 'absensi_personel') as $file)
                                    <a href="{{ asset('storage/'.$file->file_path) }}"
                                    target="_blank"
                                    class="d-block small mt-2">
                                        Lihat Absensi Personel
                                    </a>
                                @endforeach

                                @foreach($lampirans->where('jenis', 'daftar_nama_personel') as $file)
                                    <a href="{{ asset('storage/'.$file->file_path) }}"
                                    target="_blank"
                                    class="d-block small mt-2">
                                        Lihat Daftar Personel
                                    </a>
                                @endforeach

                                @foreach($lampirans->where('jenis', 'berita_acara_penyerahan_materil') as $file)
                                    <a href="{{ asset('storage/'.$file->file_path) }}"
                                    target="_blank"
                                    class="d-block small mt-2">
                                        Lihat Berita Acara Penyerahan Materil
                                    </a>
                                @endforeach

                            @endif
                        </div>
                    </div>

                    <div class="sop-action-panel">
                        @if($bolehUpdate)
                            @if($role === 'abk' && $urutan === 14)
                                <a href="{{ route('abk-laporan.edit', $patroli) }}" class="btn btn-polairud w-100">
                                    Isi Laporan SOP 14
                                </a>

                                @if($progress->status === 'selesai')
                                    <div class="small text-success fw-bold mt-2">
                                        Laporan sudah tersimpan. SOP 14 selesai.
                                    </div>
                                @else
                                    <div class="small text-muted mt-2">
                                        SOP 14 akan otomatis selesai setelah laporan ABK disimpan.
                                    </div>
                                @endif

                            @elseif($role === 'abk' && $urutan === 15)
                                <a href="{{ route('abk-anev.edit', $patroli) }}" class="btn btn-polairud w-100">
                                    Isi ANEV SOP 15
                                </a>

                                @if($progress->status === 'selesai')
                                    <div class="small text-success fw-bold mt-2">
                                        ANEV sudah tersimpan. SOP 15 selesai.
                                    </div>
                                @else
                                    <div class="small text-muted mt-2">
                                        SOP 15 akan otomatis selesai setelah ANEV disimpan.
                                    </div>
                                @endif

                            @elseif($role === 'abk' && $urutan === 16)
                                <form action="{{ route('sop-progress.update', $progress->id) }}" method="POST">
                                    @csrf

                                    <label class="sop-check-card">
                                        <input
                                            type="checkbox"
                                            name="selesai"
                                            value="1"
                                            onchange="this.form.submit()"
                                            {{ $progress->status === 'selesai' ? 'checked' : '' }}
                                        >

                                        <span>
                                            {{ $progress->status === 'selesai' ? 'Sudah masuk ke arsip fisik' : 'Centang jika sudah masuk ke arsip fisik' }}
                                        </span>
                                    </label>

                                    <small class="text-muted d-block mt-2">
                                        Laporan ABK dan ANEV sudah tersimpan di sistem. Checklist ini khusus untuk arsip dokumen fisik.
                                    </small>
                                </form>

                            @elseif($role === 'komandan' && $urutan === 4)
                                <form action="{{ route('sop-progress.update', $progress->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="bukti-box">
                                        <strong class="d-block mb-2">Upload Berkas SOP 4</strong>

                                        @if($progress->bukti_file_url)
                                            <a href="{{ $progress->bukti_file_url }}" target="_blank" class="d-block mb-2">
                                                Lihat scan Surat Perintah yang sudah diupload
                                            </a>
                                        @endif

                                        @if($progress->bukti_file_2_url)
                                            <a href="{{ $progress->bukti_file_2_url }}" target="_blank" class="d-block mb-2">
                                                Lihat Berita Acara yang sudah diupload
                                            </a>
                                        @endif

                                        <label class="form-label small fw-bold">Scan Surat Perintah</label>
                                        <input
                                            type="file"
                                            name="bukti_file"
                                            class="form-control form-control-sm mb-2"
                                            accept=".pdf,application/pdf"
                                            required
                                        >

                                        <label class="form-label small fw-bold">Berita Acara</label>
                                        <input
                                            type="file"
                                            name="bukti_file_2"
                                            class="form-control form-control-sm"
                                            accept=".pdf,application/pdf"
                                            required
                                        >

                                        <small class="text-muted d-block mt-2">
                                            Format PDF. Masing-masing maksimal 5MB.
                                        </small>

                                        <button class="btn btn-sm btn-polairud w-100 mt-3">
                                            Upload dan Selesaikan SOP 4
                                        </button>

                                        @if($progress->status === 'selesai')
                                            <div class="small text-success fw-bold mt-2">
                                                Surat Perintah dan Berita Acara sudah diupload. SOP 4 selesai.
                                            </div>
                                        @endif
                                    </div>
                                </form>

                            @elseif($role === 'komandan' && $urutan === 5)
                                <form action="{{ route('sop-progress.update', $progress->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="sarpras-box">
                                        <strong class="d-block mb-2">Checklist Sarana & Prasarana</strong>

                                        <div class="sarpras-check-grid">
                                            @foreach($sarprasLabels as $key => $label)
                                                <label class="mini-check">
                                                    <input
                                                        type="checkbox"
                                                        name="sarpras[{{ $key }}]"
                                                        value="1"
                                                        {{ in_array($key, $checkedSarpras) ? 'checked' : '' }}
                                                    >
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        @if($progress->air_tawar_file_url)
                                            <a href="{{ $progress->air_tawar_file_url }}" target="_blank" class="d-block small mt-3">
                                                Lihat bukti foto pengisian air tawar
                                            </a>
                                        @endif
                                        
                                        

                                        <label class="form-label small fw-bold mt-3">
                                            Bukti Foto Pengisian Air Tawar
                                        </label>
                                        <input
                                            type="file"
                                            name="air_tawar_file"
                                            class="form-control form-control-sm"
                                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                            required
                                        >

                                        <small class="text-muted d-block mt-2">
                                            Format JPG, JPEG, PNG. Maksimal 5MB.
                                        </small>

                                        <button class="btn btn-sm btn-polairud w-100 mt-3">
                                            Simpan Checklist SOP 5
                                        </button>

                                        @if($progress->status === 'selesai')
                                            <div class="small text-success fw-bold mt-2">
                                                Sarpras lengkap dan bukti air tawar sudah diupload. SOP 5 selesai.
                                            </div>
                                        @else
                                            <div class="small text-muted mt-2">
                                                Semua item wajib dicentang dan bukti air tawar wajib diupload agar SOP 5 selesai.
                                            </div>
                                        @endif
                                    </div>
                                </form>

                            @elseif($role === 'komandan' && $urutan === 6)
                                <form action="{{ route('sop-progress.update', $progress->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="bukti-box">
                                        <strong class="d-block mb-2">Upload Bukti APP</strong>

                                        @if($progress->bukti_file_url)
                                            <a href="{{ $progress->bukti_file_url }}" target="_blank" class="d-block mb-2">
                                                Lihat bukti yang sudah diupload
                                            </a>
                                        @endif

                                        <input
                                            type="file"
                                            name="bukti_file"
                                            class="form-control form-control-sm"
                                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                            required
                                        >

                                        <small class="text-muted d-block mt-2">
                                            Format JPG, JPEG, PNG. Maksimal 5MB.
                                        </small>

                                        <button class="btn btn-sm btn-polairud w-100 mt-3">
                                            Upload dan Selesaikan SOP 6
                                        </button>
                                    </div>
                                </form>

                            @elseif($role === 'komandan' && $urutan === 11)
                                @php
                                    $tindakPidanaList = $patroli->abkLaporan?->riksaKapals
                                        ? $patroli->abkLaporan->riksaKapals->where('kategori', 'tindak_pidana')
                                        : collect();
                                @endphp

                                <form action="{{ route('sop-progress.update', $progress->id) }}" method="POST">
                                    @csrf

                                    <div class="bukti-box">
                                        <strong class="d-block mb-2">Gelar Perkara</strong>

                                        @if($tindakPidanaList->count() > 0)
                                            <div class="small text-muted mb-2">
                                                Kapal terindikasi tindak pidana:
                                            </div>

                                            <ul class="small mb-3">
                                                @foreach($tindakPidanaList as $riksa)
                                                    <li>
                                                        <strong>{{ $riksa->nama_kapal ?? '-' }}</strong>
                                                        —
                                                        Nahkoda: {{ $riksa->nama_nahkoda ?? '-' }}
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <label class="sop-check-card">
                                                <input
                                                    type="checkbox"
                                                    name="selesai"
                                                    value="1"
                                                    {{ $progress->status === 'selesai' ? 'checked' : '' }}
                                                >
                                                <span>Centang jika gelar perkara sudah dilaksanakan</span>
                                            </label>
                                        @else
                                            <label class="sop-check-card">
                                                <input
                                                    type="checkbox"
                                                    name="nihil_gelar_perkara"
                                                    value="1"
                                                    {{ $progress->nihil_gelar_perkara ? 'checked' : '' }}
                                                >
                                                <span>Nihil tindak pidana / semua riksa aman</span>
                                            </label>
                                        @endif

                                        <button class="btn btn-sm btn-polairud w-100 mt-3">
                                            Simpan SOP 11
                                        </button>
                                    </div>
                                </form>

                            @else
                                <form action="{{ route('sop-progress.update', $progress->id) }}" method="POST">
                                    @csrf

                                    <label class="sop-check-card">
                                        <input
                                            type="checkbox"
                                            name="selesai"
                                            value="1"
                                            onchange="this.form.submit()"
                                            {{ $progress->status === 'selesai' ? 'checked' : '' }}
                                        >

                                        <span>
                                            {{ $progress->status === 'selesai' ? 'Sudah selesai' : 'Centang jika sudah' }}
                                        </span>
                                    </label>
                                </form>
                            @endif
                        @else
                            @if($terkunciKomandan)
                                <div class="sop-locked-card">
                                    🔒 Menunggu Admin menyelesaikan SOP 1-3.
                                </div>
                            @elseif($terkunciKomandanSop5Plus)
                                <div class="sop-locked-card">
                                    🔒 Menunggu Komandan menyelesaikan SOP 4 dengan upload scan Surat Perintah dan Berita Acara.
                                </div>
                            @elseif($terkunciAbk)
                                <div class="sop-locked-card">
                                    @if($urutan === 14)
                                        SOP 14 siap diisi.
                                    @elseif($urutan === 15)
                                        🔒 Menunggu ABK menyelesaikan Laporan SOP 14.
                                    @elseif($urutan === 16)
                                        🔒 Menunggu SOP 1-15 selesai.
                                    @else
                                        🔒 Menunggu tahapan sebelumnya selesai.
                                    @endif
                                </div>
                            @else
                                <div class="readonly-status-card">
                                    <strong>
                                        {{ $progress->status === 'selesai' ? 'Sudah selesai' : 'Belum selesai' }}
                                    </strong>

                                    @if($role === 'admin' && !($urutan >= 1 && $urutan <= 3))
                                        <small class="d-block mt-1">
                                            SOP ini bukan bagian checklist Admin.
                                        </small>
                                    @elseif($role === 'komandan' && !($urutan >= 4 && $urutan <= 13))
                                        <small class="d-block mt-1">
                                            SOP ini bukan bagian checklist Komandan Kapal.
                                        </small>
                                    @elseif($role === 'abk' && !($urutan >= 14 && $urutan <= 16))
                                        <small class="d-block mt-1">
                                            SOP ini bukan bagian checklist ABK Kapal.
                                        </small>
                                    @elseif($role === 'pimpinan')
                                        <small class="d-block mt-1">
                                            Pimpinan hanya dapat memantau progress.
                                        </small>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="alert alert-warning rounded-4 mb-0">
                    Tidak ada tahapan SOP untuk patroli ini.
                </div>
            @endforelse
        </div>
    </div>
@endsection