<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id('id_pengaduan');
            $table->foreignId('id_user')->constrained('users', 'id_user')->restrictOnDelete();
            $table->foreignId('id_fasilitas')->constrained('fasilitas_lab', 'id_fasilitas')->cascadeOnDelete();
            $table->foreignId('id_status_pengaduan')->constrained('status_pengaduan', 'id_status_pengaduan')->restrictOnDelete();
            $table->text('deskripsi_kerusakan');
            $table->date('tanggal_lapor');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
