<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama',
        'nrp',
        'pangkat',
        'jabatan',
        'role',
        'kapal_id',
        'status',
        'profile_photo',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function kapal()
    {
        return $this->belongsTo(Kapal::class);
    }

    public function patrolis()
    {
        return $this->belongsToMany(Patroli::class, 'patroli_user')
            ->withPivot('posisi')
            ->withTimestamps();
    }

    public function patroliSebagaiKomandan()
    {
        return $this->hasMany(Patroli::class, 'komandan_id');
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        return null;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
