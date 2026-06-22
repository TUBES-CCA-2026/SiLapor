<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';
    protected $primaryKey = 'id_pengaduan';

    protected $fillable = [
        'foto_kerusakan', 'deskripsi_kerusakan', 'tanggal_lapor',
        'status_pengaduan', 'id_user', 'id_fasilitas',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function fasilitas()
    {
        return $this->belongsTo(FasilitasLab::class, 'id_fasilitas', 'id_fasilitas');
    }

    public function tindakLanjut()
    {
        return $this->hasOne(TindakLanjut::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function isGuestReport(): bool
    {
        return $this->id_user === null;
    }
}
