@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Dashboard ABK Kapal</h2>
            <p class="text-muted mb-0">
                Ringkasan patroli, kapal, dan tahapan SOP yang menjadi tanggung jawab ABK.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-sea">
                <div class="stat-icon">📡</div>
                <small>Total Patroli</small>
                <h2>{{ $totalPatroli }}</h2>
                <p>Patroli yang sedang/sudah Anda urus</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-dark">
                <div class="stat-icon">🚤</div>
                <small>Total Kapal</small>
                <h2>{{ $totalKapal }}</h2>
                <p>Kapal yang terkait dengan penugasan Anda</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-gold">
                <div class="stat-icon">📋</div>
                <small>Total SOP ABK</small>
                <h2>{{ $totalSop }}</h2>
                <p>SOP nomor 14 sampai 16</p>
            </div>
        </div>
    </div>

    <div class="section-panel mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="section-title mb-0">SOP yang Ditangani</h5>
                <small class="text-muted">Tahapan SOP ABK dari patroli yang ditugaskan.</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle dashboard-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kapal</th>
                        <th>SOP</th>
                        <th>Tahapan</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sopAbk as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->patroli->kapal->kode_kapal ?? '-' }}</td>
                            <td>SOP {{ $item->sop->urutan ?? '-' }}</td>
                            <td>{{ $item->sop->tahapan ?? '-' }}</td>
                            <td>
                                <span class="badge-status status-{{ $item->status }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>{{ optional($item->updated_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada SOP ABK yang ditangani.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="section-title mb-0">Riwayat Patroli</h5>
                <small class="text-muted">Patroli yang sedang atau pernah Anda ikuti.</small>
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
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPatroli as $patroli)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $patroli->kapal->kode_kapal ?? '-' }}</td>
                            <td>{{ $patroli->wilayah_patroli ?? '-' }}</td>
                            <td>
                                {{ optional($patroli->tanggal_mulai)->format('d/m/Y') }}
                                -
                                {{ optional($patroli->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge-status status-{{ $patroli->status }}">
                                    {{ ucfirst($patroli->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('patroli.show', $patroli) }}" class="btn btn-sm btn-polairud">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada patroli untuk ABK ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection