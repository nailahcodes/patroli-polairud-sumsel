<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AbkKronologi;

class AbkLaporan extends Model
{
    protected $fillable = [
        'patroli_id',
        'user_id',
        'total_pengisian_bbm',
        'total_stock_bbm_tangki',
        'total_jarak_tempuh',
        'total_pemakaian_bbm',
        'pemakaian_bbm_selama_layar',
        'kecepatan_rata_rata',
        'sisa_bbm_selesai_patroli',
        'status',
        'catatan',
    ];

    public function patroli()
    {
        return $this->belongsTo(Patroli::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function anggarans()
    {
        return $this->hasMany(AbkAnggaran::class);
    }

    public function logistiks()
    {
        return $this->hasMany(AbkLogistik::class);
    }

    public function koordinats()
    {
        return $this->hasMany(AbkKoordinat::class);
    }

    public function riksaKapals()
    {
        return $this->hasMany(RiksaKapal::class);
    }

    public function lampirans()
    {
        return $this->hasMany(AbkLampiran::class);
    }

    public function kronologis()
    {
        return $this->hasMany(AbkKronologi::class)
        ->orderBy('waktu_input');
    }
}