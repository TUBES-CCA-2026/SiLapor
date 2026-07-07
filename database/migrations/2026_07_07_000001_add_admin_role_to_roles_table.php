<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'admin'],
            ['nama_role' => 'admin']
        );
    }

    public function down(): void
    {
        DB::table('roles')->where('nama_role', 'admin')->delete();
    }
};
