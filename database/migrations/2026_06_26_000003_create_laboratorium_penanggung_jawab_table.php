<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('laboratorium_penanggung_jawab')) {
            Schema::create('laboratorium_penanggung_jawab', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_laboratorium')->constrained('laboratorium', 'id_laboratorium')->cascadeOnDelete();
                $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['id_laboratorium', 'id_user'], 'lab_pj_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratorium_penanggung_jawab');
    }
};
