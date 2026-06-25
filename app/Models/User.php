<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama', 'email', 'password', 'role', 'phone',
        'nim', 'jurusan', 'peminatan', 'penanggung_jawab', 'foto',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ----- Relasi -----
    public function pengaduanDilaporkan()
    {
        return $this->hasMany(Pengaduan::class, 'id_user', 'id_user');
    }

    public function tindakLanjutDitugaskan()
    {
        return $this->hasMany(TindakLanjut::class, 'id_asisten', 'id_user');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_asisten', 'id_user');
    }

    // ----- Helper role -----
    public function isAsisten(): bool
    {
        return $this->role === 'asisten';
    }

    public function isLaboran(): bool
    {
        return $this->role === 'laboran';
    }

    public function isKoordinatorLab(): bool
    {
        return $this->role === 'koordinator_lab';
    }

    public function isKepalaLab(): bool
    {
        return $this->role === 'kepala_lab';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
