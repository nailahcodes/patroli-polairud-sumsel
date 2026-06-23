@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Arsip Laporan ABK</h2>
            <p class="text-muted mb-0">Daftar laporan patroli yang pernah Anda isi.</p>
        </div>
        <form method="GET" class="row g-2 mb-4">

            <div class="col-md-4">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Cari kapal, wilayah, nama, NRP...">
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="draft"
                        @selected(request('status')=='draft')>
                        Draft
                    </option>
                    <option value="tersimpan"
                        @selected(request('status')=='tersimpan')>
                        Tersimpan
                    </option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="kapal" class="form-select">
                    <option value="">Semua Kapal</option>

                    @foreach($kapals as $item)
                        <option
                            value="{{ $item->id }}"
                            @selected(request('kapal')==$item->id)>
                            {{ $item->kode_kapal }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-polairud">
                    Cari
                </button>

                <a href="{{ route('abk-laporan.index') }}"
                    class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <div class="section-panel">
        <div class="table-responsive">
            <table class="table table-hover align-middle dashboard-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kapal</th>
                        <th>Wilayah</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $laporan)
                        <tr>
                            <td>{{ $laporans->firstItem() + $loop->index }}</td>
                            <td>{{ $laporan->patroli->kapal->kode_kapal ?? '-' }}</td>
                            <td>{{ $laporan->patroli->wilayah_patroli ?? '-' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($laporan->patroli->tanggal_mulai)->format('d/m/Y') }}
                                -
                                {{ \Carbon\Carbon::parse($laporan->patroli->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge-status status-{{ $laporan->status === 'tersimpan' ? 'selesai' : 'draft' }}">
                                    {{ ucfirst($laporan->status) }}
                                </span>
                            </td>
                            <td>

                                @php
                                    $bolehEdit =
                                        auth()->user()->role === 'abk'
                                        && $laporan->patroli
                                        && $laporan->patroli->personels->contains(auth()->id());
                                @endphp

                                @if($bolehEdit)
                                    <a href="{{ route('abk-laporan.edit', $laporan->patroli) }}"
                                        class="btn btn-sm btn-outline-primary rounded-3">
                                        Edit
                                    </a>
                                @endif

                                <a href="{{ route('abk-laporan.export', $laporan->patroli) }}"
                                    class="btn btn-sm btn-polairud">
                                    Export PDF
                                </a>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada arsip laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $laporans->links() }}
        </div>
    </div>
@endsection