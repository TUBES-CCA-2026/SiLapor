<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->foreignId('id_teknisi')->nullable()->after('id_petugas')
                  ->constrained('users', 'id_user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->dropForeign(['id_teknisi']);
            $table->dropColumn('id_teknisi');
        });
    }
};
