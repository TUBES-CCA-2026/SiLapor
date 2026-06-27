<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPengaduan extends Model
{
    protected $table = 'status_pengaduan';
    protected $primaryKey = 'id_status_pengaduan';
    public $timestamps = false;

    protected $fillable = ['kode_status', 'nama_status'];

    public static function idByKode(string $kodeStatus): int
    {
        $namaStatus = match ($kodeStatus) {
            'NEW' => 'New',
            'HANDLED' => 'On Progress',
            'DONE' => 'Done',
            'CANCEL' => 'Cancel',
            'NO_SPAREPART' => 'No Sparepart',
            default => $kodeStatus,
        };

        $status = static::firstOrCreate(
            ['kode_status' => $kodeStatus],
            ['nama_status' => $namaStatus]
        );

        return (int) $status->id_status_pengaduan;
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'id_status_pengaduan', 'id_status_pengaduan');
    }
}
