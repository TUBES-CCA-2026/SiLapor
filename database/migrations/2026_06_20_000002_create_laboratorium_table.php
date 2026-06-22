<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laboratorium', function (Blueprint $table) {
            $table->id('id_laboratorium');
            $table->string('nama_laboratorium', 120); // pindahan dari fasilitas_lab.laboratorium
            $table->string('kode_laboratorium', 20)->nullable(); // misal "LAB-RPL", "LAB-JAR"
            $table->string('lokasi', 120)->nullable(); // misal "Gedung A Lantai 2"

            // FK ke users.id_user -> siapa koordinator_lab penanggung jawab lab ini.
            // Nullable: lab boleh dibuat dulu, koordinatornya ditentukan menyusul.
            $table->foreignId('id_koordinator')->nullable()
                ->constrained('users', 'id_user')->nullOnDelete();

            $table->integer('kapasitas')->nullable(); // jumlah unit/komputer di lab
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratorium');
    }
};
