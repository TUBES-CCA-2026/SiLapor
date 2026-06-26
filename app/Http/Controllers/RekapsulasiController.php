<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;; // Sesuaikan dengan model Anda
use Illuminate\Http\Request;

class RekapsulasiController extends Controller
{
    public function index(Request $request)
    {
        // Memulai query dasar
        $query = Pengaduan::query();

        // 1. Filter Tanggal
        if ($request->filled('tanggal')) {
            // Asumsi input format: "YYYY-MM-DD - YYYY-MM-DD" atau single date
            $dates = explode(' - ', $request->tanggal);
            $query->whereBetween('created_at', [$dates[0], $dates[1] ?? $dates[0]]);
        }

        // 2. Filter Penanggung Jawab
        if ($request->filled('penanggung_jawab')) {
            $query->where('user_id', $request->penanggung_jawab);
        }

        // 3. Filter Lokasi Masalah (Lab)
        if ($request->filled('lokasi')) {
            $query->whereHas('fasilitas.laboratorium', function($q) use ($request) {
                $q->where('id', $request->lokasi);
            });
        }

        // 4. Cari Laporan (Keyword)
        if ($request->filled('search')) {
            $query->where('deskripsi_kerusakan', 'like', '%' . $request->search . '%');
        }

        // 5. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 6. Filter Fasilitas
        if ($request->filled('fasilitas')) {
            $query->where('fasilitas_id', $request->fasilitas);
        }

        // 7. Pengurutan (Sort)
        $sortOrder = $request->input('urutan', 'desc');
        $query->orderBy('created_at', $sortOrder);

        // Eksekusi Data dengan Pagination
        $daftarLaporan = $query->paginate(10)->withQueryString();

        return view('rekapsulasi.index', compact('daftarLaporan'));
    }
}