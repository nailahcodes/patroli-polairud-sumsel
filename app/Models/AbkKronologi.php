<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbkKronologi extends Model
{
    protected $fillable = [
        'abk_laporan_id',
        'uraian',
        'waktu_input',
    ];

    protected $casts = [
        'waktu_input' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(AbkLaporan::class);
    }
}