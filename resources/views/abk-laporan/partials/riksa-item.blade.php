<div class="riksa-item section-panel mb-3">
    <input type="hidden" name="riksa[{{ $i }}][id]" value="{{ optional($riksa)->id }}">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <strong>Data Riksa Kapal</strong>

        <button type="button" class="btn btn-sm btn-outline-danger rounded-3" onclick="this.closest('.riksa-item').remove()">
            Hapus
        </button>
    </div>

    @if($riksa)
        <div class="alert alert-light border rounded-4">
            <strong>File lama:</strong><br>

            @if($riksa->foto_riksa_url)
                <a href="{{ $riksa->foto_riksa_url }}" target="_blank" class="small d-inline-block me-2">
                    Foto Riksa
                </a>
            @endif

            @if($riksa->foto_binluh_url)
                <a href="{{ $riksa->foto_binluh_url }}" target="_blank" class="small d-inline-block me-2">
                    Foto Binluh
                </a>
            @endif

            @if($riksa->surat_hasil_pemeriksaan_url)
                <a href="{{ $riksa->surat_hasil_pemeriksaan_url }}" target="_blank" class="small d-inline-block me-2">
                    Surat Hasil Pemeriksaan
                </a>
            @endif
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama Kapal Riksa</label>
            <input type="text" name="riksa[{{ $i }}][nama_kapal]" class="form-control" value="{{ old("riksa.$i.nama_kapal", optional($riksa)->nama_kapal) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Nama Nahkoda</label>
            <input type="text" name="riksa[{{ $i }}][nama_nahkoda]" class="form-control" value="{{ old("riksa.$i.nama_nahkoda", optional($riksa)->nama_nahkoda) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Dari / Tujuan</label>
            <input type="text" name="riksa[{{ $i }}][dari_tujuan]" class="form-control" value="{{ old("riksa.$i.dari_tujuan", optional($riksa)->dari_tujuan) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Muatan</label>
            <input type="text" name="riksa[{{ $i }}][muatan]" class="form-control" value="{{ old("riksa.$i.muatan", optional($riksa)->muatan) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Titik Koordinat Riksa</label>
            <input type="text" name="riksa[{{ $i }}][titik_koordinat]" class="form-control" value="{{ old("riksa.$i.titik_koordinat", optional($riksa)->titik_koordinat) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <select name="riksa[{{ $i }}][kategori]" class="form-select">
                <option value="aman" {{ old("riksa.$i.kategori", optional($riksa)->kategori ?? 'aman') === 'aman' ? 'selected' : '' }}>Aman</option>
                <option value="tindak_pidana" {{ old("riksa.$i.kategori", optional($riksa)->kategori) === 'tindak_pidana' ? 'selected' : '' }}>Tindak Pidana</option>
                <option value="pelanggaran" {{ old("riksa.$i.kategori", optional($riksa)->kategori) === 'pelanggaran' ? 'selected' : '' }}>Pelanggaran Pelayaran</option>
            </select>
        </div>

        <div class="col-md-12">
            <label class="form-label">Penjelasan</label>
            <textarea name="riksa[{{ $i }}][penjelasan]" class="form-control" rows="3">{{ old("riksa.$i.penjelasan", optional($riksa)->penjelasan) }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Foto Riksa Kapal</label>
            <input type="file" name="riksa[{{ $i }}][foto_riksa]" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        </div>

        <div class="col-md-4">
            <label class="form-label">Foto Binluh</label>
            <input type="file" name="riksa[{{ $i }}][foto_binluh]" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        </div>

        <div class="col-md-4">
            <label class="form-label">Surat Hasil Pemeriksaan Kapal</label>
            <input type="file" name="riksa[{{ $i }}][surat_hasil_pemeriksaan]" class="form-control" accept=".pdf,application/pdf">
        </div>
    </div>
</div>