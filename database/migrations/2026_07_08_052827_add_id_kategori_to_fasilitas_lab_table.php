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
        Schema::table('fasilitas_lab', function (Blueprint $table) {
            $table->foreignId('id_kategori')->nullable()->after('id_laboratorium')
                  ->constrained('kategori_fasilitas', 'id_kategori')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fasilitas_lab', function (Blueprint $table) {
            $table->dropForeign(['id_kategori']);
            $table->dropColumn(['id_kategori']);
        });
    }
};
