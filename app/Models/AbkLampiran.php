<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbkLampiran extends Model
{
    protected $fillable = [
        'abk_laporan_id',
        'jenis',
        'file_path',
    ];

    public function laporan()
    {
        return $this->belongsTo(AbkLaporan::class, 'abk_laporan_id');
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}