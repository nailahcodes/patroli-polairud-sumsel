@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Profil Saya</h2>
            <p class="text-muted mb-0">
                Kelola informasi akun dan foto profil yang digunakan di aplikasi.
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-3">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4">
            <strong>Upload gagal.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="section-panel text-center">
                @if($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" class="profile-avatar-large-img" alt="Foto Profil">
                @else
                    <div class="profile-avatar-large">
                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                    </div>
                @endif

                <h4 class="fw-bold mb-1">{{ $user->nama }}</h4>
                <p class="text-muted mb-3">
                    {{ $user->pangkat ?? '-' }} — {{ $user->jabatan ?? '-' }}
                </p>

                <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
                    <span class="badge-status status-berjalan">
                        {{ strtoupper($user->role) }}
                    </span>

                    <span class="badge-status {{ $user->status === 'aktif' ? 'status-selesai' : 'status-terlambat' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>

                <form action="{{ route('profile.photo.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="text-start">

                    @csrf

                    <label class="form-label">Upload Foto Profil</label>

                    <input type="file"
                        name="profile_photo"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                        required>

                    <small class="text-muted d-block mt-2">
                        Format: JPG, JPEG, PNG. Maksimal 2MB.
                    </small>

                    <button type="submit"
                            class="btn btn-polairud w-100 mt-3">
                        Simpan Foto Profil
                    </button>
                </form>

                @if($user->profile_photo)
                    <form action="{{ route('profile.photo.delete') }}" method="POST" class="mt-2"
                          onsubmit="return confirm('Yakin ingin menghapus foto profil?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-outline-danger rounded-3 w-100">
                            Hapus Foto
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="col-lg-8">
            <div class="section-panel">
                <h5 class="section-title mb-3">Detail Akun</h5>

                <table class="table table-borderless info-table">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $user->nama }}</td>
                    </tr>
                    <tr>
                        <th>NRP</th>
                        <td>{{ $user->nrp }}</td>
                    </tr>
                    <tr>
                        <th>Pangkat</th>
                        <td>{{ $user->pangkat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>{{ $user->jabatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ strtoupper($user->role) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst($user->status) }}</td>
                    </tr>
                    <tr>
                        <th>Kapal Terkait</th>
                        <td>
                            @if($user->kapal)
                                <strong>{{ $user->kapal->kode_kapal }}</strong><br>
                                <small class="text-muted">
                                    {{ $user->kapal->zona_patroli }} {{ $user->kapal->wilayah_patroli }}
                                </small>
                            @else
                                <span class="text-muted">Tidak terkait kapal</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary rounded-3">
                        Edit Data Akun di Manajemen User
                    </a>
                @else
                    <div class="alert alert-info rounded-4 mt-3 mb-0">
                        Perubahan nama, NRP, pangkat, jabatan, role, dan status hanya dapat dilakukan oleh Admin.
                        Foto profil dapat diubah sendiri oleh setiap pengguna.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection