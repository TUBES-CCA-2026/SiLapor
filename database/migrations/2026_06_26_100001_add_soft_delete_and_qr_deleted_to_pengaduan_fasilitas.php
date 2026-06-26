<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            if (! Schema::hasColumn('pengaduan', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('fasilitas_lab', function (Blueprint $table) {
            if (! Schema::hasColumn('fasilitas_lab', 'qr_deleted_at')) {
                $table->timestamp('qr_deleted_at')->nullable()->after('qr_generated_date');
            }
        });

        if (Schema::hasTable('roles')) {
            $laboranId = DB::table('roles')->where('nama_role', 'laboran')->value('id_role');
            $adminId = DB::table('roles')->where('nama_role', 'admin')->value('id_role');

            if ($laboranId && $adminId) {
                DB::table('users')->where('id_role', $adminId)->update(['id_role' => $laboranId]);
                DB::table('roles')->where('id_role', $adminId)->delete();
            }
        }
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            if (Schema::hasColumn('pengaduan', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('fasilitas_lab', function (Blueprint $table) {
            if (Schema::hasColumn('fasilitas_lab', 'qr_deleted_at')) {
                $table->dropColumn('qr_deleted_at');
            }
        });
    }
};
