@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Data SOP</h2>
            <p class="text-muted mb-0">
                Daftar tahapan standar operasional patroli.
            </p>
        </div>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('sop.create') }}" class="btn btn-polairud">
                + Tambah SOP
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
        <form method="GET" action="{{ route('sop.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Urutan SOP</label>
                    <select name="sort" class="form-select">
                        <option value="asc" {{ request('sort', 'asc') === 'asc' ? 'selected' : '' }}>
                            SOP 1 ke 16
                        </option>
                        <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>
                            SOP 16 ke 1
                        </option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Pelaksana / Jabatan</label>
                    <select name="pelaksana" class="form-select">
                        <option value="">Semua Pelaksana</option>

                        @foreach($pelaksanaList as $item)
                            <option value="{{ $item }}" {{ request('pelaksana') === $item ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-polairud w-100">
                        Filter
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('sop.index') }}" class="btn btn-outline-secondary rounded-3 w-100">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="section-panel">
        <div class="table-responsive">
            <table class="table table-hover align-middle dashboard-table">
                <thead>
                    <tr>
                        <th width="90">No SOP</th>
                        <th>Tahapan</th>
                        <th>Pelaksana</th>
                        <th>Waktu</th>
                        <th>Kelengkapan</th>
                        <th>Output</th>

                        @if(auth()->user()->role === 'admin')
                            <th width="210">Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse($sops as $sop)
                        <tr>
                            <td>
                                <span class="sop-number">
                                    {{ $sop->urutan }}
                                </span>
                            </td>

                            <td>
                                <strong>{{ $sop->tahapan }}</strong>
                            </td>

                            <td>
                                {{ $sop->pelaksana ?? '-' }}
                            </td>

                            <td>
                                {{ $sop->waktu ?? '-' }}
                            </td>

                            <td>
                                {{ $sop->kelengkapan ?? '-' }}
                            </td>

                            <td>
                                {{ $sop->output ?? '-' }}
                            </td>

                            @if(auth()->user()->role === 'admin')
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('sop.edit', $sop) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                            Edit
                                        </a>

                                        <form action="{{ route('sop.destroy', $sop) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus SOP ini?')">
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
                                Belum ada data SOP.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $sops->links() }}
        </div>
    </div>
@endsection