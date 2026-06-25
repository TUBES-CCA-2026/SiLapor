<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPengiriman extends Model
{
    protected $table = 'status_pengiriman';
    protected $primaryKey = 'id_status_pengiriman';
    public $timestamps = false;

    protected $fillable = ['kode_status', 'nama_status'];

    public static function idByKode(string $kodeStatus): int
    {
        $namaStatus = match ($kodeStatus) {
            'sent' => 'Terkirim',
            'failed' => 'Gagal',
            default => $kodeStatus,
        };

        $status = static::firstOrCreate(
            ['kode_status' => $kodeStatus],
            ['nama_status' => $namaStatus]
        );

        return (int) $status->id_status_pengiriman;
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_status_pengiriman', 'id_status_pengiriman');
    }
}
