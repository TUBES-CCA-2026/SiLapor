<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

final class SidebarMenu
{
    public static function forRole(?string $role): array
    {
        $url = static fn (string $name): string => Route::has($name) ? route($name) : '#';

        return match ($role) {
            'laboran' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['laporan', 'Laporan', 'fa-regular fa-file-lines', $url('laporan.index')],
                ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $url('riwayat.index')],
                ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $url('rekapsulasi.index')],
                ['laboratorium', 'Laboratorium', 'fa-regular fa-building', $url('laboratorium.index')],
                ['fasilitas', 'Fasilitas & QR', 'fa-solid fa-qrcode', $url('fasilitas.index')],
                ['users', 'Kelola User', 'fa-solid fa-users-gear', $url('admin.users.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            'koordinator_lab' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['laporan', 'Laporan', 'fa-regular fa-file-lines', $url('laporan.index')],
                ['penugasan', 'Penugasan', 'fa-solid fa-user-check', $url('penugasan.index')],
                ['detail-laporan', 'Detail Laporan', 'fa-regular fa-rectangle-list', $url('detail-laporan.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            'asisten' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['pengaduan', 'Pengaduan', 'fa-regular fa-file-lines', $url('pengaduan.index')],
                ['tindak-lanjut', 'Tindak Lanjut', 'fa-solid fa-screwdriver-wrench', $url('tindak-lanjut.index')],
                ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $url('riwayat.index')],
                ['teknisi', 'Teknisi', 'fa-solid fa-triangle-exclamation', $url('teknisi.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            'kepala_lab' => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['laporan', 'Laporan', 'fa-regular fa-file-lines', $url('laporan.index')],
                ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $url('riwayat.index')],
                ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $url('rekapsulasi.index')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
            default => [
                ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $url('dashboard')],
                ['profil', 'Profil', 'fa-regular fa-user', $url('profile.index')],
            ],
        };
    }
}
