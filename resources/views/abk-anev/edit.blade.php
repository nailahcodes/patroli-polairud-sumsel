@extends('layouts.app')

@section('content')
    @php
        $riksaKapals = $patroli->abkLaporan?->riksaKapals ?? collect();
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">ANEV ABK SOP 15</h2>
            <p class="text-muted mb-0">
                Isi hambatan, kendala, pembuat laporan, dan dokumentasi pelaksanaan ANEV.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('patroli.show', $patroli) }}" class="btn btn-outline-secondary rounded-3">
                Kembali
            </a>

            <a href="{{ route('abk-anev.export', $patroli) }}" class="btn btn-polairud">
                Export PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <strong>Data belum valid.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="anev-form" action="{{ route('abk-anev.update', $patroli) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="section-panel mb-4">
            <h5 class="section-title mb-3">A. Hasil yang Dicapai</h5>

            <p class="text-muted">
                Data hasil yang dicapai otomatis diambil dari seluruh input riksa kapal pada laporan SOP 14.
            </p>

            <div class="table-responsive">
                <table class="table table-hover align-middle dashboard-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kapal</th>
                            <th>Dari/Tujuan</th>
                            <th>Muatan</th>
                            <th>Tindak Pidana</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riksaKapals as $riksa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $riksa->nama_kapal ?? '-' }}</td>
                                <td>{{ $riksa->dari_tujuan ?? '-' }}</td>
                                <td>{{ $riksa->muatan ?? '-' }}</td>
                                <td>
                                    @if($riksa->kategori === 'tindak_pidana')
                                        Ya
                                    @else
                                        Tidak
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada data riksa kapal dari laporan SOP 14.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-panel mb-4">
            <h5 class="section-title mb-3">B. Hambatan dan Kendala</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Hambatan yang Dihadapi</label>
                    <textarea name="hambatan" class="form-control" rows="6" placeholder="Contoh:
a) Cuaca buruk
b) Jarak tempuh jauh">{{ old('hambatan', $anev->hambatan) }}</textarea>
                    <small class="text-muted">
                        Isi sesuai format poin a), b), c), dan seterusnya.
                    </small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kendala yang Dihadapi</label>
                    <textarea name="kendala" class="form-control" rows="6" placeholder="Contoh:
a) Keterbatasan sinyal komunikasi
b) Arus sungai deras">{{ old('kendala', $anev->kendala) }}</textarea>
                    <small class="text-muted">
                        Isi sesuai format poin a), b), c), dan seterusnya.
                    </small>
                </div>
            </div>
        </div>

        <div class="section-panel mb-4">
            <h5 class="section-title mb-3">C. Pembuat Laporan dan Dokumentasi</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Pembuat Laporan</label>
                    <select name="pembuat_laporan_id" class="form-select" required>
                        <option value="">Pilih User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('pembuat_laporan_id', $anev->pembuat_laporan_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->nama }} — {{ $user->pangkat ?? '-' }} — NRP: {{ $user->nrp }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        Data nama dan NRP diambil dari Manajemen User.
                    </small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Bukti Foto Pelaksanaan ANEV</label>

                    @if($anev->foto_anev_url)
                        <a href="{{ $anev->foto_anev_url }}" target="_blank" class="d-block mb-2">
                            Lihat foto ANEV yang sudah diupload
                        </a>
                    @endif

                    <input type="file" name="foto_anev" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                    <small class="text-muted">
                        Format JPG, JPEG, PNG. Maksimal 5MB.
                    </small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-polairud">
                Simpan ANEV
            </button>
        </div>
    </form>

    <script type="module">

        import {
            saveAnevDraft,
            loadAnevDraft
        }
        from '/resources/js/indexeddb-anev.js';

        document.addEventListener(
            'DOMContentLoaded',
            async () => {

            const form =
                document.getElementById(
                    'anev-form'
                );

            if (!form) return;

            const draftKey =
                'anev-{{ $patroli->id }}';

            const saved =
                await loadAnevDraft(
                    draftKey
                );

            if (saved?.data) {

                Object.entries(
                    saved.data
                ).forEach(
                    ([name, value]) => {

                    const field =
                        form.querySelector(
                            `[name="${name}"]`
                        );

                    if (
                        field &&
                        field.type !== 'file'
                    ) {
                        field.value =
                            value;
                    }

                });

            }

            form.addEventListener(
                'input',
                async () => {

                const data = {};

                form.querySelectorAll(
                    'input, textarea, select'
                ).forEach(field => {

                    if (
                        !field.name ||
                        field.type === 'file'
                    ) {
                        return;
                    }

                    data[field.name] =
                        field.value;

                });

                await saveAnevDraft(
                    draftKey,
                    data
                );

            });

        });
    </script>

    @if(session('success'))
        <script>

            document.addEventListener(
                'DOMContentLoaded',
                () => {

                const request =
                    indexedDB.open(
                        'polairudDB'
                    );

                request.onsuccess =
                    () => {

                    const db =
                        request.result;

                    const tx =
                        db.transaction(
                            'anevDraft',
                            'readwrite'
                        );

                    tx.objectStore(
                        'anevDraft'
                    ).delete(
                        'anev-{{ $patroli->id }}'
                    );

                };

            });

        </script>
    @endif
@endsection