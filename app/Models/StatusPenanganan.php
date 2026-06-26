<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPenanganan extends Model
{
    protected $table = 'status_penanganan';
    protected $primaryKey = 'id_status_penanganan';
    public $timestamps = false;

    protected $fillable = ['kode_status', 'nama_status'];

    public static function idByKode(string $kodeStatus): int
    {
        $namaStatus = match ($kodeStatus) {
            'ON PROGRES' => 'On Progress',
            'DONE' => 'Selesai',
            'CANCEL' => 'Cancel',
            'NO SPAREPART' => 'No Sparepart',
            default => $kodeStatus,
        };

        $status = static::firstOrCreate(
            ['kode_status' => $kodeStatus],
            ['nama_status' => $namaStatus]
        );

        return (int) $status->id_status_penanganan;
    }

    public function tindakLanjut()
    {
        return $this->hasMany(TindakLanjut::class, 'id_status_penanganan', 'id_status_penanganan');
    }
}
