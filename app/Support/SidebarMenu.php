<?php

namespace App\Support;

use App\Models\Laboratorium;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

final class SidebarMenu
{
    public static function forRole(?string $role): array
    {
        $url = static function (string $name): string {
            if (! Route::has($name)) {
                return '#';
            }

            try {
                return route($name);
            } catch (\Throwable $e) {
                return '#';
            }
        };

        return match ($role) {
            'admin' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['users', 'Kelola User', 'fa-solid fa-users-gear', $url('admin.users.index')],
                ['fasilitas', 'Fasilitas & QR', 'fa-solid fa-qrcode', $url('fasilitas.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            'laboran' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['laporan', 'Laporan', 'fa-regular fa-file-lines', $url('laporan.index')],
                ['rekapsulasi', 'Rekapitulasi', 'fa-regular fa-rectangle-list', $url('rekapsulasi.index')],
                ['laboratorium', 'Laboratorium', 'fa-regular fa-building', $url('laboratorium.index')],
                ['users', 'Kelola User', 'fa-solid fa-users-gear', $url('admin.users.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            'koordinator_lab' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['penugasan', 'Penugasan', 'fa-solid fa-user-check', $url('penugasan.index')],
                ['detail-laporan', 'Detail Laporan', 'fa-regular fa-rectangle-list', $url('detail-laporan.index')],
                ['laboratorium', 'Laboratorium', 'fa-regular fa-building', $url('laboratorium.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            'asisten' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['pengaduan', 'Pengaduan', 'fa-regular fa-file-lines', $url('pengaduan.index')],
                ['tindak-lanjut', 'Teknisi', 'fa-solid fa-screwdriver-wrench', $url('tindak-lanjut.index')],
                ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $url('riwayat.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            'kepala_lab' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['laporan', 'Laporan', 'fa-regular fa-file-lines', $url('laporan.index')],
                ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $url('riwayat.index')],
                ['rekapsulasi', 'Rekapitulasi', 'fa-regular fa-rectangle-list', $url('rekapsulasi.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            default => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
        };
    }
}
