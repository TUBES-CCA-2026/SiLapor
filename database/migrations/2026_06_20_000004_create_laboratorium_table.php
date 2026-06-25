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
            $table->string('nama_laboratorium', 120);
            $table->string('kode_laboratorium', 20)->nullable();
            $table->string('lokasi', 120)->nullable();
            $table->foreignId('id_koordinator')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->integer('kapasitas')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratorium');
    }
};
