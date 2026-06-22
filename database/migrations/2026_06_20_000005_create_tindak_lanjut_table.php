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

            // id_user = koordinator/penanggung jawab yang MENUGASKAN perbaikan
            $table->foreignId('id_user')->constrained('users', 'id_user');

            $table->text('catatan_perbaikan')->nullable();
            $table->date('tanggal_penanganan')->nullable();
            $table->enum('status_penanganan', ['ON PROGRES', 'DONE'])->default('ON PROGRES');

            // id_asisten = asisten yang DITUGASKAN memperbaiki
            $table->foreignId('id_asisten')->nullable()->constrained('users', 'id_user')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
