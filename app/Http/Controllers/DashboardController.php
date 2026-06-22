<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\TindakLanjut;
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

        if ($user->isAdmin()) {
            return view('dashboard.admin');
        }

        // Role lain (laboran, koordinator_lab, kepala_lab) bisa diarahkan
        // ke view dashboard masing-masing yang sudah ada di project kamu.
        return view('dashboard.default', compact('user'));
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
