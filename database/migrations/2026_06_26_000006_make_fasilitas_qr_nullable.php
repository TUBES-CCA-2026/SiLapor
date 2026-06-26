<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('fasilitas_lab', 'qr_code')) {
            DB::statement('ALTER TABLE fasilitas_lab MODIFY qr_code VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('fasilitas_lab', 'qr_code')) {
            DB::statement('ALTER TABLE fasilitas_lab MODIFY qr_code VARCHAR(255) NOT NULL');
        }
    }
};
