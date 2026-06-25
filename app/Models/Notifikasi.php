<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false;

    protected $fillable = [
        'id_tindak_lanjut', 'id_user_penerima', 'id_status_pengiriman',
        'tanggal_pengiriman', 'created_at',
        'id_asisten', 'status_pengiriman',
    ];

    protected $casts = [
        'tanggal_pengiriman' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function tindakLanjut()
    {
        return $this->belongsTo(TindakLanjut::class, 'id_tindak_lanjut', 'id_tindak_lanjut');
    }

    public function userPenerima()
    {
        return $this->belongsTo(User::class, 'id_user_penerima', 'id_user');
    }

    public function asisten()
    {
        return $this->belongsTo(User::class, 'id_user_penerima', 'id_user');
    }

    public function statusData()
    {
        return $this->belongsTo(StatusPengiriman::class, 'id_status_pengiriman', 'id_status_pengiriman');
    }

    public function setStatusPengirimanAttribute(string $value): void
    {
        $this->attributes['id_status_pengiriman'] = StatusPengiriman::idByKode($value);
    }

    public function getStatusPengirimanAttribute(): ?string
    {
        if ($this->relationLoaded('statusData')) {
            return $this->statusData?->kode_status;
        }

        return $this->statusData()->value('kode_status');
    }

    public function setIdAsistenAttribute(int|string|null $value): void
    {
        if ($value !== null) {
            $this->attributes['id_user_penerima'] = $value;
        }
    }

    public function getIdAsistenAttribute(): ?int
    {
        return $this->id_user_penerima;
    }

    public function getEmailTujuanAttribute(): ?string
    {
        return $this->userPenerima?->email;
    }
}
