<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan ABK</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #111827;
        }

        h3, h4 {
            margin: 0 0 8px;
        }

        .text-center {
            text-align: center;
        }

        .section {
            margin-top: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #111827;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #e5e7eb;
        }

        .no-border td,
        .no-border th {
            border: none;
        }

        .page-break {
            page-break-before: always;
        }

        .image-box {
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }

        .doc-image {
            max-width: 100%;
            max-height: 430px;
            object-fit: contain;
            border: 1px solid #d1d5db;
            padding: 4px;
        }

        .small {
            font-size: 10px;
        }

        ol {
            padding-left: 18px;
        }

        li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
@php
    use Carbon\Carbon;

    $tz = $timezone ?? config('app.timezone', 'Asia/Jakarta');

    Carbon::setLocale('id');

    $sop = $patroli->sopProgress->keyBy(fn($item) => $item->sop->urutan ?? 0);

    $komandan = $patroli->kapal->komandan ?? null;
    $totalAnggaran = $laporan->anggarans->sum('nominal');

    $riksa = $laporan->riksaKapals;
    $totalTindakPidana = $riksa->where('kategori', 'tindak_pidana')->count();
    $totalPelanggaran = $riksa->where('kategori', 'pelanggaran')->count();

    $koordinatBertolak = optional($laporan->koordinats->where('jenis', 'bertolak')->first())->koordinat;
    $koordinatBersandar = optional($laporan->koordinats->where('jenis', 'bersandar')->first())->koordinat;

    $fmtJam = function ($date) use ($tz) {
        if (! $date) {
            return '-';
        }

        return Carbon::parse($date)->timezone($tz)->format('H:i');
    };

    $fmtTanggal = function ($date) use ($tz) {
        if (! $date) {
            return '-';
        }

        return Carbon::parse($date)->timezone($tz)->translatedFormat('d F Y');
    };

    $fmtTanggalKapital = function ($date) use ($tz) {
        if (! $date) {
            return '-';
        }

        return mb_strtoupper(Carbon::parse($date)->timezone($tz)->translatedFormat('d F Y'), 'UTF-8');
    };

    $imgPath = function ($path) {
        if (! $path) {
            return null;
        }

        $full = public_path('storage/' . $path);

        return file_exists($full) ? $full : null;
    };

    $lampiranByJenis = $laporan->lampirans->keyBy('jenis');
@endphp

<h3 class="text-center">LAPORAN HASIL PELAKSANAAN PATROLI</h3>
<h4 class="text-center">KAPAL POLISI {{ $patroli->kapal->kode_kapal ?? '-' }}</h4>

