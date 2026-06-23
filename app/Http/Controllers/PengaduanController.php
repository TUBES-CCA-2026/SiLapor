<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;
use App\Models\TindakLanjut;
use Illuminate\Support\Facades\Log;

class PengaduanController extends Controller
{
    /**
     * Menampilkan daftar pengaduan (Tampilan utama yang konsisten).
     * Gunakan ini sebagai satu-satunya rute tujuan untuk menu Pengaduan.
     */
    public function index()
    {
        try {
            $tugas = TindakLanjut::with(['pengaduan' => function($query) {
                        $query->with(['user', 'fasilitas.laboratorium']);
                    }])
                    ->latest()
                    ->get();
                    
            // Semua akses sekarang mengarah ke view yang sama
            return view('pengaduan.index', compact('tugas'));
        } catch (\Exception $e) {
            Log::error('Error Halaman Pengaduan: ' . $e->getMessage());
            return view('pengaduan.index', ['tugas' => collect([])]);
        }
    }

    /**
     * Jika Anda ingin fitur "Buat Pengaduan" muncul di halaman yang sama,
     * Anda bisa menggunakan modal di pengaduan.index atau mengarahkan ke sini.
     * Pastikan view 'pengaduan.create' menggunakan layout yang sama dengan 'pengaduan.index'.
     */
    public function create()
    {
        return view('pengaduan.create');
    }

    /**
     * Menyimpan data pengaduan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_fasilitas' => 'required',
            'deskripsi'    => 'required',
            'foto'         => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $fotoPath = $request->file('foto')->store('uploads', 'public');

            Pengaduan::create([
                'id_user'             => auth()->id(), 
                'id_fasilitas'        => $request->id_fasilitas,
                'deskripsi_kerusakan' => $request->deskripsi,
                'foto_kerusakan'      => $fotoPath,
                'status'              => 'PENDING'
            ]);

            return redirect()->route('pengaduan.index')->with('success', 'Pengaduan berhasil dikirim!');
        } catch (\Exception $e) {
            Log::error('Gagal simpan pengaduan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
}