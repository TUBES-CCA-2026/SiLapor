<?php

namespace App\Http\Controllers;

use App\models\pengaduan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Mengambil data pengaduan beserta relasi pelapor & laboratoriumnya
        $query = Pengaduan::with(['pelapor', 'fasilitas.laboratorium']);

        // Logika fitur pencarian
        if ($search) {
            $query->where('deskripsi_kerusakan', 'like', "%{$search}%")
                  ->orWhereHas('pelapor', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
        }

        // Ambil data dengan pembatasan 10 data per halaman
        $laporanTerbaru = $query->latest()->paginate(10);

        // Mengarahkan ke file view laporan yang berada di folder views/pengaduan/
        // UBAH BARIS 29 MENJADI SEPERTI INI:
        return view('dashboard.kepalalab', compact('laporanTerbaru', 'search'));
    }
}
