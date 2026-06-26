<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasLab extends Model
{
    protected $table = 'fasilitas_lab';
    protected $primaryKey = 'id_fasilitas';

    protected $fillable = [
        'nama_fasilitas', 'id_laboratorium', 'no_fasilitas',
        'qr_code', 'qr_generated_date', 'qr_deleted_at',
    ];

    protected $casts = [
        'qr_generated_date' => 'datetime',
        'qr_deleted_at' => 'datetime',
    ];

    public function laboratorium()
    {
        return $this->belongsTo(Laboratorium::class, 'id_laboratorium', 'id_laboratorium');
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'id_fasilitas', 'id_fasilitas');
    }

    // URL unik yang akan di-encode ke dalam gambar QR.
    // Host diambil dari request aktif agar QR tetap bisa dibuka dari HP saat server lokal
    // diakses memakai IP jaringan, bukan 127.0.0.1.
    public function scanUrl(): string
    {
        return request()->getSchemeAndHttpHost() . route('pengaduan.qr.create', ['qr_code' => $this->qr_code], false);
    }
}
