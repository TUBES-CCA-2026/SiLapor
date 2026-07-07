<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorium extends Model
{
    protected $table = 'laboratorium';
    protected $primaryKey = 'id_laboratorium';

    protected $fillable = [
        'nama_laboratorium', 'kode_laboratorium', 'lokasi',
        'id_koordinator', 'id_penanggung_jawab', 'id_pendamping',
        'kapasitas', 'keterangan',
    ];

    protected $casts = [
        'id_pendamping' => 'array',
    ];

    public function penanggungJawabUser()
    {
        return $this->belongsTo(User::class, 'id_penanggung_jawab', 'id_user');
    }

    public function pendampingUser()
    {
        return $this->belongsTo(User::class, 'id_penanggung_jawab', 'id_user')->whereRaw('1 = 0');
    }

    public function getPendampingUsersAttribute()
    {
        $ids = $this->id_pendamping;
        if (empty($ids)) {
            return collect();
        }
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?: explode(',', $ids);
        }
        return User::whereIn('id_user', (array)$ids)->get();
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'id_koordinator', 'id_user');
    }

    public function fasilitas()
    {
        return $this->hasMany(FasilitasLab::class, 'id_laboratorium', 'id_laboratorium')
            ->activeQr();
    }

    public function semuaFasilitas()
    {
        return $this->hasMany(FasilitasLab::class, 'id_laboratorium', 'id_laboratorium');
    }

    public function penanggungJawabs()
    {
        return $this->belongsToMany(User::class, 'laboratorium_penanggung_jawab', 'id_laboratorium', 'id_user')
            ->withTimestamps();
    }
}
