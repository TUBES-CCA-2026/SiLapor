<?php

namespace Database\Seeders;

use App\Models\Laboratorium;
use Illuminate\Database\Seeder;

class LaboratoriumSeeder extends Seeder
{
    public function run(): void
    {
        $labs = [
            ['nama_laboratorium' => 'Lab Internet Of Things',  'kode_laboratorium' => 'Lab-IOT'],
            ['nama_laboratorium' => 'Lab Startup',             'kode_laboratorium' => 'Lab-STRTP'],
            ['nama_laboratorium' => 'Lab Computer Networking',  'kode_laboratorium' => 'Lab-COMNET'],
            ['nama_laboratorium' => 'Lab Multiedia',           'kode_laboratorium' => 'Lab-MULMED'],
            ['nama_laboratorium' => 'Lab Computer Vision',      'kode_laboratorium' => 'Lab-COMVIS'],
            ['nama_laboratorium' => 'Lab Data Science',         'kode_laboratorium' => 'Lab-DS'],
            ['nama_laboratorium' => 'Lab MIcrocontroller',      'kode_laboratorium' => 'Lab-MICRO'],
        ];

        foreach ($labs as $lab) {
            Laboratorium::firstOrCreate(
                ['kode_laboratorium' => $lab['kode_laboratorium']],
                $lab
            );
        }
    }
}
