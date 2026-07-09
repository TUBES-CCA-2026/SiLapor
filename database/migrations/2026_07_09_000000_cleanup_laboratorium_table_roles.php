<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratorium', function (Blueprint $table) {
            // Drop foreign key if exists
            try {
                $table->dropForeign(['id_penanggung_jawab']);
            } catch (\Exception $e) {
                // Ignore if not exists
            }
            
            // Drop columns
            $table->dropColumn(['id_penanggung_jawab', 'id_pendamping']);
        });
    }

    public function down(): void
    {
        Schema::table('laboratorium', function (Blueprint $table) {
            $table->foreignId('id_penanggung_jawab')->nullable()->after('id_koordinator')
                  ->constrained('users', 'id_user')->nullOnDelete();
            $table->text('id_pendamping')->nullable()->after('id_penanggung_jawab');
        });
    }
};
