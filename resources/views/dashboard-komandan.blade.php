@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Dashboard Komandan Kapal</h2>
            <p class="text-muted mb-0">
                Ringkasan kapal, SOP yang ditangani, dan riwayat patroli Anda.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-sea">
                <div class="stat-icon">🚤</div>
                <small>Total Kapal</small>
                <h2>{{ $totalKapal }}</h2>
                <p>Data Kapal Patroli</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-gold">
                <div class="stat-icon">📋</div>
                <small>SOP Komandan</small>
                <h2>{{ $totalSop }}</h2>
                <p>Tahapan SOP 4 sampai 13</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-dark">
                <div class="stat-icon">📡</div>
                <small>Total Patroli</small>
                <h2>{{ $totalPatroli }}</h2>
                <p>Patroli yang ditangani</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="section-panel">
                <h5 class="section-title mb-3">Riwayat Patroli Ditangani</h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle dashboard-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kapal</th>
                                <th>Wilayah</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatPatroli as $patroli)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $patroli->kapal->kode_kapal ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $patroli->wilayah_patroli }}</td>
                                    <td>
                                        <small>
                                            {{ \Carbon\Carbon::parse($patroli->tanggal_mulai)->format('d/m/Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($patroli->tanggal_selesai)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge-status status-{{ $patroli->status }}">
                                            {{ ucfirst($patroli->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('patroli.show', $patroli) }}" class="btn btn-sm btn-outline-info rounded-3">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada riwayat patroli.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="section-panel">
                <h5 class="section-title mb-3">SOP yang Sedang Ditangani</h5>

                <div class="timeline-list">
                    @forelse($sopKomandan as $progress)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div>
                                <strong>
                                    SOP {{ $progress->sop->urutan ?? '-' }}
                                </strong>
                                <p class="mb-1 text-muted small">
                                    {{ $progress->sop->tahapan ?? '-' }}
                                </p>
                                <p class="mb-1 text-muted small">
                                    {{ $progress->patroli->kapal->kode_kapal ?? '-' }}
                                    —
                                    {{ $progress->patroli->wilayah_patroli ?? '-' }}
                                </p>
                                <span class="badge-status status-{{ $progress->status }}">
                                    {{ ucfirst($progress->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon">🌊</div>
                            <p class="mb-0">Belum ada SOP yang sedang ditangani.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection