<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakLanjut extends Model
{
    protected $table = 'tindak_lanjut';
    protected $primaryKey = 'id_tindak_lanjut';

    protected $fillable = [
        'id_pengaduan', 'id_petugas', 'id_teknisi', 'id_status_penanganan',
        'catatan_perbaikan', 'tanggal_penanganan',
        'status_penanganan', 'id_asisten',
    ];

    protected $casts = [
        'tanggal_penanganan' => 'date',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas', 'id_user');
    }

    public function asisten()
    {
        return $this->belongsTo(User::class, 'id_petugas', 'id_user');
    }

    public function penugas()
    {
        return $this->belongsTo(User::class, 'id_petugas', 'id_user');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'id_teknisi', 'id_user');
    }

    public function statusData()
    {
        return $this->belongsTo(StatusPenanganan::class, 'id_status_penanganan', 'id_status_penanganan');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_tindak_lanjut', 'id_tindak_lanjut');
    }

    public function setStatusPenangananAttribute(string $value): void
    {
        $this->attributes['id_status_penanganan'] = StatusPenanganan::idByKode($value);
    }

    public function getStatusPenangananAttribute(): ?string
    {
        if ($this->relationLoaded('statusData')) {
            return $this->statusData?->kode_status;
        }

        return $this->statusData()->value('kode_status');
    }

    public function setIdAsistenAttribute(int|string|null $value): void
    {
        if ($value !== null) {
            $this->attributes['id_petugas'] = $value;
        }
    }

    public function getIdAsistenAttribute(): ?int
    {
        return $this->id_petugas;
    }

    public function scopeStatusKode($query, string|array $kodeStatus)
    {
        $kodeStatus = (array) $kodeStatus;

        return $query->whereHas('statusData', fn ($q) => $q->whereIn('kode_status', $kodeStatus));
    }
}
