@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Arsip ANEV ABK</h2>
            <p class="text-muted mb-0">
                Daftar ANEV patroli yang pernah Anda isi.
            </p>
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

                <a href="{{ route('abk-anev.index') }}"
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
                        <th>Pembuat Laporan</th>
                        <th>Status</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anevs as $anev)
                        <tr>
                            <td>{{ $anevs->firstItem() + $loop->index }}</td>
                            <td>{{ $anev->patroli->kapal->kode_kapal ?? '-' }}</td>
                            <td>{{ $anev->patroli->wilayah_patroli ?? '-' }}</td>
                            <td>
                                {{ $anev->pembuatLaporan->nama ?? '-' }}<br>
                                <small class="text-muted">
                                    NRP: {{ $anev->pembuatLaporan->nrp ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge-status status-{{ $anev->status === 'tersimpan' ? 'selesai' : 'draft' }}">
                                    {{ ucfirst($anev->status) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $bolehEdit =
                                        auth()->user()->role === 'abk'
                                        && $anev->patroli
                                        && $anev->patroli->personels->contains(auth()->id());
                                @endphp

                                @if($bolehEdit)
                                    <a href="{{ route('abk-anev.edit', $anev->patroli) }}"
                                        class="btn btn-sm btn-outline-primary rounded-3">
                                        Edit
                                    </a>
                                @endif

                                <a href="{{ route('abk-anev.export', $anev->patroli) }}"
                                    class="btn btn-sm btn-polairud">
                                    Export PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada arsip ANEV.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $anevs->links() }}
        </div>
    </div>
@endsection