<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan_foto', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaduan_foto', 'file_base64')) {
                $table->longText('file_base64')->nullable();
            }

            if (!Schema::hasColumn('pengaduan_foto', 'mime_type')) {
                $table->string('mime_type', 100)->nullable();
            }

            if (!Schema::hasColumn('pengaduan_foto', 'original_name')) {
                $table->string('original_name', 255)->nullable();
            }

            if (!Schema::hasColumn('pengaduan_foto', 'file_size')) {
                $table->unsignedInteger('file_size')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan_foto', function (Blueprint $table) {
            if (Schema::hasColumn('pengaduan_foto', 'file_size')) {
                $table->dropColumn('file_size');
            }

            if (Schema::hasColumn('pengaduan_foto', 'original_name')) {
                $table->dropColumn('original_name');
            }

            if (Schema::hasColumn('pengaduan_foto', 'mime_type')) {
                $table->dropColumn('mime_type');
            }

            if (Schema::hasColumn('pengaduan_foto', 'file_base64')) {
                $table->dropColumn('file_base64');
            }
        });
    }
};
