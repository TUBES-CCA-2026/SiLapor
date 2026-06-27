<?php

namespace App\Http\Controllers;

use App\Models\FasilitasLab;
use App\Models\Laboratorium;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = FasilitasLab::with('laboratorium')
            ->activeQr()
            ->orderBy('id_laboratorium')
            ->orderBy('nama_fasilitas')
            ->get();

        $laboratoriums = Laboratorium::orderBy('nama_laboratorium')->get();

        return view('fasilitas.index', compact('fasilitas', 'laboratoriums'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_fasilitas' => ['required', 'string', 'max:120'],
            'id_laboratorium' => ['required', 'exists:laboratorium,id_laboratorium'],
            'no_fasilitas' => ['nullable', 'string', 'max:120'],
        ]);

        $validated['qr_code'] = Str::uuid()->toString(); // token unik untuk QR
        $validated['qr_generated_date'] = now();

        $fasilitas = FasilitasLab::create($validated);

        return back()
            ->with('success', 'Fasilitas baru berhasil ditambahkan & QR siap dicetak.')
            ->with('new_fasilitas_id', $fasilitas->id_fasilitas);
    }

    /**
     * Regenerasi token QR (kalau QR fisik hilang/rusak, token lama otomatis
     * tidak berlaku lagi).
     */
    public function regenerateQr(FasilitasLab $fasilitas)
    {
        $fasilitas->update([
            'qr_code' => Str::uuid()->toString(),
            'qr_generated_date' => now(),
            'qr_deleted_at' => null,
        ]);

        return back()
            ->with('success', 'QR Code untuk ' . $fasilitas->nama_fasilitas . ' berhasil diperbarui.')
            ->with('new_fasilitas_id', $fasilitas->id_fasilitas);
    }


    public function deleteQr(FasilitasLab $fasilitas)
    {
        $namaFasilitas = $fasilitas->nama_fasilitas;

        $fasilitas->update([
            'qr_code' => null,
            'qr_generated_date' => null,
            'qr_deleted_at' => now(),
        ]);

        return back()->with('success', 'QR Code untuk ' . $namaFasilitas . ' berhasil dihapus. Data fasilitas tersebut otomatis tidak tampil di Fasilitas & QR dan tidak dihitung lagi pada Laboratorium.');
    }
}
