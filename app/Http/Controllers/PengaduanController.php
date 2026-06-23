<?php

namespace App\Http\Controllers;

use App\Models\FasilitasLab;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class PengaduanController extends Controller
{
    /**
     * Form pengaduan dari QR. Fasilitas dikunci berdasarkan token QR.
     */
    public function createQr(string $qr_code): View
    {
        $fasilitas = FasilitasLab::with('laboratorium')
            ->where('qr_code', $qr_code)
            ->firstOrFail();

        return $this->renderCreateForm('qr', $fasilitas);
    }

    /**
     * Simpan pengaduan dari QR.
     */
    public function storeQr(Request $request, string $qr_code): RedirectResponse
    {
        $fasilitas = FasilitasLab::where('qr_code', $qr_code)->firstOrFail();
        $validated = $this->validateReport($request, false);

        return $this->persistReport($request, $validated, $fasilitas);
    }

    /**
     * Form pengaduan manual. Pelapor memilih fasilitas sendiri.
     */
    public function createManual(): View
    {
        return $this->renderCreateForm('manual');
    }

    /**
     * Simpan pengaduan manual berdasarkan fasilitas yang dipilih.
     */
    public function storeManual(Request $request): RedirectResponse
    {
        $validated = $this->validateReport($request, true);
        $fasilitas = FasilitasLab::findOrFail($validated['id_fasilitas']);

        return $this->persistReport($request, $validated, $fasilitas);
    }

    /**
     * QR yang telah dicetak sebelumnya tetap dapat digunakan.
     */
    public function redirectLegacyQr(string $qr_code): RedirectResponse
    {
        return redirect()->route('pengaduan.qr.create', ['qr_code' => $qr_code]);
    }

    public function success(Pengaduan $pengaduan): View
    {
        $pengaduan->load(['fasilitas.laboratorium', 'pelapor']);

        return view('pengaduan.success', compact('pengaduan'));
    }

    private function renderCreateForm(string $mode, ?FasilitasLab $fasilitas = null): View
    {
        $isGuest = !Auth::check();

        return view('pengaduan.create', [
            'mode' => $mode,
            'fasilitas' => $fasilitas,
            'facilities' => $mode === 'manual'
                ? FasilitasLab::with('laboratorium')->orderBy('nama_fasilitas')->get()
                : collect(),
            'isGuest' => $isGuest,
            'users' => $isGuest
                ? User::orderBy('nama')->get(['id_user', 'nama', 'role'])
                : collect(),
        ]);
    }

    private function validateReport(Request $request, bool $isManual): array
    {
        $isGuest = !Auth::check();

        return $request->validate([
            'id_fasilitas' => $isManual
                ? ['required', 'integer', 'exists:fasilitas_lab,id_fasilitas']
                : ['prohibited'],
            'deskripsi_kerusakan' => ['required', 'string', 'max:2000'],
            'foto_kerusakan' => ['required', 'image', 'max:4096'],
            'id_user' => $isGuest
                ? ['nullable', 'integer', 'exists:users,id_user']
                : ['prohibited'],
        ], [
            'id_fasilitas.required' => 'Fasilitas yang dilaporkan wajib dipilih.',
            'id_fasilitas.exists' => 'Fasilitas yang dipilih tidak valid.',
            'deskripsi_kerusakan.required' => 'Deskripsi kerusakan wajib diisi.',
            'foto_kerusakan.required' => 'Foto kerusakan wajib diunggah.',
            'foto_kerusakan.image' => 'File yang diunggah harus berupa gambar.',
            'foto_kerusakan.max' => 'Ukuran foto maksimal 4 MB.',
            'id_user.exists' => 'Nama pelapor yang dipilih tidak valid.',
        ]);
    }

    private function persistReport(
        Request $request,
        array $validated,
        FasilitasLab $fasilitas
    ): RedirectResponse {
        $path = $request->file('foto_kerusakan')->store('pengaduan', 'public');

        try {
            $pengaduan = Pengaduan::create([
                'foto_kerusakan' => $path,
                'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
                'tanggal_lapor' => now()->toDateString(),
                'status_pengaduan' => 'NEW',
                'id_user' => Auth::check()
                    ? Auth::id()
                    : ($validated['id_user'] ?? null),
                'id_fasilitas' => $fasilitas->id_fasilitas,
            ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        return redirect()
            ->route('pengaduan.success', $pengaduan)
            ->with('success', 'Pengaduan berhasil dikirim.');
    }
}
