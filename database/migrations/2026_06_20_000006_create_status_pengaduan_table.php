<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_pengaduan', function (Blueprint $table) {
            $table->id('id_status_pengaduan');
            $table->string('kode_status', 30)->unique();
            $table->string('nama_status', 100);
        });

        DB::table('status_pengaduan')->insert([
            ['kode_status' => 'NEW', 'nama_status' => 'Baru'],
            ['kode_status' => 'HANDLED', 'nama_status' => 'Dalam Penanganan'],
            ['kode_status' => 'DONE', 'nama_status' => 'Selesai'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('status_pengaduan');
    }
};
