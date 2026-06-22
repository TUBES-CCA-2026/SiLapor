<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakLanjut extends Model
{
    protected $table = 'tindak_lanjut';
    protected $primaryKey = 'id_tindak_lanjut';

    protected $fillable = [
        'id_pengaduan', 'id_user', 'catatan_perbaikan',
        'tanggal_penanganan', 'status_penanganan', 'id_asisten',
    ];

    protected $casts = [
        'tanggal_penanganan' => 'date',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function penugas()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function asisten()
    {
        return $this->belongsTo(User::class, 'id_asisten', 'id_user');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_tindak_lanjut', 'id_tindak_lanjut');
    }
}
