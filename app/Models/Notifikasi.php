<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false; // hanya created_at, tanpa updated_at

    protected $fillable = [
        'id_tindak_lanjut', 'id_asisten', 'email_tujuan',
        'status_pengiriman', 'tanggal_pengiriman', 'created_at',
    ];

    protected $casts = [
        'tanggal_pengiriman' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function tindakLanjut()
    {
        return $this->belongsTo(TindakLanjut::class, 'id_tindak_lanjut', 'id_tindak_lanjut');
    }

    public function asisten()
    {
        return $this->belongsTo(User::class, 'id_asisten', 'id_user');
    }
}
