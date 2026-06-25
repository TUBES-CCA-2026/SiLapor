<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teknisi extends Model
{
    // Pastikan nama tabel di database sesuai dengan ini
    protected $table = 'teknisi';
    
    // Menentukan primary key agar Laravel mengenali id_teknisi
    protected $primaryKey = 'id_teknisi';
    
    // Mengizinkan semua kolom untuk diisi (mass assignment)
    protected $guarded = [];

    // Relasi ke tabel tindak lanjut
    public function tindakLanjut()
    {
        return $this->hasMany(TindakLanjut::class, 'id_teknisi', 'id_teknisi');
    }
}