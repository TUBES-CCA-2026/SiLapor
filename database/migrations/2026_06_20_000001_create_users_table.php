<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama', 120);
            $table->string('email', 120)->unique();
            $table->string('password', 120);
            $table->enum('role', ['asisten', 'laboran', 'koordinator_lab', 'kepala_lab', 'admin']);
            $table->string('phone', 15)->nullable();
            $table->string('nim', 12)->nullable();
            $table->string('jurusan', 20)->nullable();
            $table->string('peminatan', 20)->nullable();
            $table->string('penanggung_jawab', 20)->nullable();
            // tambahan wajib untuk Auth bawaan Laravel (Remember Me & reset password)
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
