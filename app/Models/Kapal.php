<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kapal extends Model
{
    protected $fillable = [
        'kelompok',
        'kode_kapal',
        'komandan_id',
        'zona_patroli',
        'wilayah_patroli',
        'komandan_kapal',
        'status',
    ];

    public function patrolis()
    {
        return $this->hasMany(Patroli::class);
    }

    public function komandan()
    {
        return $this->belongsTo(User::class, 'komandan_id');
    }

    public function getNamaKomandanAttribute()
    {
        return $this->komandan
            ? $this->komandan->nama
            : $this->komandan_kapal;
    }
}
