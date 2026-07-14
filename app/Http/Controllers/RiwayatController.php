<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            ->orderByDesc('updated_at')
            ->get();

        $rows = $riwayat;

        return view('riwayat.index', compact('riwayat', 'rows'));
    }



}
