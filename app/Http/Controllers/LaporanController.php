<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan; // Kepala lab tetap membaca data dari tabel pengaduan/laporan

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Mengambil semua data pengaduan/laporan untuk dipantau Kepala Lab
        $daftarLaporan = Pengaduan::with(['fasilitas.laboratorium'])
            ->when($search, function($query) use ($search) {
                return $query->where('deskripsi_kerusakan', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10); // Amankan pagination dengan ->paginate()

        return view('laporan.index', compact('daftarLaporan', 'search'));
    }

    public function show($id)
    {
        $laporan = Pengaduan::with(['fasilitas.laboratorium'])->findOrFail($id);
        return view('laporan.show', compact('laporan'));
    }
}