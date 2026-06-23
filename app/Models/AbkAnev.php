<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbkAnev extends Model
{
    protected $fillable = [
        'patroli_id',
        'user_id',
        'pembuat_laporan_id',
        'hambatan',
        'kendala',
        'foto_anev',
        'status',
    ];

    public function patroli()
    {
        return $this->belongsTo(Patroli::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembuatLaporan()
    {
        return $this->belongsTo(User::class, 'pembuat_laporan_id');
    }

    public function getFotoAnevUrlAttribute()
    {
        if ($this->foto_anev) {
            return asset('storage/' . $this->foto_anev);
        }

        return null;
    }
}