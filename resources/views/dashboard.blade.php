@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Dashboard Monitoring Patroli</h2>
            <p class="text-muted mb-0">
                Ringkasan kegiatan patroli, progres SOP, kapal, dan administrasi laporan.
            </p>
        </div>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('patroli.create') }}" class="btn btn-polairud">
                + Buat Patroli Baru
            </a>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-blue">
                <div class="stat-icon">👥</div>
                <small>Total User</small>
                <h2>{{ $totalUser }}</h2>
                <p>Akun internal sistem</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-sea">
                <div class="stat-icon">🚤</div>
                <small>Total Kapal</small>
                <h2>{{ $totalKapal }}</h2>
                <p>Armada patroli aktif</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-gold">
                <div class="stat-icon">📋</div>
                <small>Tahapan SOP</small>
                <h2>{{ $totalSop }}</h2>
                <p>Standar operasional</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-dark">
                <div class="stat-icon">📡</div>
                <small>Total Patroli</small>
                <h2>{{ $totalPatroli }}</h2>
                <p>Periode patroli tercatat</p>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="mini-card">
                <div>
                    <span class="mini-label">Draft</span>
                    <h3>{{ $patroliDraft }}</h3>
                </div>
                <span class="mini-badge bg-secondary">Belum lengkap</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="mini-card">
                <div>
                    <span class="mini-label">Berjalan</span>
                    <h3>{{ $patroliBerjalan }}</h3>
                </div>
                <span class="mini-badge bg-info">Dalam proses</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="mini-card">
                <div>
                    <span class="mini-label">Selesai</span>
                    <h3>{{ $patroliSelesai }}</h3>
                </div>
                <span class="mini-badge bg-success">Selesai</span>
            </div>
        </div>
    </div>

    <div class="section-panel mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">
                Ringkasan Armada Kapal
            </h5>
            <small class="text-muted">
                Status kesiapan armada patroli
            </small>
        </div>

        <div class="row g-3">

            <div class="col-md-4">
                <div class="mini-card">
                    <div>
                        <span class="mini-label">
                            Sedang Patroli
                        </span>

                        <h3>
                            {{ $kapalSedangPatroli }}
                        </h3>
                    </div>

                    <span class="mini-badge bg-info">
                        Operasional
                    </span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mini-card">
                    <div>
                        <span class="mini-label">
                            Docking
                        </span>

                        <h3>
                            {{ $kapalDocking }}
                        </h3>
                    </div>

                    <span class="mini-badge bg-success">
                        Siap Digunakan
                    </span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mini-card">
                    <div>
                        <span class="mini-label">
                            Perawatan
                        </span>

                        <h3>
                            {{ $kapalPerawatan }}
                        </h3>
                    </div>

                    <span class="mini-badge bg-warning text-dark">
                        Maintenance
                    </span>
                </div>
            </div>

        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="section-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="section-title mb-0">Patroli Terbaru</h5>
                    <a href="{{ route('patroli.index') }}" class="small-link">Lihat semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle dashboard-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kapal</th>
                                <th>Wilayah</th>
                                <th>Tanggal</th>
                                <th>Progress SOP</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($patroliTerbaru as $item)
                                @php
                                    $totalTahapan = $item->sopProgress->count();
                                    $selesai = $item->sopProgress->where('status', 'selesai')->count();
                                    $persen = $totalTahapan > 0 ? round(($selesai / $totalTahapan) * 100) : 0;
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $item->kapal->kode_kapal ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $item->kapal->komandan_kapal ?? '-' }}</small>
                                    </td>
                                    <td>
                                        {{ $item->wilayah_patroli }}
                                    </td>
                                    <td>
                                        <small>
                                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td style="min-width: 150px;">
                                        <div class="progress progress-soft">
                                            <div
                                                class="progress-bar"
                                                role="progressbar"
                                                style="width: {{ $persen }}%;"
                                            >
                                                {{ $persen }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $selesai }} dari {{ $totalTahapan }} tahapan</small>
                                    </td>
                                    <td>
                                        <span class="badge-status status-{{ $item->status }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada data patroli.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="section-panel">
                <h5 class="section-title mb-3">Update SOP Terbaru</h5>

                <div class="timeline-list">
                    @forelse ($progressTerbaru as $progress)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div>
                                <strong>
                                    {{ $progress->sop->urutan ?? '-' }}.
                                    {{ \Illuminate\Support\Str::limit($progress->sop->tahapan ?? '-', 42) }}
                                </strong>

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
                            <p class="mb-0">Belum ada update progress SOP.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection