@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Data Kapal</h2>
            <p class="text-muted mb-0">
                Data kapal patroli, zona, wilayah, dan komandan kapal.
            </p>
        </div>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('kapal.create') }}" class="btn btn-polairud">
                + Tambah Kapal
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
        <form method="GET" action="{{ route('kapal.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-12">
                    <label class="form-label">Search Kapal</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Cari kode kapal, kelompok, zona, wilayah, komandan, pangkat, atau NRP..."
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Urutan Kode Kapal</label>
                    <select name="sort" class="form-select">
                        <option value="asc" {{ request('sort', 'asc') === 'asc' ? 'selected' : '' }}>
                            Kecil ke Besar / A-Z
                        </option>
                        <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>
                            Besar ke Kecil / Z-A
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cari Kode Kapal</label>
                    <input
                        type="text"
                        name="kode_kapal"
                        class="form-control"
                        value="{{ request('kode_kapal') }}"
                        placeholder="Contoh: V-3002"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Zona</label>
                    <select name="zona_patroli" class="form-select">
                        <option value="">Semua Zona</option>

                        @foreach($zonaList as $zona)
                            <option value="{{ $zona }}" {{ request('zona_patroli') === $zona ? 'selected' : '' }}>
                                {{ $zona }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-polairud w-100">
                        Filter
                    </button>
                </div>

                <div class="col-md-12">
                    <a href="{{ route('kapal.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                        Reset Filter
                    </a>

                    @if(request('search') || request('kode_kapal') || request('zona_patroli') || request('sort'))
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
                        <th>Kode Kapal</th>
                        <th>Kelompok</th>
                        <th>Zona</th>
                        <th>Wilayah Patroli</th>
                        <th>Komandan Kapal</th>

                        @if(auth()->user()->role === 'admin')
                            <th width="210">Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse($kapals as $kapal)
                        <tr>
                            <td>{{ $kapals->firstItem() + $loop->index }}</td>

                            <td>
                                <strong>{{ $kapal->kode_kapal }}</strong>
                            </td>

                            <td>
                                {{ $kapal->kelompok ?? '-' }}
                            </td>

                            <td>
                                <span class="badge-status status-berjalan">
                                    {{ $kapal->zona_patroli ?? '-' }}
                                </span>
                            </td>

                            <td>
                                {{ $kapal->wilayah_patroli ?? '-' }}
                            </td>

                            <td>
                                @if($kapal->komandan)
                                    <strong>{{ $kapal->komandan->nama }}</strong><br>
                                    <small class="text-muted">
                                        {{ $kapal->komandan->pangkat ?? '-' }}
                                        —
                                        NRP: {{ $kapal->komandan->nrp }}
                                    </small>
                                @else
                                    <span>{{ $kapal->komandan_kapal ?? '-' }}</span>
                                @endif
                            </td>

                            @if(auth()->user()->role === 'admin')
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('kapal.edit', $kapal) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                            Edit
                                        </a>

                                        <form action="{{ route('kapal.destroy', $kapal) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus data kapal ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? 7 : 6 }}" class="text-center text-muted py-4">
                                Data kapal tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $kapals->links() }}
        </div>
    </div>
@endsection