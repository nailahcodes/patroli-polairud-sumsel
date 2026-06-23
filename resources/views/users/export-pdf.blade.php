<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Manajemen User</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
        }

        h2, h4 {
            margin: 0;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            margin-top: 6px;
            margin-bottom: 18px;
            font-size: 10px;
        }

        .filter-box {
            margin-bottom: 12px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #dbeafe;
            font-weight: bold;
            text-align: center;
        }

        th, td {
            border: 1px solid #111827;
            padding: 5px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>DATA MANAJEMEN USER</h2>
    <h4>MONITORING PATROLI POLAIRUD</h4>

    <div class="subtitle">
        Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} WIB
    </div>

    <div class="filter-box">
        <strong>Filter:</strong>
        Search:
        {{ $search ?: '-' }}
        |
        Role:
        {{ $role ? strtoupper($role) : 'Semua Role' }}
        |
        Status:
        {{ $status ? ucfirst($status) : 'Semua Status' }}
        |
        Urutan:
        {{ $sort === 'asc' ? 'A-Z' : 'Z-A' }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="18%">Nama</th>
                <th width="10%">NRP</th>
                <th width="10%">Pangkat</th>
                <th width="14%">Jabatan</th>
                <th width="9%">Role</th>
                <th width="9%">Status Akun</th>
                <th width="10%">Kapal</th>
                <th width="10%">Password Awal</th>
                <th width="16%">Status Tugas</th>
            </tr>
        </thead>

        <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $user->nama ?? '-' }}</td>
                    <td>{{ $user->nrp ?? '-' }}</td>
                    <td>{{ $user->pangkat ?? '-' }}</td>
                    <td>{{ $user->jabatan ?? '-' }}</td>
                    <td class="text-center">{{ strtoupper($user->role ?? '-') }}</td>
                    <td class="text-center">{{ ucfirst($user->status ?? '-') }}</td>
                    <td>
                        @if($user->kapal)
                            {{ $user->kapal->kode_kapal }}<br>
                            <small>{{ $user->kapal->wilayah_patroli ?? '-' }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($user->role === 'admin')
                            ADM{{ substr($user->nrp, -4) }}#
                        @elseif($user->role === 'pimpinan')
                            PMP{{ substr($user->nrp, -4) }}#
                        @elseif(in_array($user->role, ['komandan', 'abk']))
                            Patroliairud{{ substr($user->nrp, -4) }}#
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($userBertugasIds->contains($user->id))
                            <span class="badge">Sedang Bertugas</span>
                        @else
                            Tidak Bertugas
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        Data user tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>