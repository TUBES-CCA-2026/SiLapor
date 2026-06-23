<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pengaduan;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Satu pintu /dashboard, lalu di-redirect/ditampilkan sesuai role
    public function index()
    {
        $user = Auth::user();

        if ($user->isAsisten()) {
            return $this->dashboardAsisten($user);
        }

        if ($user->isKoordinatorLab()) {
            return $this->dashboardKoordinator($user);
        }

        if ($user->isAdmin()) {
            return view('dashboard.admin');
        }

        // Role lain (laboran, kepala_lab) bisa diarahkan
        // ke view dashboard masing-masing yang sudah ada di project kamu.
        return view('dashboard.default', compact('user'));
    }

    /**
     * Dashboard koordinator_lab: lihat pengaduan masuk & berikan tugas
     * perbaikan ke asisten (inilah fitur "koordinator memberi asisten tugas
     * perbaikan" yang memicu notifikasi email, lihat TindakLanjutController@assign).
     */
    protected function dashboardKoordinator($user)
    {
        $pengaduanBaru = Pengaduan::with(['fasilitas.laboratorium', 'pelapor'])
            ->where('status_pengaduan', 'NEW')
            ->orderByDesc('id_pengaduan')
            ->get();

        $pengaduanDitangani = Pengaduan::with(['fasilitas.laboratorium', 'pelapor', 'tindakLanjut.asisten'])
            ->whereIn('status_pengaduan', ['HANDLED', 'DONE'])
            ->orderByDesc('id_pengaduan')
            ->take(20)
            ->get();

        $asisten = User::where('role', 'asisten')->orderBy('nama')->get();

        return view('dashboard.koordinator', compact('pengaduanBaru', 'pengaduanDitangani', 'asisten'));
    }

    protected function dashboardAsisten($user)
    {
        // Tugas perbaikan yang ditugaskan ke asisten ini
        $tugas = TindakLanjut::with('pengaduan.fasilitas')
            ->where('id_asisten', $user->id_user)
            ->orderByDesc('id_tindak_lanjut')
            ->get();

        // Riwayat notifikasi (email) yang pernah dikirim ke asisten ini
        $notifikasi = Notifikasi::with('tindakLanjut.pengaduan.fasilitas')
            ->where('id_asisten', $user->id_user)
            ->orderByDesc('id_notifikasi')
            ->take(20)
            ->get();

        return view('dashboard.asisten', compact('tugas', 'notifikasi'));
    }
}
