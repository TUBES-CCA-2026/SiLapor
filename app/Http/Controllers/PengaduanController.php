<?php

namespace App\Http\Controllers;

use App\Models\FasilitasLab;
use App\Models\KategoriFasilitas;
use App\Models\Laboratorium;
use App\Models\Pengaduan;
use App\Models\PengaduanFoto;
use App\Models\TindakLanjut;
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

        // Cek duplikat: apakah fasilitas ini sudah punya laporan aktif
        if ($this->hasDuplicateReport($fasilitas->id_fasilitas)) {
            return back()->withInput()->with('duplicate_error', 'Fasilitas ini telah dilaporkan dan sedang dalam proses penanganan.');
        }

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

        // Cek duplikat: apakah fasilitas ini sudah punya laporan aktif
        if ($this->hasDuplicateReport($validated['id_fasilitas'])) {
            return back()->withInput()->with('duplicate_error', 'Fasilitas ini telah dilaporkan dan sedang dalam proses penanganan.');
        }

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
        $facilities = FasilitasLab::with(['laboratorium', 'kategori'])
            ->whereNull('qr_deleted_at')
            ->orderBy('nama_fasilitas')
            ->get();

        $facilitySource = $mode === 'qr' && $fasilitas !== null
            ? collect([$fasilitas])
            : $facilities;

        $laboratoriums = Laboratorium::orderBy('nama_laboratorium')->get();
        $categories = KategoriFasilitas::orderBy('nama_kategori')->get();

        return [
            'mode' => $mode,
            'fasilitas' => $fasilitas,
            'facilities' => $facilities,
            'laboratoriums' => $laboratoriums,
            'categories' => $categories,
            'facilityPayload' => $facilitySource->map(fn (FasilitasLab $item) => [
                'id' => (string) $item->id_fasilitas,
                'kode_barang' => $item->no_fasilitas ?: '-',
                'nama_fasilitas' => $item->nama_fasilitas,
                'lokasi_lab' => $this->formatLokasiLab($item),
                'id_laboratorium' => (string) $item->id_laboratorium,
                'id_kategori' => (string) $item->id_kategori,
                'nama_kategori' => $item->kategori?->nama_kategori ?? '-',
            ])->values(),
            'isGuest' => !Auth::check(),
            'users' => User::role('asisten')->with('roleData')->orderBy('nama')->get(['id_user', 'id_role', 'nama']),
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

    /**
     * API endpoint for cascading dropdowns: returns fasilitas filtered by lab and/or category.
     */
    public function apiFasilitas(Request $request)
    {
        $query = FasilitasLab::with(['laboratorium', 'kategori'])
            ->whereNull('qr_deleted_at');

        if ($request->filled('id_laboratorium')) {
            $query->where('id_laboratorium', $request->id_laboratorium);
        }

        if ($request->filled('id_kategori')) {
            $query->where('id_kategori', $request->id_kategori);
        }

        $fasilitas = $query->orderBy('nama_fasilitas')->get();

        return response()->json(
            $fasilitas->map(fn (FasilitasLab $item) => [
                'id' => (string) $item->id_fasilitas,
                'no_fasilitas' => $item->no_fasilitas ?: '-',
                'nama_fasilitas' => $item->nama_fasilitas,
                'id_laboratorium' => (string) $item->id_laboratorium,
                'id_kategori' => (string) $item->id_kategori,
                'nama_laboratorium' => $item->laboratorium?->nama_laboratorium ?? '-',
                'nama_kategori' => $item->kategori?->nama_kategori ?? '-',
            ])->values()
        );
    }

    private function validateReport(Request $request, bool $isManual): array
    {
        $isGuest = !Auth::check();

        return $request->validate([
            'id_fasilitas' => $isManual
                ? ['required', 'integer', 'exists:fasilitas_lab,id_fasilitas']
                : ['prohibited'],
            'deskripsi_kerusakan' => ['required', 'string', 'max:2000'],
            'foto_kerusakan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'id_user' => $isGuest
                ? ['required', 'integer', 'exists:users,id_user']
                : ['prohibited'],
        ], [
            'id_fasilitas.required' => 'Fasilitas yang dilaporkan wajib dipilih.',
            'id_fasilitas.exists' => 'Fasilitas yang dipilih tidak valid.',
            'deskripsi_kerusakan.required' => 'Deskripsi kerusakan wajib diisi.',
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
        $photoBinary = $uploadedPhoto ? file_get_contents($uploadedPhoto->getRealPath()) : null;

        try {
            $pengaduan = DB::transaction(function () use ($validated, $fasilitas, $uploadedPhoto, $photoBinary) {
                $pengaduan = Pengaduan::create([
                    'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
                    'tanggal_lapor' => now()->toDateString(),
                    'status_pengaduan' => 'NEW',
                    'id_user' => Auth::check() ? Auth::id() : $validated['id_user'],
                    'id_fasilitas' => $fasilitas->id_fasilitas,
                ]);

                if ($uploadedPhoto) {
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
                }

                // Auto-assign TindakLanjut berdasarkan PJ & Pendamping lab
                $lab = $fasilitas->laboratorium;
                if ($lab && $lab->id_penanggung_jawab) {
                    $pendampingIds = $lab->id_pendamping;
                    if (is_string($pendampingIds)) {
                        $pendampingIds = json_decode($pendampingIds, true) ?: explode(',', $pendampingIds);
                    }
                    $firstPendampingId = !empty($pendampingIds) && is_array($pendampingIds) ? reset($pendampingIds) : null;

                    TindakLanjut::create([
                        'id_pengaduan' => $pengaduan->id_pengaduan,
                        'id_petugas' => $lab->id_penanggung_jawab,
                        'id_teknisi' => $firstPendampingId,
                        'status_penanganan' => 'ON PROGRES',
                        'tanggal_penanganan' => now()->toDateString(),
                    ]);

                    $pengaduan->update(['status_pengaduan' => 'HANDLED']);
                }

                return $pengaduan;
            });
        } catch (Throwable $exception) {
            Log::error('Gagal menyimpan pengaduan: ' . $exception->getMessage());
            throw $exception;
        }

        $redirect = $this->redirectAfterReport($pengaduan);

        return $showSuccessPopup
            ? $redirect->with('success', 'Pengaduan berhasil dikirim.')
            : $redirect;
    }

    private function redirectAfterReport(Pengaduan $pengaduan): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('pengaduan.success', $pengaduan);
        }

        $user = Auth::user();

        if ($user?->isAsisten() && Route::has('pengaduan.index')) {
            return redirect()->route('pengaduan.index');
        }

        if (Route::has('dashboard')) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('pengaduan.success', $pengaduan);
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
    /**
     * Cek apakah fasilitas sudah memiliki laporan aktif (NEW atau HANDLED).
     */
    private function hasDuplicateReport(int $fasilitasId): bool
    {
        return Pengaduan::where('id_fasilitas', $fasilitasId)
            ->statusKode(['NEW', 'HANDLED'])
            ->exists();
    }
}
