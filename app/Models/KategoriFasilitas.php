<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriFasilitas extends Model
{
    protected $table = 'kategori_fasilitas';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
    ];

    public function fasilitas()
    {
        return $this->hasMany(FasilitasLab::class, 'id_kategori', 'id_kategori');
    }
}
