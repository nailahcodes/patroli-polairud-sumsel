<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kronologi extends Model
{
    protected $fillable = [
        'patroli_id',
        'tanggal',
        'jam_wib',
        'jenis_kegiatan',
        'titik_koordinat',
        'lokasi',
        'deskripsi',
        'nama_kapal_diperiksa',
        'nahkoda',
        'asal_tujuan',
        'muatan',
        'keterangan_pemeriksaan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function patroli()
    {
        return $this->belongsTo(Patroli::class);
    }
}