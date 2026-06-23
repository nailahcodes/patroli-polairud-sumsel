<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiksaKapal extends Model
{
    protected $fillable = [
        'abk_laporan_id',
        'nama_kapal',
        'nama_nahkoda',
        'dari_tujuan',
        'muatan',
        'titik_koordinat',
        'kategori',
        'penjelasan',
        'foto_riksa',
        'foto_binluh',
        'surat_hasil_pemeriksaan',
    ];

    public function laporan()
    {
        return $this->belongsTo(AbkLaporan::class, 'abk_laporan_id');
    }

    public function getFotoRiksaUrlAttribute()
    {
        return $this->foto_riksa ? asset('storage/' . $this->foto_riksa) : null;
    }

    public function getFotoBinluhUrlAttribute()
    {
        return $this->foto_binluh ? asset('storage/' . $this->foto_binluh) : null;
    }

    public function getSuratHasilPemeriksaanUrlAttribute()
    {
        return $this->surat_hasil_pemeriksaan ? asset('storage/' . $this->surat_hasil_pemeriksaan) : null;
    }
}