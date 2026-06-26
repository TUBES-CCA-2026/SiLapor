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


    public function update(Request $request, Pengaduan $pengaduan): RedirectResponse
    {
        $validated = $request->validate([
            'deskripsi_kerusakan' => ['required', 'string', 'max:2000'],
            'status_pengaduan' => ['required', 'in:NEW,HANDLED,DONE,CANCEL,NO_SPAREPART'],
        ]);

        $pengaduan->update($validated);

        return back()->with('success', 'Riwayat laporan berhasil diperbarui.');
    }

    public function destroy(Pengaduan $pengaduan): RedirectResponse
    {
        $pengaduan->delete();

        return back()->with('success', 'Riwayat laporan berhasil dihapus.');
    }
}