<div class="section">
    <strong>a. Identitas Patroli</strong>

    <table>
        <tr>
            <td width="35%">Nomor Surat Perintah</td>
            <td>{{ $patroli->nomor_sprin ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kapal</td>
            <td>{{ $patroli->kapal->kode_kapal ?? '-' }}</td>
        </tr>
        <tr>
            <td>Wilayah Patroli</td>
            <td>{{ $patroli->wilayah_patroli ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Patroli</td>
            <td>
                {{ $fmtTanggal($patroli->tanggal_mulai) }}
                -
                {{ $fmtTanggal($patroli->tanggal_selesai) }}
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <strong>b. Personel</strong>

    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Nama</th>
                <th>Pangkat</th>
                <th>NRP</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $komandan->nama ?? '-' }}</td>
                <td>{{ $komandan->pangkat ?? '-' }}</td>
                <td>{{ $komandan->nrp ?? '-' }}</td>
                <td>Komandan Kapal</td>
            </tr>

            @foreach($patroli->personels as $personel)
                <tr>
                    <td>{{ $loop->iteration + 1 }}</td>
                    <td>{{ $personel->nama }}</td>
                    <td>{{ $personel->pangkat ?? '-' }}</td>
                    <td>{{ $personel->nrp ?? '-' }}</td>
                    <td>{{ $personel->pivot->posisi ?? 'ABK Kapal' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <strong>c. Rencana Anggaran</strong>

    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Komponen</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan->anggarans as $anggaran)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $anggaran->komponen }}</td>
                    <td>Rp {{ number_format($anggaran->nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <tr>
                <th colspan="2">TOTAL</th>
                <th>Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</th>
            </tr>
        </tbody>
    </table>
</div>

<div class="section">
    <strong>d. Rencana Logistik / BBM / Oli</strong>

    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Jenis</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan->logistiks as $logistik)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $logistik->jenis }}</td>
                    <td>{{ $logistik->jumlah_liter }} Liter</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <strong>e. Kronologi Kegiatan Patroli</strong>

    @php

        $timeline = collect();

        // Kronologi manual
        foreach ($laporan->kronologis as $kronologi) {

            $timeline->push([
                'jenis' => 'kronologi',
                'waktu' => $kronologi->waktu_input,
                'data' => $kronologi,
            ]);
        }

        // Pemeriksaan kapal / tindak pidana
        foreach ($laporan->riksaKapals as $riksa) {

            $timeline->push([
                'jenis' => 'riksa',
                'waktu' => $riksa->waktu_kejadian ?? $riksa->created_at,
                'data' => $riksa,
            ]);
        }

        $timeline = $timeline
            ->sortBy('waktu')
            ->groupBy(function ($item) {

                return \Carbon\Carbon::parse($item['waktu'])
                    ->format('Y-m-d');

            });

    @endphp

    @foreach($timeline as $tanggal => $items)

        <p>
            <strong>
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
            </strong>
        </p>

        @foreach($items as $entry)

            <p>
                <strong>
                    {{ \Carbon\Carbon::parse($entry['waktu'])->format('H:i') }} WIB
                </strong>
            </p>

            @if($entry['jenis'] === 'kronologi')

                <p>
                    {{ $entry['data']->uraian }}
                </p>

            @else

                @php
                    $riksa = $entry['data'];
                @endphp

                <p>
                    Kapal Polisi
                    {{ $patroli->kapal->kode_kapal ?? '-' }}
                    melaksanakan pemeriksaan kapal pada posisi
                    {{ $riksa->titik_koordinat ?? '-' }}
                    dengan data:
                </p>

                <div style="margin-left:20px">

                    Nama Kapal:
                    {{ $riksa->nama_kapal ?? '-' }}
                    <br>

                    Nama Nahkoda:
                    {{ $riksa->nama_nahkoda ?? '-' }}
                    <br>

                    Dari/Tujuan:
                    {{ $riksa->dari_tujuan ?? '-' }}
                    <br>

                    Muatan:
                    {{ $riksa->muatan ?? '-' }}
                    <br>

                    Keterangan:
                    {{ ucfirst(str_replace('_', ' ', $riksa->kategori)) }}
                    <br>

                    Penjelasan:
                    {{ $riksa->penjelasan ?? '-' }}

                </div>

            @endif

        @endforeach

        <br>

    @endforeach

</div>

<div class="section">
    <strong>f. Hasil yang Dicapai</strong>

    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Uraian</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Total Pemeriksaan Kapal</td>
                <td>{{ $riksa->count() }} kali</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Total Tindak Pidana</td>
                <td>{{ $totalTindakPidana }} kasus</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Total Pelanggaran Pelayaran</td>
                <td>{{ $totalPelanggaran }} kasus</td>
            </tr>
            <tr>
                <td>4</td>
                <td>Total Pengisian BBM</td>
                <td>{{ $laporan->total_pengisian_bbm }} Liter</td>
            </tr>
            <tr>
                <td>5</td>
                <td>Total Stok BBM di Tangki</td>
                <td>{{ $laporan->total_stock_bbm_tangki }} Liter</td>
            </tr>
            <tr>
                <td>6</td>
                <td>Total Lamanya Pelayaran</td>
                <td>{{ $durasiPelayaran }}</td>
            </tr>
            <tr>
                <td>7</td>
                <td>Total Jarak Tempuh</td>
                <td>{{ $laporan->total_jarak_tempuh }} NM / nmil</td>
            </tr>
            <tr>
                <td>8</td>
                <td>Total Pemakaian BBM</td>
                <td>{{ $laporan->total_pemakaian_bbm }} Liter</td>
            </tr>
            <tr>
                <td>9</td>
                <td>Pemakaian BBM Selama Layar</td>
                <td>{{ $laporan->pemakaian_bbm_selama_layar }} Liter</td>
            </tr>
            <tr>
                <td>10</td>
                <td>Kecepatan Rata-rata</td>
                <td>{{ $laporan->kecepatan_rata_rata }} NM / nmil</td>
            </tr>
            <tr>
                <td>11</td>
                <td>Sisa BBM Selesai Patroli</td>
                <td>{{ $laporan->sisa_bbm_selesai_patroli }} Liter</td>
            </tr>
        </tbody>
    </table>
</div>

<br><br>

<table class="no-border">
    <tr>
        <td width="55%"></td>
        <td class="text-center">
            PALEMBANG, {{ $fmtTanggalKapital(now()) }}<br>
            KOMANDAN KAPAL POLISI {{ $patroli->kapal->kode_kapal ?? '-' }}<br><br><br><br>
            <strong>{{ $komandan->nama ?? '-' }}</strong><br>
            NRP {{ $komandan->nrp ?? '-' }}
        </td>
    </tr>
</table>

<div class="page-break"></div>

<h3 class="text-center">LAMPIRAN DOKUMENTASI RIKSA KAPAL</h3>

@forelse($laporan->riksaKapals as $item)
    <div class="{{ $loop->first ? '' : 'page-break' }}">
        <h4>Riksa Kapal {{ $loop->iteration }}</h4>

        <table>
            <tr>
                <td width="35%">Nama Kapal</td>
                <td>{{ $item->nama_kapal ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nahkoda</td>
                <td>{{ $item->nama_nahkoda ?? '-' }}</td>
            </tr>
            <tr>
                <td>Dari/Tujuan</td>
                <td>{{ $item->dari_tujuan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Muatan</td>
                <td>{{ $item->muatan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Titik Koordinat</td>
                <td>{{ $item->titik_koordinat ?? '-' }}</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>{{ ucfirst(str_replace('_', ' ', $item->kategori)) }}</td>
            </tr>

            <tr>
                <td>Waktu Kejadian</td>
                <td>
                    {{ $item->waktu_kejadian
                        ? \Carbon\Carbon::parse($item->waktu_kejadian)->format('H:i') . ' WIB'
                        : '-' }}
                </td>
            </tr>

            <tr>
                <td>Penjelasan</td>
                <td>{{ $item->penjelasan ?? '-' }}</td>
            </tr>
        </table>

        @if($imgPath($item->foto_riksa))
            <div class="image-box">
                <strong>Foto Riksa Kapal</strong><br>
                <img src="{{ $imgPath($item->foto_riksa) }}" class="doc-image">
            </div>
        @endif

        @if($imgPath($item->foto_binluh))
            <div class="image-box">
                <strong>Foto Binluh</strong><br>
                <img src="{{ $imgPath($item->foto_binluh) }}" class="doc-image">
            </div>
        @endif

        @if($item->surat_hasil_pemeriksaan)
            <p class="small">
                Dokumen PDF Surat Hasil Pemeriksaan Kapal digabungkan setelah halaman laporan utama.
            </p>
        @endif
    </div>
@empty
    <p>Belum ada dokumentasi riksa kapal.</p>
@endforelse

<div class="page-break"></div>

<h3 class="text-center">LAMPIRAN DOKUMEN DAN FOTO PENDUKUNG</h3>

<table>
    <thead>
        <tr>
            <th width="8%">No</th>
            <th>Jenis Lampiran</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($laporan->lampirans as $lampiran)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $lampiran->jenis)) }}</td>
                <td>
                    @if(strtolower(pathinfo($lampiran->file_path, PATHINFO_EXTENSION)) === 'pdf')
                        Isi dokumen PDF digabungkan setelah halaman laporan utama.
                    @else
                        Foto/dokumen tercetak pada halaman laporan utama jika format didukung.
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td>1</td>
                <td>-</td>
                <td>Belum ada lampiran tambahan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@if(isset($lampiranByJenis['foto_pengisian_air_tawar']) && $imgPath($lampiranByJenis['foto_pengisian_air_tawar']->file_path))
    <div class="section">
        <strong>Dokumentasi Foto Pengisian Air Tawar</strong>

        <div class="image-box">
            <img src="{{ $imgPath($lampiranByJenis['foto_pengisian_air_tawar']->file_path) }}" class="doc-image">
        </div>
    </div>
@endif

</body>
</html>