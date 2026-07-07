<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitas_lab', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['id_laboratorium']);
        });

        Schema::table('fasilitas_lab', function (Blueprint $table) {
            // Make column nullable
            $table->unsignedBigInteger('id_laboratorium')->nullable()->change();
            
            // Re-add foreign key with nullOnDelete()
            $table->foreign('id_laboratorium')
                ->references('id_laboratorium')
                ->on('laboratorium')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas_lab', function (Blueprint $table) {
            $table->dropForeign(['id_laboratorium']);
        });

        Schema::table('fasilitas_lab', function (Blueprint $table) {
            $table->unsignedBigInteger('id_laboratorium')->nullable(false)->change();
            $table->foreign('id_laboratorium')
                ->references('id_laboratorium')
                ->on('laboratorium')
                ->cascadeOnDelete();
        });
    }
};
