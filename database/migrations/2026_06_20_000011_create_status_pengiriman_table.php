<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_pengiriman', function (Blueprint $table) {
            $table->id('id_status_pengiriman');
            $table->string('kode_status', 30)->unique();
            $table->string('nama_status', 100);
        });

        DB::table('status_pengiriman')->insert([
            ['kode_status' => 'sent', 'nama_status' => 'Terkirim'],
            ['kode_status' => 'failed', 'nama_status' => 'Gagal'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('status_pengiriman');
    }
};
