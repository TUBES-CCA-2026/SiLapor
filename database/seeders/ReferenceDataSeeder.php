<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\StatusPenanganan;
use App\Models\StatusPengaduan;
use App\Models\StatusPengiriman;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['asisten', 'laboran', 'koordinator_lab', 'kepala_lab'] as $role) {
            Role::firstOrCreate(['nama_role' => $role]);
        }

        foreach ([
            'NEW' => 'New',
            'HANDLED' => 'On Progress',
            'DONE' => 'Done',
            'CANCEL' => 'Cancel',
            'NO_SPAREPART' => 'No Sparepart',
        ] as $kode => $nama) {
            StatusPengaduan::firstOrCreate(['kode_status' => $kode], ['nama_status' => $nama]);
        }

        foreach ([
            'ON PROGRES' => 'On Progress',
            'DONE' => 'Done',
            'CANCEL' => 'Cancel',
            'NO SPAREPART' => 'No Sparepart',
        ] as $kode => $nama) {
            StatusPenanganan::firstOrCreate(['kode_status' => $kode], ['nama_status' => $nama]);
        }

        foreach ([
            'sent' => 'Terkirim',
            'failed' => 'Gagal',
        ] as $kode => $nama) {
            StatusPengiriman::firstOrCreate(['kode_status' => $kode], ['nama_status' => $nama]);
        }
    }
}
