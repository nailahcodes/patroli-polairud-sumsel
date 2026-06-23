@extends('layouts.app')

@section('content')
    @php
        $key = fn($value) => strtolower(str_replace([' ', '/', '(', ')'], '_', $value));

        $anggaranMap = $laporan->anggarans->keyBy('komponen');
        $logistikMap = $laporan->logistiks->keyBy('jenis');

        $koordinatBertolak = optional($laporan->koordinats->where('jenis', 'bertolak')->first())->koordinat;
        $koordinatBersandar = optional($laporan->koordinats->where('jenis', 'bersandar')->first())->koordinat;

        $anggaranItems = [
            'Uang Lauk Pauk (ULP)',
            'Uang Saku Komandan Kapal',
            'Uang Saku (Pa Nautika)',
            'Uang Saku (Pa Teknika)',
            'Uang Saku (ABK)',
            'Uang Saku Air Tawar',
        ];

        $logistikItems = [
            'Pertamax',
            'Prima XP',
            'Rored EPA',
        ];
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Laporan ABK SOP 14</h2>
            <p class="text-muted mb-0">
                Isi laporan patroli ABK untuk kapal {{ $patroli->kapal->kode_kapal ?? '-' }}.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('patroli.show', $patroli) }}" class="btn btn-outline-secondary rounded-3">
                Kembali
            </a>

            <a href="{{ route('abk-laporan.export', $patroli) }}" class="btn btn-polairud">
                Export PDF
            </a>
        </div>
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

    @if($patroli->status === 'perbaiki' && $patroli->validasi_pimpinan_catatan)
        <div class="alert alert-warning rounded-4">
            <strong>Catatan Perbaikan dari Pimpinan:</strong><br>
            {{ $patroli->validasi_pimpinan_catatan }}
        </div>
    @endif

    <form  id="abk-form" action="{{ route('abk-laporan.update', $patroli) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="section-panel mb-4">
            <h5 class="section-title mb-3">A. Rencana Anggaran</h5>

            <div class="row g-3">
                @foreach($anggaranItems as $item)
                    <div class="col-md-6">
                        <label class="form-label">{{ $item }}</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input
                                type="number"
                                name="anggaran[{{ $key($item) }}]"
                                class="form-control"
                                value="{{ old('anggaran.' . $key($item), optional($anggaranMap->get($item))->nominal ?? 0) }}"
                                min="0"
                                step="1"
                            >
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="section-panel mb-4">
            <h5 class="section-title mb-3">B. Rencana Logistik / BBM / Oli</h5>

            <div class="row g-3">
                        @foreach($logistikItems as $item)
                            <div class="col-md-4">
                                <label class="form-label">{{ $item }}</label>
                                <div class="input-group">
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="logistik[{{ $key($item) }}]"
                                        class="form-control"
                                        value="{{ old('logistik.' . $key($item), optional($logistikMap->get($item))->jumlah_liter ?? 0) }}"
                                        min="0"
                                    >
                                    <span class="input-group-text">Liter</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="section-panel mb-4">
                    <div class="section-panel mb-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="section-title mb-0">
                    C. Kronologi Kegiatan
                </h5>

                <button
                    type="button"
                    class="btn btn-outline-primary rounded-3"
                    onclick="addKronologi()">
                    + Tambah Kronologi
                </button>

            </div>

            <div id="kronologi-wrapper">

                @foreach($laporan->kronologis ?? [] as $i => $item)

                    <div class="card p-3 mb-3">

                    <small class="text-muted">
                        {{ $item->waktu_input?->format('d/m/Y H:i') }}
                    </small>

                    <input
                        type="hidden"
                        name="kronologi[{{ $i }}][id]"
                        value="{{ $item->id }}">

                    <textarea
                        class="form-control mt-2"
                        rows="3"
                        name="kronologi[{{ $i }}][uraian]"
                    >{{ $item->uraian }}</textarea>

                </div>

                @endforeach

            </div>

        </div>
        </div>

        <div class="section-panel mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="section-title mb-0">D. Riksa Kapal</h5>
                    <small class="text-muted">
                        Tambahkan data pemeriksaan kapal. Jumlah riksa kapal tidak dibatasi.
                    </small>
                </div>

                <button type="button" class="btn btn-outline-primary rounded-3" onclick="addRiksa()">
                    + Tambah Riksa
                </button>
            </div>

            <div id="riksa-wrapper">
                @forelse($laporan->riksaKapals as $i => $riksa)
                    @include('abk-laporan.partials.riksa-item', [
                        'i' => $i,
                        'riksa' => $riksa
                    ])
                @empty
                    @include('abk-laporan.partials.riksa-item', [
                        'i' => 0,
                        'riksa' => null
                    ])
                @endforelse
            </div>
        </div>

        <template id="kronologi-template">

            <div class="card mb-3 p-3">

                <small class="text-muted">
                    Akan tercatat otomatis saat disimpan
                </small>

                <textarea
                    class="form-control mt-2"
                    rows="3"
                    name="kronologi[__INDEX__][uraian]"></textarea>

            </div>

        </template>

        <div class="section-panel mb-4">
            <h5 class="section-title mb-3">E. Hasil yang Dicapai</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Total Pengisian BBM</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="total_pengisian_bbm" class="form-control" value="{{ old('total_pengisian_bbm', $laporan->total_pengisian_bbm) }}" min="0">
                        <span class="input-group-text">Liter</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Total Stok BBM di Tangki</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="total_stock_bbm_tangki" class="form-control" value="{{ old('total_stock_bbm_tangki', $laporan->total_stock_bbm_tangki) }}" min="0">
                        <span class="input-group-text">Liter</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Total Jarak Tempuh</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="total_jarak_tempuh" class="form-control" value="{{ old('total_jarak_tempuh', $laporan->total_jarak_tempuh) }}" min="0">
                        <span class="input-group-text">NM / nmil</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Total Pemakaian BBM</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="total_pemakaian_bbm" class="form-control" value="{{ old('total_pemakaian_bbm', $laporan->total_pemakaian_bbm) }}" min="0">
                        <span class="input-group-text">Liter</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pemakaian BBM Selama Layar</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="pemakaian_bbm_selama_layar" class="form-control" value="{{ old('pemakaian_bbm_selama_layar', $laporan->pemakaian_bbm_selama_layar) }}" min="0">
                        <span class="input-group-text">Liter</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kecepatan Rata-rata</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="kecepatan_rata_rata" class="form-control" value="{{ old('kecepatan_rata_rata', $laporan->kecepatan_rata_rata) }}" min="0">
                        <span class="input-group-text">NM / nmil</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sisa BBM Selesai Patroli</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="sisa_bbm_selesai_patroli" class="form-control" value="{{ old('sisa_bbm_selesai_patroli', $laporan->sisa_bbm_selesai_patroli) }}" min="0">
                        <span class="input-group-text">Liter</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-panel mb-4">
            <h5 class="section-title mb-3">F. Lampiran</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Daftar Absensi Personel</label>
                    <input type="file" name="lampiran[absensi_personel]" class="form-control" accept=".pdf,application/pdf" multiple>
                    @foreach(
                        $laporan->lampirans
                            ->where('jenis', 'absensi_personel')
                        as $lampiran
                    )
                        <div class="d-flex align-items-center gap-2 mt-2">

                            <a
                                href="{{ asset('storage/'.$lampiran->file_path) }}"
                                target="_blank"
                                class="btn btn-sm btn-outline-primary">

                                📄 Lampiran {{ $loop->iteration }}

                            </a>

                            <a
                                href="{{ route('abk-laporan.lampiran.destroy', $lampiran) }}"
                                class="btn btn-sm btn-outline-danger"
                                onclick="
                                    event.preventDefault();

                                    if(confirm('Hapus lampiran ini?')) {
                                        document.getElementById(
                                            'hapus-lampiran-{{ $lampiran->id }}'
                                        ).submit();
                                    }
                                ">
                                Hapus
                            </a>

                        </div>

                        
                    @endforeach
                    <small class="text-muted">PDF maksimal 5MB.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Daftar Nama Personel</label>
                    <input type="file" name="lampiran[daftar_nama_personel]" class="form-control" accept=".pdf,application/pdf" multiple>
                    @foreach(
                        $laporan->lampirans
                            ->where('jenis', 'daftar_nama_personel')
                        as $lampiran
                    )

                        <div class="mt-2">

                            <a
                                href="{{ asset('storage/'.$lampiran->file_path) }}"
                                target="_blank"
                                class="btn btn-sm btn-outline-primary">

                                📄 Lampiran {{ $loop->iteration }}

                            </a>

                            <a
                                href="#"
                                class="btn btn-sm btn-outline-danger"
                                onclick="
                                    event.preventDefault();

                                    if(confirm('Hapus lampiran ini?')) {
                                        document.getElementById(
                                            'hapus-lampiran-{{ $lampiran->id }}'
                                        ).submit();
                                    }
                                ">
                                Hapus
                            </a>

                        </div>

                    @endforeach
                    <small class="text-muted">PDF maksimal 5MB.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Berita Acara Penyerahan Materil</label>
                    <input type="file" name="lampiran[berita_acara_penyerahan_materil]" class="form-control" accept=".pdf,application/pdf" multiple>
                    @foreach(
                        $laporan->lampirans
                            ->where('jenis', 'berita_acara_penyerahan_materil')
                        as $lampiran
                    )

                    <div class="d-flex align-items-center gap-2 mt-2">

                        <a
                            href="{{ asset('storage/'.$lampiran->file_path) }}"
                            target="_blank"
                            class="btn btn-sm btn-outline-primary">

                            📄 Lampiran {{ $loop->iteration }}

                        </a>

                        <a
                            href="#"
                            class="btn btn-sm btn-outline-danger"
                            onclick="
                                event.preventDefault();

                                if(confirm('Hapus lampiran ini?')) {
                                    document.getElementById(
                                        'hapus-lampiran-{{ $lampiran->id }}'
                                    ).submit();
                                }
                            ">
                            Hapus
                        </a>

                    </div>

                    @endforeach
                    <small class="text-muted">PDF maksimal 5MB.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dokumentasi Foto Pengisian Air Tawar</label>
                    <input type="file" name="lampiran[foto_pengisian_air_tawar]" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" multiple>
                    @foreach(
                        $laporan->lampirans
                            ->where('jenis', 'foto_pengisian_air_tawar')
                        as $lampiran
                    )

                    <div class="d-flex align-items-center gap-2 mt-2">

                        <a
                            href="{{ asset('storage/'.$lampiran->file_path) }}"
                            target="_blank"
                            class="btn btn-sm btn-outline-primary">

                            🖼 Foto {{ $loop->iteration }}

                        </a>

                        <a
                            href="#"
                            class="btn btn-sm btn-outline-danger"
                            onclick="
                                event.preventDefault();

                                if(confirm('Hapus lampiran ini?')) {
                                    document.getElementById(
                                        'hapus-lampiran-{{ $lampiran->id }}'
                                    ).submit();
                                }
                            ">
                            Hapus
                        </a>

                    </div>

                    @endforeach
                    <small class="text-muted">JPG, JPEG, PNG maksimal 5MB.</small>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-end gap-2 mb-4">
            <a href="{{ route('patroli.show', $patroli) }}" class="btn btn-outline-secondary rounded-3">
                Batal
            </a>

            <button type="submit" class="btn btn-polairud">
                Simpan Laporan SOP 14
            </button>
        </div>
    </form>

    <template id="riksa-template">
        @include('abk-laporan.partials.riksa-item', [
            'i' => '__INDEX__',
            'riksa' => null
        ])
    </template>

    <script>
        let riksaIndex = {{ max($laporan->riksaKapals->count(), 1) }};

        function addRiksa() {
            const template = document
                .getElementById('riksa-template')
                .innerHTML
                .replaceAll('__INDEX__', riksaIndex);

            document
                .getElementById('riksa-wrapper')
                .insertAdjacentHTML('beforeend', template);

            riksaIndex++;
        }
        
    </script>

    <script>

        let kronologiIndex = {{ $laporan->kronologis->count() }};

        function addKronologi()
        {
            const html =
                document
                    .getElementById('kronologi-template')
                    .innerHTML
                    .replaceAll('__INDEX__', kronologiIndex);

            document
                .getElementById('kronologi-wrapper')
                .insertAdjacentHTML('beforeend', html);

            kronologiIndex++;
        }

    </script>

    @foreach($laporan->lampirans as $lampiran)

    <form
        id="hapus-lampiran-{{ $lampiran->id }}"
        action="{{ route('abk-laporan.lampiran.destroy', $lampiran) }}"
        method="POST"
        style="display:none">

        @csrf
        @method('DELETE')

    </form>

    @endforeach

    
    @if(session('success'))
        <script>

            document.addEventListener(
                'DOMContentLoaded',
                () => {

                localStorage.removeItem(
                    'abk-sop14-{{ $patroli->id }}'
                );

            });

        </script>
    @endif

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            () => {

            const form =
                document.getElementById(
                    'abk-form'
                );

            if (!form) return;

            const storageKey =
                'abk-sop14-{{ $patroli->id }}';

            /*
            |-----------------------------------
            | RESTORE DATA
            |-----------------------------------
            */

            const savedData =
                localStorage.getItem(
                    storageKey
                );

            if (savedData) {

                try {

                    const data =
                        JSON.parse(savedData);

                    if(data.__kronologi_html){

                        document.getElementById(
                            'kronologi-wrapper'
                        ).innerHTML =
                            data.__kronologi_html;

                    }

                    if(data.__riksa_html){

                        document.getElementById(
                            'riksa-wrapper'
                        ).innerHTML =
                            data.__riksa_html;

                    }

                    Object.entries(data)
                        .forEach(([name,value]) => {

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

                } catch (e) {

                    console.error(
                        'Gagal restore draft',
                        e
                    );

                }

            }

            document.querySelectorAll(
                'textarea[name*="kronologi"]'
            ).forEach(textarea => {

                const saved =
                    localStorage.getItem(
                        textarea.name
                    );

                if(saved){

                    textarea.value = saved;

                }

            });

            /*
            |-----------------------------------
            | AUTO SAVE
            |-----------------------------------
            */

            function saveDraft()
            {
                const data = {};

                form.querySelectorAll(
                    'textarea, input, select'
                ).forEach(field => {

                    if (!field.name) return;

                    if (field.type === 'file') return;

                    data[field.name] =
                        field.value;

                });

                data.__kronologi_html =
                    document.getElementById(
                        'kronologi-wrapper'
                    ).innerHTML;

                data.__riksa_html =
                    document.getElementById(
                        'riksa-wrapper'
                    ).innerHTML;
                
                localStorage.setItem(
                    storageKey,
                    JSON.stringify(data)
                );

                document.querySelectorAll(
                    'textarea[name*="kronologi"]'
                ).forEach(textarea => {

                    localStorage.setItem(
                        textarea.name,
                        textarea.value
                    );

                });

            }

            document.querySelectorAll(
                'textarea[name*="kronologi"]'
            ).forEach(textarea => {

                textarea.addEventListener(
                    'keyup',
                    function(){

                        localStorage.setItem(
                            this.name,
                            this.value
                        );

                    }
                );

            });

            form.addEventListener(
                'input',
                saveDraft
            );

            form.addEventListener(
                'change',
                saveDraft
            );

        });

    </script>
@endsection