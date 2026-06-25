<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanFoto extends Model
{
    protected $table = 'pengaduan_foto';
    protected $primaryKey = 'id_foto';
    public $timestamps = false;

    protected $fillable = [
        'id_pengaduan', 'file_path', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    }
}
