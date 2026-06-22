<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorium extends Model
{
    protected $table = 'laboratorium';
    protected $primaryKey = 'id_laboratorium';

    protected $fillable = [
        'nama_laboratorium', 'kode_laboratorium', 'lokasi',
        'id_koordinator', 'kapasitas', 'keterangan',
    ];

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'id_koordinator', 'id_user');
    }

    public function fasilitas()
    {
        return $this->hasMany(FasilitasLab::class, 'id_laboratorium', 'id_laboratorium');
    }
}
