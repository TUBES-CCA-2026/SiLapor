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
            $table->foreignId('id_laboratorium')->constrained('laboratorium', 'id_laboratorium')->cascadeOnDelete();
            $table->string('nama_fasilitas', 120);
            $table->string('no_fasilitas', 120)->nullable();
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
