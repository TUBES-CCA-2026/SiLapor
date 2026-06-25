<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';
    protected $primaryKey = 'id_pengaduan';

    protected $fillable = [
        'id_user', 'id_fasilitas', 'id_status_pengaduan',
        'deskripsi_kerusakan', 'tanggal_lapor',
        'status_pengaduan',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function fasilitas()
    {
        return $this->belongsTo(FasilitasLab::class, 'id_fasilitas', 'id_fasilitas');
    }

    public function statusData()
    {
        return $this->belongsTo(StatusPengaduan::class, 'id_status_pengaduan', 'id_status_pengaduan');
    }

    public function foto()
    {
        return $this->hasMany(PengaduanFoto::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function fotoUtama()
    {
        return $this->hasOne(PengaduanFoto::class, 'id_pengaduan', 'id_pengaduan')->oldestOfMany('id_foto');
    }

    public function tindakLanjut()
    {
        return $this->hasOne(TindakLanjut::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function setStatusPengaduanAttribute(string $value): void
    {
        $this->attributes['id_status_pengaduan'] = StatusPengaduan::idByKode($value);
    }

    public function getStatusPengaduanAttribute(): ?string
    {
        if ($this->relationLoaded('statusData')) {
            return $this->statusData?->kode_status;
        }

        return $this->statusData()->value('kode_status');
    }

    public function getFotoKerusakanAttribute(): ?string
    {
        if ($this->relationLoaded('fotoUtama')) {
            return $this->fotoUtama?->file_path;
        }

        return $this->foto()->oldest('id_foto')->value('file_path');
    }

    public function scopeStatusKode($query, string|array $kodeStatus)
    {
        $kodeStatus = (array) $kodeStatus;

        return $query->whereHas('statusData', fn ($q) => $q->whereIn('kode_status', $kodeStatus));
    }

    public function isGuestReport(): bool
    {
        return false;
    }
}
