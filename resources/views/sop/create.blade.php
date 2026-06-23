@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Tambah Tahapan SOP</h2>
            <p class="text-muted mb-0">
                Tambahkan tahapan standar operasional prosedur patroli.
            </p>
        </div>

        <a href="{{ route('sop.index') }}" class="btn btn-outline-secondary rounded-3">
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
        <form action="{{ route('sop.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan') }}" min="1" required>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Pelaksana</label>
                    <input type="text" name="pelaksana" class="form-control" value="{{ old('pelaksana') }}" required>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Waktu Standar</label>
                    <input type="text" name="waktu" class="form-control" value="{{ old('waktu') }}" placeholder="Contoh: 15 menit / 1 jam / 8 hari" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Tahapan SOP</label>
                    <textarea name="tahapan" class="form-control" rows="3" required>{{ old('tahapan') }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Kelengkapan</label>
                    <textarea name="kelengkapan" class="form-control" rows="3">{{ old('kelengkapan') }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Output</label>
                    <textarea name="output" class="form-control" rows="3">{{ old('output') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('sop.index') }}" class="btn btn-light rounded-3">
                    Batal
                </a>
                <button class="btn btn-polairud">
                    Simpan SOP
                </button>
            </div>
        </form>
    </div>
@endsection