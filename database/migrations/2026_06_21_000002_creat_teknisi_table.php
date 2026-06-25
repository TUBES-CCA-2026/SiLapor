<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_teknisi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan'); // Contoh: TDL-001
            $table->string('lokasi_masalah'); // Contoh: Lab. Computer Network
            $table->date('tanggal_lapor');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['On Progress', 'Pending', 'Done'])->default('On Progress');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_teknisi');
    }
};