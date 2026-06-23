@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Manajemen User</h2>
            <p class="text-muted mb-0">
                @if(auth()->user()->role === 'pimpinan')
                    Monitoring seluruh user dan status penugasan patroli.
                @else
                    Kelola akun internal pengguna berdasarkan NRP dan role.
                @endif
            </p>
        </div>

        @if(auth()->user()->role === 'admin')
            <div class="d-flex flex-wrap gap-2">
                <a
                    href="{{ route('users.export-pdf', request()->query()) }}"
                    class="btn btn-outline-danger rounded-3"
                >
                    Export PDF
                </a>

                <a href="{{ route('users.create') }}" class="btn btn-polairud">
                    + Tambah User
                </a>
            </div>
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
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-12">
                    <label class="form-label">Search User</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, NRP, pangkat, jabatan, role, status, atau kapal..."
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Urutan Nama</label>
                    <select name="sort" class="form-select">
                        <option value="asc" {{ request('sort', 'asc') === 'asc' ? 'selected' : '' }}>
                            A-Z
                        </option>
                        <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>
                            Z-A
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jabatan / Role</label>
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pimpinan" {{ request('role') === 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                        <option value="komandan" {{ request('role') === 'komandan' ? 'selected' : '' }}>Komandan Kapal</option>
                        <option value="abk" {{ request('role') === 'abk' ? 'selected' : '' }}>ABK Kapal</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status Akun</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-polairud w-100">
                        Filter
                    </button>
                </div>

                <div class="col-md-12">
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                        Reset Filter
                    </a>

                    @if(request('search') || request('role') || request('status') || request('sort'))
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
                        <th>Identitas</th>
                        <th>NRP</th>
                        <th>Role</th>
                        <th>Kapal</th>
                        <th>Status Akun</th>
                        <th>Status Tugas</th>

                        @if(auth()->user()->role === 'admin')
                            <th width="260">Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>

                            <td>
                                <div class="user-identity">
                                    @if($user->profile_photo_url)
                                        <img src="{{ $user->profile_photo_url }}" class="user-avatar-img" alt="Foto Profil">
                                    @else
                                        <div class="user-avatar-initial">
                                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div>
                                        <strong>{{ $user->nama }}</strong><br>
                                        <small class="text-muted">
                                            {{ $user->pangkat ?? '-' }} — {{ $user->jabatan ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="fw-bold">{{ $user->nrp }}</span>
                            </td>

                            <td>
                                <span class="badge-status status-berjalan">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>

                            <td>
                                @if($user->kapal)
                                    <strong>{{ $user->kapal->kode_kapal }}</strong><br>
                                    <small class="text-muted">
                                        {{ $user->kapal->wilayah_patroli ?? '-' }}
                                    </small>
                                @else
                                    <span class="text-muted">Tidak terkait kapal</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-status {{ $user->status === 'aktif' ? 'status-selesai' : 'status-draft' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>

                            <td>
                                @if(isset($userBertugasIds) && $userBertugasIds->contains($user->id))
                                    <span class="badge-status status-berjalan">
                                        Sedang Bertugas
                                    </span>
                                @else
                                    <span class="badge-status status-draft">
                                        Tidak Bertugas
                                    </span>
                                @endif
                            </td>

                            @if(auth()->user()->role === 'admin')
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                            Edit
                                        </a>

                                        <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-3">
                                                {{ $user->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary rounded-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#resetPasswordModal{{ $user->id }}"
                                        >
                                            Reset
                                        </button>

                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>

                                    <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reset Password</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form action="{{ route('users.reset-password', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <div class="modal-body">
                                                        <p class="text-muted">
                                                            Reset password untuk <strong>{{ $user->nama }}</strong>.
                                                        </p>

                                                        <label class="form-label">Password Baru</label>
                                                        <input
                                                            type="password"
                                                            name="password_baru"
                                                            class="form-control"
                                                            required
                                                            minlength="6"
                                                        >
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">
                                                            Batal
                                                        </button>

                                                        <button type="submit" class="btn btn-polairud">
                                                            Simpan Password
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? 8 : 7 }}" class="text-center text-muted py-4">
                                Data user tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
@endsection