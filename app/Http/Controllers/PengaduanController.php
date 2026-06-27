<?php

namespace App\Http\Controllers;

use App\Models\FasilitasLab;
use App\Models\Pengaduan;
use App\Models\PengaduanFoto;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Throwable;

class PengaduanController extends Controller
{
    /**
     * Halaman pengaduan untuk user yang sudah login.
     * Nama pelapor otomatis memakai user login, sedangkan detail fasilitas
     * otomatis terisi setelah fasilitas dipilih.
     */
    public function index(): View
    {
        return view('pengaduan.index', $this->formData('manual'));
    }

    /**
     * Kompatibilitas tombol/route lama /pengaduan/create.
     */
    public function create(): View
    {
        return $this->createManual();
    }

    /**
     * Kompatibilitas submit lama /pengaduan.
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->storeManual($request);
    }

    /**
     * Form pengaduan dari QR. Fasilitas dikunci berdasarkan token QR.
     */
    public function createQr(string $qr_code): View
    {
        $fasilitas = FasilitasLab::with('laboratorium')
            ->where('qr_code', $qr_code)
            ->whereNull('qr_deleted_at')
            ->firstOrFail();

        return view('pengaduan.create', $this->formData('qr', $fasilitas));
    }

    /**
     * Simpan pengaduan dari QR.
     */
    public function storeQr(Request $request, string $qr_code): RedirectResponse
    {
        $fasilitas = FasilitasLab::with('laboratorium')
            ->where('qr_code', $qr_code)
            ->whereNull('qr_deleted_at')
            ->firstOrFail();

        $validated = $this->validateReport($request, false);

        return $this->persistReport($request, $validated, $fasilitas, Auth::check());
    }

    /**
     * Form pengaduan manual publik. Bisa dibuka tanpa login.
     */
    public function createManual(): View
    {
        return view('pengaduan.create', $this->formData('manual'));
    }

    /**
     * Simpan pengaduan manual berdasarkan fasilitas yang dipilih.
     */
    public function storeManual(Request $request): RedirectResponse
    {
        $validated = $this->validateReport($request, true);

        $fasilitas = FasilitasLab::with('laboratorium')
            ->findOrFail($validated['id_fasilitas']);

        return $this->persistReport($request, $validated, $fasilitas);
    }

    /**
     * QR lama yang masih menyimpan URL /lapor/{qr_code} tetap diarahkan ke route baru.
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

    /**
     * Data standar untuk form QR dan manual.
     */
    private function formData(string $mode, ?FasilitasLab $fasilitas = null): array
    {
        $facilities = FasilitasLab::with('laboratorium')
            ->orderBy('nama_fasilitas')
            ->get();

        $facilitySource = $mode === 'qr' && $fasilitas !== null
            ? collect([$fasilitas])
            : $facilities;

        return [
            'mode' => $mode,
            'fasilitas' => $fasilitas,
            'facilities' => $facilities,
            'facilityPayload' => $facilitySource->map(fn (FasilitasLab $item) => [
                'id' => (string) $item->id_fasilitas,
                'kode_barang' => $item->no_fasilitas ?: '-',
                'nama_fasilitas' => $item->nama_fasilitas,
                'lokasi_lab' => $this->formatLokasiLab($item),
            ])->values(),
            'isGuest' => !Auth::check(),
            'users' => User::with('roleData')->orderBy('nama')->get(['id_user', 'id_role', 'nama']),
        ];
    }

    private function formatLokasiLab(FasilitasLab $fasilitas): string
    {
        $laboratorium = $fasilitas->laboratorium;

        if (!$laboratorium) {
            return '-';
        }

        $nama = $laboratorium->nama_laboratorium ?: '-';
        $lokasi = $laboratorium->lokasi;

        return $lokasi ? $nama . ' - ' . $lokasi : $nama;
    }

    private function validateReport(Request $request, bool $isManual): array
    {
        $isGuest = !Auth::check();

        return $request->validate([
            'id_fasilitas' => $isManual
                ? ['required', 'integer', 'exists:fasilitas_lab,id_fasilitas']
                : ['prohibited'],
            'deskripsi_kerusakan' => ['required', 'string', 'max:2000'],
            'foto_kerusakan' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'id_user' => $isGuest
                ? ['required', 'integer', 'exists:users,id_user']
                : ['prohibited'],
        ], [
            'id_fasilitas.required' => 'Fasilitas yang dilaporkan wajib dipilih.',
            'id_fasilitas.exists' => 'Fasilitas yang dipilih tidak valid.',
            'deskripsi_kerusakan.required' => 'Deskripsi kerusakan wajib diisi.',
            'foto_kerusakan.required' => 'Foto kerusakan wajib diunggah.',
            'foto_kerusakan.image' => 'File yang diunggah harus berupa gambar.',
            'foto_kerusakan.mimes' => 'Foto kerusakan harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_kerusakan.max' => 'Ukuran foto maksimal 4 MB.',
            'id_user.required' => 'Nama pelapor wajib dipilih dari user yang sudah terdaftar.',
            'id_user.exists' => 'Nama pelapor yang dipilih tidak valid.',
            'id_user.prohibited' => 'Nama pelapor otomatis memakai akun yang sedang login.',
        ]);
    }

    private function persistReport(
        Request $request,
        array $validated,
        FasilitasLab $fasilitas,
        bool $showSuccessPopup = true
    ): RedirectResponse {
        $uploadedPhoto = $request->file('foto_kerusakan');
        $photoBinary = file_get_contents($uploadedPhoto->getRealPath());

        try {
            $pengaduan = DB::transaction(function () use ($validated, $fasilitas, $uploadedPhoto, $photoBinary) {
                $pengaduan = Pengaduan::create([
                    'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
                    'tanggal_lapor' => now()->toDateString(),
                    'status_pengaduan' => 'NEW',
                    'id_user' => Auth::check() ? Auth::id() : $validated['id_user'],
                    'id_fasilitas' => $fasilitas->id_fasilitas,
                ]);

                $pengaduan->foto()->create([
                    // Nilai ini dipertahankan supaya database lama yang masih mewajibkan
                    // kolom file_path tetap bisa menerima insert baru. File foto sebenarnya
                    // disimpan sebagai binary pada kolom file_data di tabel pengaduan_foto.
                    'file_path' => 'database',
                    'file_data' => $photoBinary,
                    'file_base64' => null,
                    'mime_type' => $uploadedPhoto->getMimeType(),
                    'original_name' => $uploadedPhoto->getClientOriginalName(),
                    'file_size' => $uploadedPhoto->getSize(),
                    'created_at' => now(),
                ]);

                return $pengaduan;
            });
        } catch (Throwable $exception) {
            Log::error('Gagal menyimpan pengaduan: ' . $exception->getMessage());
            throw $exception;
        }

        $redirect = redirect()->route('pengaduan.success', $pengaduan);

        return $showSuccessPopup
            ? $redirect->with('success', 'Pengaduan berhasil dikirim.')
            : $redirect;
    }

    public function showFoto(PengaduanFoto $foto)
    {
        $binary = $foto->file_data;

        if ($binary === null && !blank($foto->file_base64)) {
            $binary = base64_decode($foto->file_base64, true);
        }

        abort_if($binary === null || $binary === false || $binary === '', 404);

        return response($binary, 200)
            ->header('Content-Type', $foto->mime_type ?: 'image/jpeg')
            ->header('Content-Length', (string) strlen($binary))
            ->header('Cache-Control', 'private, max-age=86400');
    }
}
