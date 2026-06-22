<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasLab extends Model
{
    protected $table = 'fasilitas_lab';
    protected $primaryKey = 'id_fasilitas';

    protected $fillable = [
        'nama_fasilitas', 'id_laboratorium', 'no_fasilitas',
        'qr_code', 'qr_generated_date',
    ];

    protected $casts = [
        'qr_generated_date' => 'datetime',
    ];

    public function laboratorium()
    {
        return $this->belongsTo(Laboratorium::class, 'id_laboratorium', 'id_laboratorium');
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'id_fasilitas', 'id_fasilitas');
    }

    // URL unik yang akan di-encode ke dalam gambar QR
    public function scanUrl(): string
    {
        return route('pengaduan.qr.create', ['qr_code' => $this->qr_code]);
    }
}
