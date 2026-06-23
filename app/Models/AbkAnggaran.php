<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbkAnggaran extends Model
{
    protected $fillable = [
        'abk_laporan_id',
        'komponen',
        'nominal',
    ];

    public function laporan()
    {
        return $this->belongsTo(AbkLaporan::class, 'abk_laporan_id');
    }
}