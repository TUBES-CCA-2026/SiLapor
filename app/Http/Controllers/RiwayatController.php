<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat = Pengaduan::with([
                'fasilitas.laboratorium',
                'pelapor',
                'statusData',
                'fotoUtama',
                'fotos',
                'tindakLanjut.asisten',
            ])
            ->statusKode('DONE')
            ->orderByDesc('id_pengaduan')
            ->get();

        return view('riwayat.index', compact('riwayat'));
    }
}
