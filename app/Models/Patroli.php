<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patroli extends Model
{
    protected $fillable = [
        'nomor_sprin',
        'kapal_id',
        'wilayah_patroli',
        'tanggal_persiapan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
        'validasi_pimpinan_status',
        'validasi_pimpinan_catatan',
        'validasi_pimpinan_user_id',
        'validasi_pimpinan_at',
    ];

    protected $casts = [
        'tanggal_persiapan' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'validasi_pimpinan_at' => 'datetime',
    ];

    public function kapal()
    {
        return $this->belongsTo(Kapal::class, 'kapal_id');
    }

    public function sopProgress()
    {
        return $this->hasMany(SopProgress::class, 'patroli_id');
    }

    public function personels()
    {
        return $this->belongsToMany(User::class, 'patroli_user', 'patroli_id', 'user_id')
            ->withPivot('posisi')
            ->withTimestamps();
    }

    public function kronologis()
    {
        return $this->hasMany(Kronologi::class, 'patroli_id');
    }

    public function dokumens()
    {
        return $this->hasMany(DokumenPatroli::class, 'patroli_id');
    }

    public function dokumenPatrolis()
    {
        return $this->hasMany(DokumenPatroli::class, 'patroli_id');
    }

    public function fotos()
    {
        return $this->hasMany(FotoPatroli::class, 'patroli_id');
    }

    public function fotoPatrolis()
    {
        return $this->hasMany(FotoPatroli::class, 'patroli_id');
    }

    public function anggarans()
    {
        return $this->hasMany(AnggaranPatroli::class, 'patroli_id');
    }

    public function anggaranPatrolis()
    {
        return $this->hasMany(AnggaranPatroli::class, 'patroli_id');
    }

    public function logistiks()
    {
        return $this->hasMany(LogistikPatroli::class, 'patroli_id');
    }

    public function logistikPatrolis()
    {
        return $this->hasMany(LogistikPatroli::class, 'patroli_id');
    }

    public function abkLaporan()
    {
        return $this->hasOne(AbkLaporan::class, 'patroli_id');
    }

    public function abkAnev()
    {
        return $this->hasOne(AbkAnev::class, 'patroli_id');
    }

    public function validatorPimpinan()
    {
        return $this->belongsTo(User::class, 'validasi_pimpinan_user_id');
    }
}