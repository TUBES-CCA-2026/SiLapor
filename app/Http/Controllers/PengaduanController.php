<?php

namespace App\Http\Controllers;

use App\Models\FasilitasLab;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    /**
     * Hasil scan QR mengarah ke sini: /lapor/{qr_code}
     * - Bisa diakses TANPA login (guest) -> nama_fasilitas & laboratorium
     *   otomatis terisi dari data fasilitas yang di-scan.
     * - Kalau user sudah login, otomatis ikut terisi sebagai pelapor.
     */
    public function create(string $qr_code)
    {
        $fasilitas = FasilitasLab::where('qr_code', $qr_code)->firstOrFail();

        return view('pengaduan.create', [
            'fasilitas' => $fasilitas,
            'isGuest' => !Auth::check(),
        ]);
    }

    public function store(Request $request, string $qr_code)
    {
        $fasilitas = FasilitasLab::where('qr_code', $qr_code)->firstOrFail();

        $validated = $request->validate([
            'deskripsi_kerusakan' => ['required', 'string', 'max:2000'],
            'foto_kerusakan' => ['required', 'image', 'max:4096'], // 4MB
        ], [
            'deskripsi_kerusakan.required' => 'Deskripsi kerusakan wajib diisi.',
            'foto_kerusakan.required' => 'Foto kerusakan wajib diunggah.',
            'foto_kerusakan.image' => 'File yang diunggah harus berupa gambar.',
        ]);

        $path = $request->file('foto_kerusakan')->store('pengaduan', 'public');

        $pengaduan = Pengaduan::create([
            'foto_kerusakan' => $path,
            'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
            'tanggal_lapor' => now()->toDateString(),
            'status_pengaduan' => 'NEW',
            // Auth::id() bernilai null otomatis kalau diakses sebagai guest
            'id_user' => Auth::id(),
            'id_fasilitas' => $fasilitas->id_fasilitas,
        ]);

        return redirect()
            ->route('pengaduan.success', $pengaduan->id_pengaduan)
            ->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function success(Pengaduan $pengaduan)
    {
        $pengaduan->load('fasilitas');

        return view('pengaduan.success', compact('pengaduan'));
    }
}
