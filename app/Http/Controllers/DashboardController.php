<?php

namespace App\Http\Controllers;

use App\Models\FasilitasLab;
use App\Models\Laboratorium;
use App\Models\Notifikasi;
use App\Models\Pengaduan;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Satu pintu /dashboard, lalu di-redirect/ditampilkan sesuai role
    public function index()
    {
        $user = Auth::user();

        if ($user->isAsisten()) {
            return $this->dashboardAsisten($user);
        }

        if ($user->isKoordinatorLab()) {
            return $this->dashboardKoordinator($user);
        }

        if ($user->isLaboran() || $user->isAdmin()) {
            return $this->dashboardLaboran($user);
        }

        return view('dashboard.default', compact('user'));
    }

    /**
     * Halaman menu Laporan.
     */
    public function laporan()
    {
        $pengaduanList = Pengaduan::with(['fasilitas.laboratorium', 'pelapor', 'tindakLanjut.asisten', 'statusData', 'fotoUtama', 'fotos'])
            ->orderByDesc('id_pengaduan')
            ->get();

        return view('laporan.index', compact('pengaduanList'));
    }

    /**
     * Halaman menu Penugasan pada sidebar koordinator.
     */
    public function penugasan()
    {
        $pengaduanList = Pengaduan::with(['fasilitas.laboratorium', 'pelapor', 'tindakLanjut.asisten', 'statusData', 'fotoUtama', 'fotos'])
            ->orderByDesc('id_pengaduan')
            ->get();

        $asisten = User::role('asisten')
            ->orderBy('nama')
            ->get();

        return view('penugasan.index', compact('pengaduanList', 'asisten'));
    }

    /**
     * Halaman menu Detail Laporan / Rekapsulasi.
     */
    public function detailLaporan(Request $request)
    {
        $isKoordinator = $request->user()?->role === 'koordinator_lab';

        $query = Pengaduan::with([
                'fasilitas.laboratorium',
                'pelapor',
                'statusData',
                'fotoUtama',
                'fotos',
                'tindakLanjut.asisten',
                'tindakLanjut.penugas',
            ]);

        if ($isKoordinator) {
            // Detail Laporan adalah antrean aktif: laporan selesai tidak ditampilkan.
            $query->whereHas('statusData', fn ($status) => $status->where('kode_status', '!=', 'DONE'));
            $query->orderByDesc('tanggal_lapor')->orderByDesc('id_pengaduan');
        } else {
            if ($request->filled('status')) {
                $query->statusKode($request->string('status')->toString());
            }

            if ($request->filled('id_laboratorium')) {
                $query->whereHas('fasilitas', function ($q) use ($request) {
                    $q->where('id_laboratorium', $request->integer('id_laboratorium'));
                });
            }

            if ($request->filled('id_fasilitas')) {
                $query->where('id_fasilitas', $request->integer('id_fasilitas'));
            }

            if ($request->filled('id_penanggung_jawab')) {
                $query->whereHas('tindakLanjut', function ($q) use ($request) {
                    $q->where('id_petugas', $request->integer('id_penanggung_jawab'));
                });
            }

            if ($request->filled('q')) {
                $keyword = trim($request->string('q')->toString());
                $query->where(function ($q) use ($keyword) {
                    $q->where('deskripsi_kerusakan', 'like', "%{$keyword}%")
                        ->orWhereHas('pelapor', fn ($u) => $u->where('nama', 'like', "%{$keyword}%"))
                        ->orWhereHas('fasilitas', fn ($f) => $f->where('nama_fasilitas', 'like', "%{$keyword}%"));
                });
            }

            $sort = $request->input('sort', 'terbaru');
            $sort === 'terlama'
                ? $query->orderBy('tanggal_lapor')->orderBy('id_pengaduan')
                : $query->orderByDesc('tanggal_lapor')->orderByDesc('id_pengaduan');
        }

        $pengaduanList = $query->get();
        $laboratoriums = Laboratorium::orderBy('nama_laboratorium')->get();
        $fasilitasList = FasilitasLab::orderBy('nama_fasilitas')->get();
        $penanggungJawabs = User::role('asisten')->orderBy('nama')->get();
        $filters = $request->only(['status', 'id_laboratorium', 'id_fasilitas', 'id_penanggung_jawab', 'sort', 'q']);

        return view('detail-laporan.index', compact('pengaduanList', 'laboratoriums', 'fasilitasList', 'penanggungJawabs', 'filters'));
    }

    /**
     * Dashboard koordinator_lab: lihat pengaduan masuk & berikan tugas
     * perbaikan ke asisten.
     */
    protected function dashboardKoordinator($user)
    {
        $pengaduanBaru = Pengaduan::with(['fasilitas.laboratorium', 'pelapor', 'statusData', 'fotoUtama', 'fotos'])
            ->statusKode('NEW')
            ->orderByDesc('id_pengaduan')
            ->get();

        $pengaduanDitangani = Pengaduan::with(['fasilitas.laboratorium', 'pelapor', 'tindakLanjut.asisten', 'statusData', 'fotoUtama', 'fotos'])
            ->statusKode(['HANDLED', 'DONE'])
            ->orderByDesc('id_pengaduan')
            ->get();

        $pengaduanList = Pengaduan::with(['fasilitas.laboratorium', 'pelapor', 'tindakLanjut.asisten', 'statusData', 'fotoUtama', 'fotos'])
            ->orderByDesc('id_pengaduan')
            ->take(50)
            ->get();

        $totalLaporan = Pengaduan::count();
        $proses = Pengaduan::statusKode('HANDLED')->count();
        $selesai = Pengaduan::statusKode('DONE')->count();

        $asisten = User::role('asisten')->orderBy('nama')->get();

        return view('dashboard.koordinator', compact(
            'user',
            'pengaduanBaru',
            'pengaduanDitangani',
            'pengaduanList',
            'totalLaporan',
            'proses',
            'selesai',
            'asisten'
        ));
    }

    protected function dashboardLaboran($user)
    {
        $totalLaporan = Pengaduan::count();
        $proses = Pengaduan::statusKode('HANDLED')->count();
        $selesai = Pengaduan::statusKode('DONE')->count();
        $totalLaboratorium = Laboratorium::count();
        $totalFasilitas = FasilitasLab::count();
        $totalPengguna = User::count();

        $pengaduanList = Pengaduan::with(['fasilitas.laboratorium', 'pelapor', 'statusData', 'fotoUtama', 'fotos', 'tindakLanjut.asisten'])
            ->orderByDesc('id_pengaduan')
            ->take(10)
            ->get();

        $laboratoriumList = Laboratorium::with('koordinator')
            ->orderBy('nama_laboratorium')
            ->take(8)
            ->get();

        return view('dashboard.laboran', compact(
            'user',
            'totalLaporan',
            'proses',
            'selesai',
            'totalLaboratorium',
            'totalFasilitas',
            'totalPengguna',
            'pengaduanList',
            'laboratoriumList'
        ));
    }

    /**
     * Endpoint JSON untuk popup detail pengaduan pada dashboard koordinator.
     */
    public function detailPengaduan(Pengaduan $pengaduan): JsonResponse
    {
        $pengaduan->load(['fasilitas.laboratorium', 'pelapor', 'tindakLanjut.asisten', 'statusData', 'fotoUtama', 'fotos']);

        $statusLabel = match ($pengaduan->status_pengaduan) {
            'NEW' => 'Baru',
            'HANDLED' => 'On Progress',
            'DONE' => 'Selesai',
            default => $pengaduan->status_pengaduan,
        };

        $statusClass = match ($pengaduan->status_pengaduan) {
            'NEW' => 'new',
            'HANDLED' => 'progress',
            'DONE' => 'done',
            default => 'new',
        };

        return response()->json([
            'id' => 'PGD-' . str_pad((string) $pengaduan->id_pengaduan, 3, '0', STR_PAD_LEFT),
            'status' => $pengaduan->status_pengaduan,
            'statusLabel' => $statusLabel,
            'statusClass' => $statusClass,
            'pelapor' => $pengaduan->pelapor->nama ?? 'Guest',
            'lokasi' => $pengaduan->fasilitas?->laboratorium?->nama_laboratorium ?? '-',
            'fasilitas' => $pengaduan->fasilitas?->nama_fasilitas ?? '-',
            'tanggal' => $pengaduan->tanggal_lapor
                ? $pengaduan->tanggal_lapor->format('d/m/Y')
                : '-',
            'deskripsi' => $pengaduan->deskripsi_kerusakan ?? '-',
            'foto' => $pengaduan->foto_kerusakan_url,
            'fotos' => collect($pengaduan->foto_urls)
                ->map(fn ($url) => ['url' => $url])
                ->values()
                ->all(),
        ]);
    }

    protected function dashboardAsisten($user)
    {
        $tugas = TindakLanjut::with(['pengaduan.fasilitas.laboratorium', 'pengaduan.user', 'pengaduan.statusData', 'pengaduan.fotoUtama', 'pengaduan.fotos', 'statusData'])
            ->where('id_petugas', $user->id_user)
            ->orderByDesc('id_tindak_lanjut')
            ->get();

        $notifikasi = Notifikasi::with('tindakLanjut.pengaduan.fasilitas')
            ->where('id_user_penerima', $user->id_user)
            ->orderByDesc('id_notifikasi')
            ->take(20)
            ->get();

        $totalPengaduan = $tugas->count();
        $sedangDiperbaiki = $tugas->where('status_penanganan', 'ON PROGRES')->count();
        $selesai = $tugas->where('status_penanganan', 'DONE')->count();

        return view('dashboard.asisten', compact('user', 'tugas', 'notifikasi', 'totalPengaduan', 'sedangDiperbaiki', 'selesai'));
    }
}
