<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbkLogistik extends Model
{
    protected $fillable = [
        'abk_laporan_id',
        'jenis',
        'jumlah_liter',
    ];

    public function laporan()
    {
        return $this->belongsTo(AbkLaporan::class, 'abk_laporan_id');
    }
}