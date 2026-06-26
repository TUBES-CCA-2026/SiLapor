<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $laboranId = Role::idByName('laboran');
        $adminId = DB::table('roles')->where('nama_role', 'admin')->value('id_role');

        if ($adminId) {
            DB::table('users')->where('id_role', $adminId)->update(['id_role' => $laboranId]);
            DB::table('roles')->where('id_role', $adminId)->delete();
        }
    }

    public function down(): void
    {
        Role::idByName('admin');
    }
};
