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
            $table->string('foto_kerusakan', 120); // path file foto kerusakan
            $table->text('deskripsi_kerusakan');
            $table->date('tanggal_lapor');
            $table->enum('status_pengaduan', ['NEW', 'HANDLED', 'DONE'])->default('NEW');

            // CATATAN PENTING: di ERD asli kolom ini terlihat wajib (NOT NULL),
            // tapi karena ada fitur lapor sebagai GUEST (tanpa login) lewat scan QR,
            // kolom ini dibuat nullable. Kalau guest yang lapor, id_user akan NULL.
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete();

            $table->foreignId('id_fasilitas')->constrained('fasilitas_lab', 'id_fasilitas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
