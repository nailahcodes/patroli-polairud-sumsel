@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Tambah User</h2>
            <p class="text-muted mb-0">
                Tambahkan akun internal pengguna berdasarkan NRP dan password.
            </p>
        </div>

        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary rounded-3">
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
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="profile-upload-card">
                        <div class="profile-upload-preview">
                            <div class="profile-avatar profile-avatar-lg">
                                👤
                            </div>
                        </div>

                        <h5 class="fw-bold mb-1">Foto Profil</h5>
                        <p class="text-muted small mb-3">
                            Opsional. Format JPG, JPEG, atau PNG.
                        </p>

                        <input
                            type="file"
                            name="profile_photo"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                        >

                        <small class="text-muted d-block mt-2">
                            Maksimal 2MB.
                        </small>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                value="{{ old('nama') }}"
                                placeholder="Contoh: Aipda Nandi Zaidan"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NRP <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="nrp"
                                class="form-control"
                                value="{{ old('nrp') }}"
                                placeholder="Masukkan NRP"
                                required
                            >
                            <small class="text-muted">
                                NRP digunakan untuk login.
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Minimal 6 karakter"
                                required
                                minlength="6"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pangkat</label>
                            <input
                                type="text"
                                name="pangkat"
                                class="form-control"
                                value="{{ old('pangkat') }}"
                                placeholder="Contoh: Aipda / Bripka / AKP"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input
                                type="text"
                                name="jabatan"
                                class="form-control"
                                value="{{ old('jabatan') }}"
                                placeholder="Contoh: Komandan Kapal / ABK Kapal"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="pimpinan" {{ old('role') === 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                                <option value="komandan" {{ old('role') === 'komandan' ? 'selected' : '' }}>Komandan Kapal</option>
                                <option value="abk" {{ old('role') === 'abk' ? 'selected' : '' }}>ABK Kapal</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Akun <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Kapal Terkait</label>
                            <select name="kapal_id" class="form-select">
                                <option value="">Tidak terkait kapal</option>

                                @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                        {{ $kapal->kode_kapal }}
                                        —
                                        {{ $kapal->zona_patroli ?? '-' }}
                                        {{ $kapal->wilayah_patroli ? ' / ' . $kapal->wilayah_patroli : '' }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="text-muted">
                                Untuk Komandan Kapal, kapal dapat juga diatur dari menu Data Kapal.
                                Untuk ABK, personel dipilih saat membuat patroli.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex flex-wrap justify-content-end gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary rounded-3">
                    Batal
                </a>

                <button type="submit" class="btn btn-polairud">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
@endsection