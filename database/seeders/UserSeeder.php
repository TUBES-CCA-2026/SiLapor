<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['nama' => 'Budi Asisten',      'email' => 'asisten@silapor.test',     'role' => 'asisten'],
            ['nama' => 'Sari Laboran',      'email' => 'laboran@silapor.test',     'role' => 'laboran'],
            ['nama' => 'Andi Koordinator',  'email' => 'koordinator@silapor.test', 'role' => 'koordinator_lab'],
            ['nama' => 'Dr. Wijaya',        'email' => 'kepalalab@silapor.test',   'role' => 'kepala_lab'],
            ['nama' => 'Admin SiLapor',     'email' => 'admin@silapor.test',       'role' => 'admin'],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'nama' => $data['nama'],
                    'password' => Hash::make('password'),
                    'id_role' => Role::idByName($data['role']),
                ]
            );
        }
    }
}
