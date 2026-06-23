@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Dashboard Pimpinan</h2>
            <p class="text-muted mb-0">
                Ringkasan monitoring kapal, SOP, dan patroli Ditpolairud.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-sea">
                <div class="stat-icon">🚤</div>
                <small>Total Kapal</small>
                <h2>{{ $totalKapal }}</h2>
                <p>Data kapal patroli</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-gold">
                <div class="stat-icon">📋</div>
                <small>Total SOP</small>
                <h2>{{ $totalSop }}</h2>
                <p>Tahapan standar operasional</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-dark">
                <div class="stat-icon">📡</div>
                <small>Total Patroli</small>
                <h2>{{ $totalPatroli }}</h2>
                <p>Seluruh data patroli</p>
            </div>
        </div>
    </div>

    <div class="section-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="section-title mb-0">Patroli Terbaru</h5>
                <small class="text-muted">Data patroli terakhir yang masuk ke sistem.</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle dashboard-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kapal</th>
                        <th>Wilayah</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Validasi</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patroliTerbaru as $patroli)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $patroli->kapal->kode_kapal ?? '-' }}</strong>
                            </td>
                            <td>{{ $patroli->wilayah_patroli }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($patroli->tanggal_mulai)->format('d/m/Y') }}
                                -
                                {{ \Carbon\Carbon::parse($patroli->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge-status status-{{ $patroli->status }}">
                                    {{ ucfirst($patroli->status) }}
                                </span>
                            </td>
                            <td>
                                @if($patroli->validasi_pimpinan_status)
                                    <strong>{{ ucfirst($patroli->validasi_pimpinan_status) }}</strong><br>
                                    <small class="text-muted">
                                        {{ optional($patroli->validasi_pimpinan_at)->format('d/m/Y H:i') }}
                                    </small>
                                @else
                                    <span class="text-muted">Belum divalidasi</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('patroli.show', $patroli) }}" class="btn btn-sm btn-polairud">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data patroli.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection