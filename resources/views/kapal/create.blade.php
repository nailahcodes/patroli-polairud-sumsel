@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Tambah Kapal</h2>
            <p class="text-muted mb-0">
                Tambahkan data kapal patroli, zona, dan komandan kapal dari Manajemen User.
            </p>
        </div>

        <a href="{{ route('kapal.index') }}" class="btn btn-outline-secondary rounded-3">
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

    <div class="section-panel">
        <form action="{{ route('kapal.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kelompok</label>
                    <select name="kelompok" class="form-select" required>
                        <option value="">Pilih Kelompok</option>
                        <option value="Kelompok I" {{ old('kelompok') === 'Kelompok I' ? 'selected' : '' }}>Kelompok I</option>
                        <option value="Kelompok II" {{ old('kelompok') === 'Kelompok II' ? 'selected' : '' }}>Kelompok II</option>
                        <option value="Kelompok III" {{ old('kelompok') === 'Kelompok III' ? 'selected' : '' }}>Kelompok III</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kode Kapal</label>
                    <input type="text" name="kode_kapal" class="form-control" value="{{ old('kode_kapal') }}" placeholder="Contoh: V-3002" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status Kapal</label>
                    <select name="status" class="form-select" required>
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="perawatan" {{ old('status') === 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Zona Patroli</label>
                    <select name="zona_patroli" id="zona_patroli" class="form-select" required>
                        <option value="">Pilih Zona</option>
                        @foreach($zonaOptions as $zona => $wilayah)
                            <option value="{{ $zona }}" data-wilayah="{{ $wilayah }}" {{ old('zona_patroli') === $zona ? 'selected' : '' }}>
                                {{ $zona }} - {{ $wilayah }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Wilayah Patroli</label>
                    <input type="text" name="wilayah_patroli" id="wilayah_patroli" class="form-control" value="{{ old('wilayah_patroli') }}" readonly required>
                    <small class="text-muted">Otomatis mengikuti zona yang dipilih.</small>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Komandan Kapal</label>
                    <select name="komandan_id" class="form-select" required>
                        <option value="">Pilih Komandan Kapal</option>
                        @foreach($komandans as $komandan)
                            <option value="{{ $komandan->id }}" {{ old('komandan_id') == $komandan->id ? 'selected' : '' }}>
                                {{ $komandan->nama }} — {{ $komandan->pangkat ?? '-' }} — NRP: {{ $komandan->nrp }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Data diambil dari Manajemen User dengan role Komandan Kapal.</small>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('kapal.index') }}" class="btn btn-light rounded-3">
                    Batal
                </a>
                <button class="btn btn-polairud">
                    Simpan Kapal
                </button>
            </div>
        </form>
    </div>

    <script>
        const zonaSelect = document.getElementById('zona_patroli');
        const wilayahInput = document.getElementById('wilayah_patroli');

        function updateWilayah() {
            const selected = zonaSelect.options[zonaSelect.selectedIndex];
            wilayahInput.value = selected.getAttribute('data-wilayah') || '';
        }

        zonaSelect.addEventListener('change', updateWilayah);
        updateWilayah();
    </script>
@endsection