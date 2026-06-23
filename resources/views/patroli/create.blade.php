@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Tambah Patroli</h2>
            <p class="text-muted mb-0">
                Buat jadwal patroli baru dan pilih personel ABK yang bertugas.
            </p>
        </div>

        <a href="{{ route('patroli.index') }}" class="btn btn-outline-secondary rounded-3">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4">
            <strong>Data belum valid.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $selectedPersonels = old('personel_ids', []);
    @endphp

    <div class="section-panel">
        <form action="{{ route('patroli.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nomor Surat Perintah / SPRIN</label>
                    <input
                        type="text"
                        name="nomor_sprin"
                        class="form-control"
                        value="{{ old('nomor_sprin') }}"
                        placeholder="Contoh: SPRIN/001/V/2026"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kapal <span class="text-danger">*</span></label>
                    <select
                        id="kapalSelect"
                        name="kapal_id"
                        class="form-select"
                        required
                    >
                        <option value="">Pilih Kapal</option>

                        @foreach($kapals as $kapal)
                            <option
                                value="{{ $kapal->id }}"
                                data-wilayah="{{ $kapal->wilayah_patroli }}"
                                {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}
                            >
                                {{ $kapal->kode_kapal }}
                                —
                                {{ $kapal->zona_patroli ?? '-' }}
                                {{ $kapal->wilayah_patroli ? ' / ' . $kapal->wilayah_patroli : '' }}
                                —
                                Komandan:
                                @if($kapal->komandan)
                                    {{ $kapal->komandan->nama }}
                                @else
                                    {{ $kapal->komandan_kapal ?? 'Belum diatur' }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted">
                        Komandan kapal otomatis mengikuti pengaturan pada Data Kapal.
                    </small>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Wilayah Patroli <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        id="wilayahPatroli"
                        name="wilayah_patroli"
                        class="form-control"
                        value="{{ old('wilayah_patroli') }}"
                        placeholder="Contoh: Zona 9 Kepayang"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Persiapan <span class="text-danger">*</span></label>
                    <input
                        type="date"
                        name="tanggal_persiapan"
                        class="form-control"
                        value="{{ old('tanggal_persiapan') }}"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input
                        type="date"
                        name="tanggal_mulai"
                        class="form-control"
                        value="{{ old('tanggal_mulai') }}"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input
                        type="date"
                        name="tanggal_selesai"
                        class="form-control"
                        value="{{ old('tanggal_selesai') }}"
                        required
                    >
                </div>

                <div class="col-md-12">
                    <label class="form-label">Personel ABK Kapal</label>

                    <div class="personel-select-box">
                        <div class="row g-2">
                            @forelse($abks as $abk)
                                <div class="col-md-6 col-lg-4">
                                    <label class="personel-checkbox">
                                        <input
                                            type="checkbox"
                                            name="personel_ids[]"
                                            value="{{ $abk->id }}"
                                            {{ in_array((string) $abk->id, array_map('strval', $selectedPersonels)) ? 'checked' : '' }}
                                        >

                                        <span>
                                            <strong>{{ $abk->nama }}</strong><br>
                                            <small>
                                                {{ $abk->pangkat ?? '-' }}
                                                —
                                                NRP: {{ $abk->nrp }}
                                            </small>
                                        </span>
                                    </label>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning rounded-4 mb-0">
                                        Belum ada user aktif dengan role ABK.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <small class="text-muted d-block mt-2">
                        Data personel diambil dari Manajemen User dengan role ABK.
                    </small>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Keterangan</label>
                    <textarea
                        name="keterangan"
                        class="form-control"
                        rows="4"
                        placeholder="Catatan tambahan jika diperlukan"
                    >{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex flex-wrap justify-content-end gap-2">
                <a href="{{ route('patroli.index') }}" class="btn btn-outline-secondary rounded-3">
                    Batal
                </a>

                <button type="submit" class="btn btn-polairud">
                    Simpan Patroli
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const kapalSelect = document.getElementById('kapalSelect');
            const wilayahInput = document.getElementById('wilayahPatroli');

            function isiWilayah() {

                const selected =
                    kapalSelect.options[kapalSelect.selectedIndex];

                const wilayah =
                    selected.getAttribute('data-wilayah');

                if (wilayah) {
                    wilayahInput.value = wilayah;
                }
            }

            kapalSelect.addEventListener('change', isiWilayah);

            isiWilayah();
        });
    </script>
@endsection