<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan_foto', function (Blueprint $table) {
            $table->id('id_foto');
            $table->foreignId('id_pengaduan')->constrained('pengaduan', 'id_pengaduan')->cascadeOnDelete();
            $table->string('file_path', 255);
            $table->longText('file_base64')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_foto');
    }
};
