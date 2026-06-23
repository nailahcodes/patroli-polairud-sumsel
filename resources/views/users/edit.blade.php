@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Edit User</h2>
            <p class="text-muted mb-0">
                Perbarui data akun internal pengguna.
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
        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="profile-upload-card">
                        <div class="profile-upload-preview">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" class="profile-avatar-img profile-avatar-img-lg" alt="Foto Profil">
                            @else
                                <div class="profile-avatar profile-avatar-lg">
                                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <h5 class="fw-bold mb-1">Foto Profil</h5>
                        <p class="text-muted small mb-3">
                            Kosongkan jika tidak ingin mengganti foto.
                        </p>

                        <input
                            type="file"
                            name="profile_photo"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                        >

                        <small class="text-muted d-block mt-2">
                            Maksimal 2MB. Format JPG, JPEG, PNG.
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
                                value="{{ old('nama', $user->nama) }}"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NRP <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="nrp"
                                class="form-control"
                                value="{{ old('nrp', $user->nrp) }}"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Kosongkan jika tidak diganti"
                                minlength="6"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pangkat</label>
                            <input
                                type="text"
                                name="pangkat"
                                class="form-control"
                                value="{{ old('pangkat', $user->pangkat) }}"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input
                                type="text"
                                name="jabatan"
                                class="form-control"
                                value="{{ old('jabatan', $user->jabatan) }}"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="pimpinan" {{ old('role', $user->role) === 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                                <option value="komandan" {{ old('role', $user->role) === 'komandan' ? 'selected' : '' }}>Komandan Kapal</option>
                                <option value="abk" {{ old('role', $user->role) === 'abk' ? 'selected' : '' }}>ABK Kapal</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Akun <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', $user->status) === 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="nonaktif" {{ old('status', $user->status) === 'nonaktif' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Kapal Terkait</label>
                            <select name="kapal_id" class="form-select">
                                <option value="">Tidak terkait kapal</option>

                                @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->id }}" {{ old('kapal_id', $user->kapal_id) == $kapal->id ? 'selected' : '' }}>
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection