<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pengaduan_foto', 'file_data')) {
            DB::statement('ALTER TABLE pengaduan_foto ADD file_data LONGBLOB NULL AFTER file_path');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pengaduan_foto', 'file_data')) {
            DB::statement('ALTER TABLE pengaduan_foto DROP COLUMN file_data');
        }
    }
};
