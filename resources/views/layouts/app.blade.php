<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Patroli Polairud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#123f7a">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    @auth
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">⚓</div>
                <div>
                    <h4>POLAIRUD</h4>
                    <small>Monitoring Patroli</small>
                </div>
            </div>

            <nav>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('patroli.index') }}" class="{{ request()->routeIs('patroli.*') ? 'active' : '' }}">
                    Patroli
                </a>

                <a href="{{ route('kapal.index') }}" class="{{ request()->routeIs('kapal.*') ? 'active' : '' }}">
                    Kapal
                </a>

                <a href="{{ route('sop.index') }}" class="{{ request()->routeIs('sop.*') ? 'active' : '' }}">
                    SOP
                </a>

                @if(in_array(auth()->user()->role, [
                    'admin',
                    'pimpinan',
                    'komandan',
                    'abk'
                ]))
                    <a href="{{ route('abk-laporan.index') }}"
                    class="{{ request()->routeIs('abk-laporan.*') ? 'active' : '' }}">
                        Arsip Laporan ABK
                    </a>

                    <a href="{{ route('abk-anev.index') }}"
                    class="{{ request()->routeIs('abk-anev.*') ? 'active' : '' }}">
                        Arsip ANEV ABK
                    </a>
                @endif

                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        Manajemen User
                    </a>
                @endif
            </nav>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn">Keluar</button>
            </form>
        </aside>
    @endauth

    <main class="main-content">
        @auth
            <header class="topbar">
                <div>
                    <strong>{{ auth()->user()->nama }}</strong><br>
                    <small>
                        {{ auth()->user()->pangkat ?? '-' }} - {{ strtoupper(auth()->user()->role) }}
                    </small>
                </div>
            </header>
        @endauth

        <section class="content-card">
            @yield('content')
        </section>

        @auth
            <button class="floating-profile-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#profileCanvas">
                @if(auth()->user()->profile_photo_url)
                    <img src="{{ auth()->user()->profile_photo_url }}" class="floating-avatar-img" alt="Foto Profil">
                @else
                    <span class="floating-avatar">
                        {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                    </span>
                @endif

                <span class="floating-profile-text">Profil</span>
            </button>

            <div class="offcanvas offcanvas-end profile-canvas" tabindex="-1" id="profileCanvas">
                <div class="offcanvas-header">
                    <div>
                        <h5 class="offcanvas-title fw-bold">Profil Pengguna</h5>
                        <small class="text-muted">Informasi akun internal</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>

                <div class="offcanvas-body">
                    <div class="profile-card-side">
                        @if(auth()->user()->profile_photo_url)
                            <img src="{{ auth()->user()->profile_photo_url }}" class="profile-avatar-img" alt="Foto Profil">
                        @else
                            <div class="profile-avatar">
                                {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                            </div>
                        @endif

                        <h5 class="fw-bold mb-1">{{ auth()->user()->nama }}</h5>
                        <p class="text-muted mb-3">
                            {{ auth()->user()->pangkat ?? '-' }} — {{ auth()->user()->jabatan ?? '-' }}
                        </p>

                        <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                            <span class="badge-status status-berjalan">
                                {{ strtoupper(auth()->user()->role) }}
                            </span>

                            <span class="badge-status {{ auth()->user()->status === 'aktif' ? 'status-selesai' : 'status-draft' }}">
                                {{ ucfirst(auth()->user()->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="profile-info-list">
                        <div class="profile-info-item">
                            <span>NRP</span>
                            <strong>{{ auth()->user()->nrp }}</strong>
                        </div>

                        <div class="profile-info-item">
                            <span>Pangkat</span>
                            <strong>{{ auth()->user()->pangkat ?? '-' }}</strong>
                        </div>

                        <div class="profile-info-item">
                            <span>Jabatan</span>
                            <strong>{{ auth()->user()->jabatan ?? '-' }}</strong>
                        </div>

                        <div class="profile-info-item">
                            <span>Role</span>
                            <strong>{{ strtoupper(auth()->user()->role) }}</strong>
                        </div>

                        <div class="profile-info-item">
                            <span>Kapal Terkait</span>
                            <strong>
                                @if(auth()->user()->kapal)
                                    {{ auth()->user()->kapal->kode_kapal }}
                                @else
                                    -
                                @endif
                            </strong>
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <a href="{{ route('profile.show') }}" class="btn btn-polairud">
                            Atur Foto Profil
                        </a>

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('users.edit', auth()->user()) }}" class="btn btn-outline-secondary rounded-3">
                                Edit Data Akun
                            </a>
                        @else
                            <div class="profile-note">
                                Foto profil dapat diubah sendiri. Data akun lain dikelola oleh Admin.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endauth
    </main>
</div>
</body>
</html>