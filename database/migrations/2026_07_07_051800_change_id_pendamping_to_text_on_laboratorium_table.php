<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratorium', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['id_pendamping']);
        });

        Schema::table('laboratorium', function (Blueprint $table) {
            // Change id_pendamping column type to text
            $table->text('id_pendamping')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('laboratorium', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pendamping')->nullable()->change();
            $table->foreign('id_pendamping')->references('id_user')->on('users')->nullOnDelete();
        });
    }
};
