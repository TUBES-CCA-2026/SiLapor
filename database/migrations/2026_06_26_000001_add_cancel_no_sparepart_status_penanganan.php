<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            ['kode_status' => 'CANCEL', 'nama_status' => 'Cancel'],
            ['kode_status' => 'NO SPAREPART', 'nama_status' => 'No Sparepart'],
        ] as $status) {
            DB::table('status_penanganan')->updateOrInsert(
                ['kode_status' => $status['kode_status']],
                ['nama_status' => $status['nama_status']]
            );
        }
    }

    public function down(): void
    {
        DB::table('status_penanganan')->whereIn('kode_status', ['CANCEL', 'NO SPAREPART'])->delete();
    }
};
