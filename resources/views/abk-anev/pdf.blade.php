<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ANEV ABK</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #111827;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .section {
            margin-top: 18px;
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

        .no-border td {
            border: none;
        }

        .signature-space {
            height: 70px;
        }

        .page-break {
            page-break-before: always;
        }

        .anev-photo {
            width: 100%;
            max-height: 520px;
            object-fit: contain;
            margin-top: 14px;
        }

        .pre-line {
            white-space: pre-line;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
@php
    use Carbon\Carbon;

    $tz = config('app.timezone', 'Asia/Jakarta');

    Carbon::setLocale('id');

    $riksaKapals = $patroli->abkLaporan?->riksaKapals ?? collect();
    $pembuat = $anev->pembuatLaporan;

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

    $jabatanPembuat = $pembuat->jabatan ?? 'Pembuat Laporan';
    $kodeKapal = $patroli->kapal->kode_kapal ?? '-';
@endphp

<h3>ANEV KEGIATAN PATROLI PERAIRAN</h3>

<div class="section">
    <strong>a. Identitas Patroli</strong>

    <table>
        <tr>
            <td width="35%">Kapal</td>
            <td>{{ $kodeKapal }}</td>
        </tr>
        <tr>
            <td>Wilayah Patroli</td>
            <td>{{ $patroli->wilayah_patroli ?? '-' }}</td>
        </tr>
        <tr>
            <td>Periode Patroli</td>
            <td>
                {{ $patroli->tanggal_mulai ? Carbon::parse($patroli->tanggal_mulai)->translatedFormat('d F Y') : '-' }}
                -
                {{ $patroli->tanggal_selesai ? Carbon::parse($patroli->tanggal_selesai)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <strong>b. Hasil yang Dicapai</strong>

    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Nama Kapal</th>
                <th>Dari/Tujuan</th>
                <th>Muatan</th>
                <th>Tindak Pidana</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riksaKapals as $riksa)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $riksa->nama_kapal ?? '-' }}</td>
                    <td>{{ $riksa->dari_tujuan ?? '-' }}</td>
                    <td>{{ $riksa->muatan ?? '-' }}</td>
                    <td>
                        @if($riksa->kategori === 'tindak_pidana')
                            Ya
                        @else
                            Tidak
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td>1</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="section">
    <strong>c. Hambatan dan Kendala</strong>

    <p>
        1. Hambatan yang dihadapi berupa:
    </p>

    <div class="pre-line">
        {{ $anev->hambatan ?: 'a) -' }}
    </div>

    <p>
        2. Kendala yang dihadapi berupa:
    </p>

    <div class="pre-line">
        {{ $anev->kendala ?: 'a) -' }}
    </div>
</div>

<br><br>

<table class="no-border">
    <tr>
        <td width="55%"></td>
        <td class="text-center">
            PALEMBANG, {{ $fmtTanggalKapital(now()) }}<br>
            {{ strtoupper($jabatanPembuat) }} {{ strtoupper($kodeKapal) }}<br>

            <div class="signature-space"></div>

            <strong>{{ $pembuat->nama ?? '-' }}</strong><br>
            NRP {{ $pembuat->nrp ?? '-' }}
        </td>
    </tr>
</table>

@if($anev->foto_anev && $imgPath($anev->foto_anev))
    <div class="page-break"></div>

    <h3>DOKUMENTASI PELAKSANAAN ANEV</h3>

    <img src="{{ $imgPath($anev->foto_anev) }}" class="anev-photo">
@endif

</body>
</html>