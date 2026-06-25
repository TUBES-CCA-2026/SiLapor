<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_penanganan', function (Blueprint $table) {
            $table->id('id_status_penanganan');
            $table->string('kode_status', 30)->unique();
            $table->string('nama_status', 100);
        });

        DB::table('status_penanganan')->insert([
            ['kode_status' => 'ON PROGRES', 'nama_status' => 'On Progress'],
            ['kode_status' => 'DONE', 'nama_status' => 'Selesai'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('status_penanganan');
    }
};
