<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->foreignId('id_tindak_lanjut')->constrained('tindak_lanjut', 'id_tindak_lanjut')->cascadeOnDelete();
            $table->foreignId('id_user_penerima')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('id_status_pengiriman')->constrained('status_pengiriman', 'id_status_pengiriman')->restrictOnDelete();
            $table->dateTime('tanggal_pengiriman')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
