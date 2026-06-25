<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_role');
            $table->string('nama_role', 50)->unique();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['nama_role' => 'asisten', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'laboran', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'koordinator_lab', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'kepala_lab', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
