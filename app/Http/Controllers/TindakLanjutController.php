<?php

namespace App\Http\Controllers;

use App\Mail\TugasPerbaikanMail;
use App\Models\Notifikasi;
use App\Models\Pengaduan;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TindakLanjutController extends Controller
{
    public function index()
    {
        $tugas = TindakLanjut::with(['pengaduan.user', 'pengaduan.fasilitas.laboratorium', 'pengaduan.fotoUtama', 'pengaduan.fotos', 'asisten', 'statusData'])
            ->latest('id_tindak_lanjut')
            ->get();

        return view('tindak_lanjut.index', compact('tugas'));
    }

    /**
     * Dipanggil oleh koordinator_lab: menugaskan satu pengaduan ke seorang asisten.
     * Sesuai flowchart Koordinator Lab: "Sistem Mengirimkan Notifikasi Email ke
     * Penanggung Jawab" -> "Sistem Mencatat Pengiriman Notifikasi".
     */
    public function assign(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'id_asisten' => ['required', 'exists:users,id_user'],
        ]);

        $asisten = User::where('id_user', $validated['id_asisten'])
            ->role('asisten')
            ->firstOrFail();

        $tindakLanjut = TindakLanjut::updateOrCreate(
            ['id_pengaduan' => $pengaduan->id_pengaduan],
            [
                'id_petugas' => $asisten->id_user,
                'status_penanganan' => $pengaduan->status_pengaduan === 'DONE' ? 'DONE' : 'ON PROGRES',
            ]
        );

        if ($pengaduan->status_pengaduan !== 'DONE') {
            $pengaduan->update(['status_pengaduan' => 'HANDLED']);
        }

        $this->kirimNotifikasiAsisten($tindakLanjut, $asisten);

        return back()->with('success', "Pengaduan berhasil ditugaskan ke {$asisten->nama}.");
    }

    /**
     * Kirim email ke asisten + catat hasil pengiriman ke tabel notifikasi.
     * Dibuat terpisah supaya bisa dipanggil ulang (retry) kalau gagal.
     */
    protected function kirimNotifikasiAsisten(TindakLanjut $tindakLanjut, User $asisten): Notifikasi
    {
        $status = 'sent';

        try {
            Mail::to($asisten->email)->send(new TugasPerbaikanMail($tindakLanjut));
        } catch (\Throwable $e) {
            $status = 'failed';
            Log::error('Gagal mengirim notifikasi tugas perbaikan: ' . $e->getMessage());
        }

        return Notifikasi::create([
            'id_tindak_lanjut' => $tindakLanjut->id_tindak_lanjut,
            'id_user_penerima' => $asisten->id_user,
            'status_pengiriman' => $status,
            'tanggal_pengiriman' => now(),
            'created_at' => now(),
        ]);
    }

    /**
     * Asisten mengirim ulang notifikasi yang gagal terkirim.
     */
    public function kirimUlang(Notifikasi $notifikasi)
    {
        $tindakLanjut = $notifikasi->tindakLanjut;
        $asisten = $notifikasi->asisten;

        $this->kirimNotifikasiAsisten($tindakLanjut, $asisten);

        return back()->with('success', 'Notifikasi dikirim ulang.');
    }

    /**
     * Asisten memperbarui status pengerjaan (sesuai flowchart Aslab: input
     * catatan perbaikan, status ON PROGRES / DONE).
     */
    public function update(Request $request, TindakLanjut $tindakLanjut)
    {
        $this->authorizeAsisten($tindakLanjut);

        $validated = $request->validate([
            'catatan_perbaikan' => ['nullable', 'string'],
            'status_penanganan' => ['required', 'in:ON PROGRES,DONE'],
        ]);

        if (!array_key_exists('catatan_perbaikan', $validated) || $validated['catatan_perbaikan'] === null) {
            unset($validated['catatan_perbaikan']);
        }

        $validated['tanggal_penanganan'] = now()->toDateString();
        $tindakLanjut->update($validated);

        if ($validated['status_penanganan'] === 'DONE') {
            $tindakLanjut->pengaduan->update(['status_pengaduan' => 'DONE']);
        }

        return back()->with('success', 'Status perbaikan berhasil diperbarui.');
    }

    protected function authorizeAsisten(TindakLanjut $tindakLanjut): void
    {
        if (Auth::id() !== $tindakLanjut->id_petugas) {
            abort(403);
        }
    }
}
