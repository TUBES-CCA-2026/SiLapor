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
        $user = Auth::user();
        
        $query = TindakLanjut::with([
                'pengaduan.user',
                'pengaduan.pelapor',
                'pengaduan.fasilitas.laboratorium',
                'pengaduan.fotoUtama',
                'pengaduan.fotos',
                'asisten',
                'statusData',
            ])
            ->whereHas('statusData', function ($status) {
                $status->where('kode_status', '!=', 'DONE');
            })
            ->latest('id_tindak_lanjut');

        // Asisten hanya melihat tugas di mana dia adalah teknisi yang ditunjuk
        $query->where('id_teknisi', $user->id_user);

        $tugas = $query->get();
        $isPj = false;

        return view('tindak_lanjut.index', compact('tugas', 'isPj'));
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

        $lab = $pengaduan->fasilitas?->laboratorium;
        $pjId = $lab?->id_koordinator ?? Auth::id();

        $tindakLanjut = TindakLanjut::updateOrCreate(
            ['id_pengaduan' => $pengaduan->id_pengaduan],
            [
                'id_petugas' => $pjId,
                'id_teknisi' => $asisten->id_user,
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
        $notifikasi->loadMissing(['tindakLanjut.pengaduan.fasilitas', 'asisten']);

        $tindakLanjut = $notifikasi->tindakLanjut;
        $asisten = $notifikasi->asisten;

        if (! $tindakLanjut || ! $asisten) {
            return back()->withErrors([
                'notifikasi' => 'Notifikasi tidak dapat dikirim ulang karena data tugas atau penerima tidak lengkap.',
            ]);
        }

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
            'status_penanganan' => ['nullable', 'in:ON PROGRES,DONE,CANCEL,NO SPAREPART'],
        ]);

        $updateData = [];

        // Hanya update catatan jika diisi
        if (array_key_exists('catatan_perbaikan', $validated) && $validated['catatan_perbaikan'] !== null) {
            $updateData['catatan_perbaikan'] = $validated['catatan_perbaikan'];
        }

        // Hanya update status jika dikirim dari form
        if (!empty($validated['status_penanganan'])) {
            $updateData['status_penanganan'] = $validated['status_penanganan'];
            $updateData['tanggal_penanganan'] = now()->toDateString();

            // Update status pengaduan sesuai status penanganan
            if ($validated['status_penanganan'] === 'DONE') {
                $tindakLanjut->pengaduan->update(['status_pengaduan' => 'DONE']);
            } elseif ($validated['status_penanganan'] === 'CANCEL') {
                $tindakLanjut->pengaduan->update(['status_pengaduan' => 'CANCEL']);
            } elseif ($validated['status_penanganan'] === 'NO SPAREPART') {
                $tindakLanjut->pengaduan->update(['status_pengaduan' => 'NO_SPAREPART']);
            } elseif ($validated['status_penanganan'] === 'ON PROGRES') {
                $tindakLanjut->pengaduan->update(['status_pengaduan' => 'HANDLED']);
            }
        }

        if (!empty($updateData)) {
            $tindakLanjut->update($updateData);
        }

        return back()->with('success', 'Data perbaikan berhasil diperbarui.');
    }

    protected function authorizeAsisten(TindakLanjut $tindakLanjut): void
    {
        $userId = Auth::id();
        if ($userId === $tindakLanjut->id_petugas || $userId === $tindakLanjut->id_teknisi) {
            return;
        }

        // Cek apakah user adalah Koordinator dari laboratorium tersebut
        $lab = $tindakLanjut->pengaduan?->fasilitas?->laboratorium;
        if ($lab) {
            if ($userId === $lab->id_koordinator) {
                return;
            }
        }

        abort(403);
    }
}
