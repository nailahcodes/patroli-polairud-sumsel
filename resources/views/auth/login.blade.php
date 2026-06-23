<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Monitoring Patroli Polairud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-page">
        <div class="login-bg-shape shape-one"></div>
        <div class="login-bg-shape shape-two"></div>

        <div class="login-card">
            <div class="login-brand">
                <div class="login-logo">
                    ⚓
                </div>

                <div>
                    <h1>POLAIRUD</h1>
                    <p>Monitoring Patroli Air dan Udara</p>
                </div>
            </div>

            <div class="login-heading">
                <h2>Masuk Sistem</h2>
                <p>
                    Gunakan NRP dan password yang telah disetel oleh Admin.
                </p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger rounded-4">
                    <strong>Login gagal.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success rounded-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.process') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">NRP</label>
                    <input
                        type="text"
                        name="nrp"
                        class="form-control login-input"
                        value="{{ old('nrp') }}"
                        placeholder="Masukkan NRP"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control login-input"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="login-remember">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-polairud login-submit w-100">
                    Masuk
                </button>
            </form>

            <div class="login-footer">
                <p>
                    Sistem internal monitoring patroli. Akses hanya untuk pengguna terdaftar.
                </p>
            </div>
        </div>
    </div>
</body>
</html>