<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'id_role',
        'nama',
        'email',
        'password',
        'phone',
        'foto',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roleData()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'id_user', 'id_user');
    }

    public function laboratoriumDikoordinatori()
    {
        return $this->hasMany(Laboratorium::class, 'id_koordinator', 'id_user');
    }

    public function laboratoriumPenanggungJawab()
    {
        return $this->belongsToMany(Laboratorium::class, 'laboratorium_penanggung_jawab', 'id_user', 'id_laboratorium')
            ->withTimestamps();
    }

    public function pengaduanDilaporkan()
    {
        return $this->hasMany(Pengaduan::class, 'id_user', 'id_user');
    }

    public function tindakLanjutDitugaskan()
    {
        return $this->hasMany(TindakLanjut::class, 'id_petugas', 'id_user');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user_penerima', 'id_user');
    }

    public function setRoleAttribute(string $value): void
    {
        $this->attributes['id_role'] = Role::idByName($value);
    }

    public function getRoleAttribute(): ?string
    {
        if ($this->relationLoaded('roleData')) {
            return $this->roleData?->nama_role;
        }

        return $this->roleData()->value('nama_role');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'kepala_lab' => 'Kepala Lab',
            'koordinator_lab' => 'Koordinator Lab',
            'laboran' => 'Laboran',
            'asisten' => 'Asisten Lab',
                        default => 'User',
        };
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->foto && $this->getKey()) {
            $version = $this->updated_at ? '?v=' . $this->updated_at->timestamp : '';

            return url('/users/' . $this->getKey() . '/foto' . $version);
        }

        $name = urlencode($this->nama ?: $this->email ?: 'User');

        return "https://ui-avatars.com/api/?name={$name}&background=FFFFFF&color=0090F5";
    }

    public function getNimAttribute(): ?string
    {
        return $this->profile?->nim;
    }

    public function getJurusanAttribute(): ?string
    {
        return $this->profile?->jurusan;
    }

    public function getPeminatanAttribute(): ?string
    {
        return $this->profile?->peminatan;
    }

    public function getPenanggungJawabAttribute(): ?string
    {
        return $this->profile?->penanggung_jawab;
    }

    public function getNameAttribute(): ?string
    {
        return $this->nama;
    }

    public function getNoHpAttribute(): ?string
    {
        return $this->phone;
    }

    public function getPjAttribute(): ?string
    {
        return $this->penanggung_jawab;
    }

    public function scopeRole($query, string $role)
    {
        return $query->whereHas('roleData', fn ($q) => $q->where('nama_role', $role));
    }

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
        return false;
    }
}
