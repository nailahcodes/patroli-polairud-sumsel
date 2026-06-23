<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Periode Patroli</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 13px;
            margin-top: 4px;
        }

        .section-title {
            background: #0f172a;
            color: white;
            padding: 8px;
            margin-top: 16px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 7px;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            font-weight: bold;
        }

        .no-border th,
        .no-border td {
            border: none;
            padding: 5px;
        }

        .status {
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Periode Patroli 8 Hari</div>
        <div class="subtitle">Ditpolairud Polda Sumsel</div>
    </div>

    <div class="section-title">A. Identitas Patroli</div>
    <table>
        <tr>
            <th width="30%">Nomor Sprin</th>
            <td>{{ $patroli->nomor_sprin ?? '-' }}</td>
        </tr>
        <tr>
            <th>Kapal</th>
            <td>{{ $patroli->kapal->kode_kapal ?? '-' }}</td>
        </tr>
        <tr>
            <th>Komandan Kapal</th>
            <td>{{ $patroli->kapal->komandan_kapal ?? '-' }}</td>
        </tr>
        <tr>
            <th>Wilayah Patroli</th>
            <td>{{ $patroli->wilayah_patroli }}</td>
        </tr>
        <tr>
            <th>Tanggal Persiapan</th>
            <td>{{ $patroli->tanggal_persiapan }}</td>
        </tr>
        <tr>
            <th>Tanggal Patroli</th>
            <td>{{ $patroli->tanggal_mulai }} s/d {{ $patroli->tanggal_selesai }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($patroli->status) }}</td>
        </tr>
    </table>

    <div class="section-title">B. Personel ABK Kapal</div>
    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Nama</th>
                <th>NRP</th>
                <th>Pangkat</th>
                <th>Posisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patroli->personels as $personel)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $personel->nama }}</td>
                    <td>{{ $personel->nrp }}</td>
                    <td>{{ $personel->pangkat ?? '-' }}</td>
                    <td>{{ $personel->pivot->posisi ?? 'ABK Kapal' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada personel ABK.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">C. Progress SOP</div>
    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Tahapan SOP</th>
                <th>Pelaksana</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patroli->sopProgress->sortBy('sop.urutan') as $progress)
                <tr>
                    <td>{{ $progress->sop->urutan }}</td>
                    <td>{{ $progress->sop->tahapan }}</td>
                    <td>{{ $progress->sop->pelaksana }}</td>
                    <td>{{ $progress->sop->waktu }}</td>
                    <td class="status">{{ $progress->status }}</td>
                    <td>{{ $progress->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada progress SOP.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">D. Kronologi Patroli</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jam WIB</th>
                <th>Kegiatan</th>
                <th>Koordinat</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patroli->kronologis as $kronologi)
                <tr>
                    <td>{{ $kronologi->tanggal }}</td>
                    <td>{{ $kronologi->jam_wib }}</td>
                    <td>{{ $kronologi->jenis_kegiatan }}</td>
                    <td>{{ $kronologi->titik_koordinat ?? '-' }}</td>
                    <td>{{ $kronologi->deskripsi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada kronologi patroli.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>