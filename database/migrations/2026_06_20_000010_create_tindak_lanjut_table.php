<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id('id_tindak_lanjut');
            $table->foreignId('id_pengaduan')->constrained('pengaduan', 'id_pengaduan')->cascadeOnDelete();
            $table->foreignId('id_petugas')->constrained('users', 'id_user')->restrictOnDelete();
            $table->foreignId('id_status_penanganan')->constrained('status_penanganan', 'id_status_penanganan')->restrictOnDelete();
            $table->text('catatan_perbaikan')->nullable();
            $table->date('tanggal_penanganan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
