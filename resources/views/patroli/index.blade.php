@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Data Patroli</h2>
            <p class="text-muted mb-0">
                Monitoring jadwal, personel, kapal, dan progress patroli.
            </p>
        </div>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('patroli.create') }}" class="btn btn-polairud">
                + Tambah Patroli
            </a>
        @endif
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

    <div class="section-panel mb-4">
        <form method="GET" action="{{ route('patroli.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-12">
                    <label class="form-label">Search Patroli</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Cari nomor sprin, kode kapal, wilayah, status, komandan, atau personel ABK..."
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Urutan</label>
                    <select name="sort" class="form-select">
                        <option value="desc" {{ request('sort', 'desc') === 'desc' ? 'selected' : '' }}>
                            Terbaru ke Terlama
                        </option>
                        <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>
                            Terlama ke Terbaru
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status Patroli</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(['draft', 'diproses', 'berjalan', 'selesai', 'valid', 'perbaiki'] as $item)
                            <option value="{{ $item }}" {{ request('status') === $item ? 'selected' : '' }}>
                                {{ ucfirst($item) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kode Kapal</label>
                    <select name="kapal_id" class="form-select">
                        <option value="">Semua Kapal</option>

                        @foreach($kapals as $kapal)
                            <option value="{{ $kapal->id }}" {{ request('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                {{ $kapal->kode_kapal }} — {{ $kapal->wilayah_patroli ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-polairud w-100">
                        Filter
                    </button>
                </div>

                <div class="col-md-12">
                    <a href="{{ route('patroli.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                        Reset Filter
                    </a>

                    @if(request('search') || request('status') || request('kapal_id') || request('sort'))
                        <span class="filter-chip ms-2">
                            Hasil filter aktif
                        </span>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="section-panel">
        <div class="table-responsive">
            <table class="table table-hover align-middle dashboard-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Sprin</th>
                        <th>Kapal</th>
                        <th>Wilayah</th>
                        <th>Periode</th>
                        <th>Personel</th>
                        <th>Status</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($patrolis as $patroli)
                        <tr>
                            <td>{{ $patrolis->firstItem() + $loop->index }}</td>

                            <td>
                                <strong>{{ $patroli->nomor_sprin ?? '-' }}</strong>
                            </td>

                            <td>
                                <strong>{{ $patroli->kapal->kode_kapal ?? '-' }}</strong><br>
                                <small class="text-muted">
                                    Komandan:
                                    {{ $patroli->kapal->komandan->nama ?? $patroli->kapal->komandan_kapal ?? '-' }}
                                </small>
                            </td>

                            <td>
                                {{ $patroli->wilayah_patroli ?? '-' }}
                            </td>

                            <td>
                                {{ optional($patroli->tanggal_mulai)->format('d/m/Y') }}
                                -
                                {{ optional($patroli->tanggal_selesai)->format('d/m/Y') }}
                            </td>

                            <td>
                                <span class="badge-status status-berjalan">
                                    {{ $patroli->personels->count() }} ABK
                                </span>

                                @if($patroli->personels->count() > 0)
                                    <div class="small text-muted mt-1">
                                        {{ $patroli->personels->take(2)->pluck('nama')->join(', ') }}
                                        @if($patroli->personels->count() > 2)
                                            +{{ $patroli->personels->count() - 2 }} lainnya
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge-status status-{{ $patroli->status }}">
                                    {{ ucfirst($patroli->status) }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('patroli.show', $patroli) }}" class="btn btn-sm btn-polairud">
                                        Detail
                                    </a>

                                    @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('patroli.edit', $patroli) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                            Edit
                                        </a>

                                        <form action="{{ route('patroli.destroy', $patroli) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus patroli ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-outline-danger rounded-3">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Data patroli tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $patrolis->links() }}
        </div>
    </div>
@endsection