<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas_lab', function (Blueprint $table) {
            $table->id('id_fasilitas');
            $table->string('nama_fasilitas', 120);

            // GANTI dari kolom varchar 'laboratorium' -> sekarang foreign key
            // ke tabel laboratorium (relasi proper, bukan teks bebas lagi).
            $table->foreignId('id_laboratorium')->constrained('laboratorium', 'id_laboratorium')->cascadeOnDelete();

            $table->string('no_fasilitas', 120)->nullable(); // contoh: PC-LAB1-01
            // token unik yang di-encode ke dalam QR (BUKAN id_fasilitas langsung, demi keamanan)
            $table->string('qr_code', 255)->unique();
            $table->dateTime('qr_generated_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas_lab');
    }
};
