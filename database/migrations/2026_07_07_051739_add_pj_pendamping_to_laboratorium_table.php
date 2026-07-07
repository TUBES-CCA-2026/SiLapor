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
        Schema::table('laboratorium', function (Blueprint $table) {
            $table->foreignId('id_penanggung_jawab')->nullable()->after('id_koordinator')
                  ->constrained('users', 'id_user')->nullOnDelete();
            $table->foreignId('id_pendamping')->nullable()->after('id_penanggung_jawab')
                  ->constrained('users', 'id_user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('laboratorium', function (Blueprint $table) {
            $table->dropForeign(['id_penanggung_jawab']);
            $table->dropForeign(['id_pendamping']);
            $table->dropColumn(['id_penanggung_jawab', 'id_pendamping']);
        });
    }
};
