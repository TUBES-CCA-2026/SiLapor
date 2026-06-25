<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Mengambil data pengaduan yang berstatus 'DONE' (Selesai) beserta relasinya
        $riwayats = Pengaduan::with(['user', 'fasilitas_lab.laboratorium'])
            ->where('status_pengaduan', 'DONE') 
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('deskripsi_kerusakan', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('nama', 'like', "%{$search}%");
                      })
                      ->orWhereHas('fasilitas_lab', function ($f) use ($search) {
                          $f->where('nama_fasilitas', 'like', "%{$search}%")
                            ->orWhereHas('laboratorium', function ($l) use ($search) {
                                $l->where('nama_laboratorium', 'like', "%{$search}%");
                            });
                      });
                });
            })
            ->latest('tanggal_lapor')
            ->paginate(10);

        return view('riwayat.index', compact('riwayats', 'search'));
    }
}