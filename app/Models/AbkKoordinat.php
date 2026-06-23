<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbkKoordinat extends Model
{
    protected $fillable = [
        'abk_laporan_id',
        'jenis',
        'koordinat',
        'keterangan',
    ];

    public function laporan()
    {
        return $this->belongsTo(AbkLaporan::class, 'abk_laporan_id');
    }
}