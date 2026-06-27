<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitas_lab', function (Blueprint $table) {
            if (! Schema::hasColumn('fasilitas_lab', 'qr_deleted_at')) {
                $table->timestamp('qr_deleted_at')->nullable()->after('qr_generated_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas_lab', function (Blueprint $table) {
            if (Schema::hasColumn('fasilitas_lab', 'qr_deleted_at')) {
                $table->dropColumn('qr_deleted_at');
            }
        });
    }
};
