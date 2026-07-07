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
    public function index()
    {
        $user = Auth::user()->load('profile', 'roleData');

        if ($user->isAdmin()) {
            return $this->dashboardAdmin($user);
        }

        if ($user->isAsisten()) {
            return $this->dashboardAsisten($user);
        }

        if ($user->isKoordinatorLab()) {
            return $this->dashboardKoordinator($user);
        }

        if ($user->isLaboran()) {
            return $this->dashboardLaboran($user);
        }

        if ($user->isKepalaLab()) {
            return $this->dashboardKepalaLab($user);
        }

        return view('dashboard.default', compact('user'));
    }

    protected function dashboardAdmin($user)
    {
        $totalLaboratorium = Laboratorium::count();
        $totalFasilitas = FasilitasLab::activeQr()->count();
        $totalPengguna = User::count();

        return view('dashboard.admin', compact(
            'user',
            'totalLaboratorium',
            'totalFasilitas',
            'totalPengguna'
        ));
    }

    public function laporan()
    {
        $query = Pengaduan::with([
            'fasilitas.laboratorium',
            'pelapor',
            'tindakLanjut.asisten',
            'statusData',
            'fotoUtama',
            'fotos',
        ]);

        // Laboran hanya melihat laporan berstatus NO_SPAREPART
        $user = auth()->user();
        if ($user && $user->role === 'laboran') {
            $query->statusKode('NO_SPAREPART');
        }

        $pengaduanList = $query->orderByDesc('id_pengaduan')->get();

        return view('laporan.index', compact('pengaduanList'));
    }

    public function penugasan()
    {
        $user = Auth::user();

        // Cari ID laboratorium di mana asisten ini ditunjuk sebagai PJ
        $labIds = Laboratorium::where('id_penanggung_jawab', $user->id_user)->pluck('id_laboratorium')->toArray();

        $pengaduanList = Pengaduan::with([
            'fasilitas.laboratorium',
            'pelapor',
            'tindakLanjut.asisten',
            'statusData',
            'fotoUtama',
            'fotos',
        ])
            ->whereHas('fasilitas.laboratorium', function ($query) use ($labIds) {
                $query->whereIn('id_laboratorium', $labIds);
            })
            ->orderByDesc('id_pengaduan')
            ->get();

        $asisten = collect(); // Dropdown sekarang spesifik per laboratorium di view

        return view('penugasan.index', compact('pengaduanList', 'asisten'));
    }

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
            $query->whereHas('statusData', function ($status) {
                $status->where('kode_status', '!=', 'DONE');
            });

            $query->orderByDesc('tanggal_lapor')
                ->orderByDesc('id_pengaduan');
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
                        ->orWhereHas('pelapor', function ($u) use ($keyword) {
                            $u->where('nama', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('fasilitas', function ($f) use ($keyword) {
                            $f->where('nama_fasilitas', 'like', "%{$keyword}%");
                        });
                });
            }

            $sort = $request->input('sort', 'terbaru');

            if ($sort === 'terlama') {
                $query->orderBy('tanggal_lapor')
                    ->orderBy('id_pengaduan');
            } else {
                $query->orderByDesc('tanggal_lapor')
                    ->orderByDesc('id_pengaduan');
            }
        }

        $pengaduanList = $query->get();
        $laboratoriums = Laboratorium::orderBy('nama_laboratorium')->get();
        $fasilitasList = FasilitasLab::activeQr()->orderBy('nama_fasilitas')->get();
        $penanggungJawabs = User::role('asisten')->orderBy('nama')->get();
        $filters = $request->only(['status', 'id_laboratorium', 'id_fasilitas', 'id_penanggung_jawab', 'sort', 'q']);

        return view('detail-laporan.index', compact(
            'pengaduanList',
            'laboratoriums',
            'fasilitasList',
            'penanggungJawabs',
            'filters'
        ));
    }


    public function rekapsulasi(Request $request)
    {
        $query = Pengaduan::with([
            'fasilitas.laboratorium',
            'pelapor',
            'statusData',
            'fotoUtama',
            'fotos',
            'tindakLanjut.asisten',
            'tindakLanjut.penugas',
        ]);

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_lapor', $request->input('tanggal'));
        }

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
                    ->orWhereHas('pelapor', function ($u) use ($keyword) {
                        $u->where('nama', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('fasilitas', function ($f) use ($keyword) {
                        $f->where('nama_fasilitas', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->input('sort') === 'terlama') {
            $query->orderBy('tanggal_lapor')->orderBy('id_pengaduan');
        } else {
            $query->orderByDesc('tanggal_lapor')->orderByDesc('id_pengaduan');
        }

        $daftarLaporan = $query->paginate(10)->withQueryString();
        $laboratoriums = Laboratorium::orderBy('nama_laboratorium')->get();
        $fasilitasList = FasilitasLab::activeQr()->orderBy('nama_fasilitas')->get();
        $penanggungJawabs = User::role('asisten')->orderBy('nama')->get();
        $filters = $request->only(['tanggal', 'status', 'id_laboratorium', 'id_fasilitas', 'id_penanggung_jawab', 'sort', 'q']);

        return view('rekapsulasi.index', compact(
            'daftarLaporan',
            'laboratoriums',
            'fasilitasList',
            'penanggungJawabs',
            'filters'
        ));
    }

    public function detailPengaduan(Pengaduan $pengaduan): JsonResponse
    {
        $pengaduan->load([
            'fasilitas.laboratorium',
            'pelapor',
            'tindakLanjut.asisten',
            'tindakLanjut.penugas',
            'statusData',
            'fotoUtama',
            'fotos',
        ]);

        $statusKode = $pengaduan->status_pengaduan;

        $statusLabel = match ($statusKode) {
            'NEW' => 'Baru',
            'HANDLED' => 'On Progress',
            'DONE' => 'Selesai',
            'CANCEL' => 'Cancel',
            'NO_SPAREPART' => 'No Sparepart',
            default => $statusKode ?: '-',
        };

        $statusClass = match ($statusKode) {
            'NEW' => 'new',
            'HANDLED' => 'progress',
            'DONE' => 'done',
            'CANCEL' => 'cancel',
            'NO_SPAREPART' => 'no-sparepart',
            default => 'new',
        };

        $tanggalLapor = $pengaduan->tanggal_lapor
            ? \Carbon\Carbon::parse($pengaduan->tanggal_lapor)->format('d/m/Y')
            : '-';

        $tanggalSelesai = $pengaduan->updated_at
            ? \Carbon\Carbon::parse($pengaduan->updated_at)->format('d/m/Y')
            : '-';

        $fotoUrls = collect($pengaduan->foto_urls)
            ->filter()
            ->map(fn ($url) => ['url' => $url])
            ->values()
            ->all();

        return response()->json([
            'id' => 'PGD-' . str_pad((string) $pengaduan->id_pengaduan, 3, '0', STR_PAD_LEFT),
            'status' => $statusKode,
            'statusLabel' => $statusLabel,
            'statusClass' => $statusClass,
            'pelapor' => $pengaduan->pelapor?->nama ?? $pengaduan->user?->nama ?? 'Guest',
            'lokasi' => $pengaduan->fasilitas?->laboratorium?->nama_laboratorium ?? '-',
            'fasilitas' => $pengaduan->fasilitas?->nama_fasilitas ?? '-',
            'tanggal' => $tanggalLapor,
            'tanggalLapor' => $tanggalLapor,
            'tanggalSelesai' => $tanggalSelesai,
            'deskripsi' => $pengaduan->deskripsi_kerusakan ?? '-',
            'penanggungJawab' => $pengaduan->tindakLanjut?->asisten?->nama
                ?? $pengaduan->tindakLanjut?->penugas?->nama
                ?? 'Belum ditugaskan',
            'catatanPerbaikan' => $pengaduan->tindakLanjut?->catatan_perbaikan ?? '-',
            'foto' => $pengaduan->foto_kerusakan_url,
            'fotos' => $fotoUrls,
        ]);
    }

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
        $totalFasilitas = FasilitasLab::activeQr()->count();
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

    protected function dashboardAsisten($user)
    {
        $tugas = TindakLanjut::with([
            'pengaduan.fasilitas.laboratorium',
            'pengaduan.pelapor',
            'pengaduan.user',
            'pengaduan.statusData',
            'pengaduan.fotoUtama',
            'pengaduan.fotos',
            'statusData',
        ])
            ->where('id_petugas', $user->id_user)
            ->whereHas('statusData', function ($status) {
                $status->where('kode_status', '!=', 'DONE');
            })
            ->orderByDesc('id_tindak_lanjut')
            ->get();

        $notifikasi = Notifikasi::with('tindakLanjut.pengaduan.fasilitas')
            ->where('id_user_penerima', $user->id_user)
            ->orderByDesc('id_notifikasi')
            ->take(20)
            ->get();

        $statistikTugas = TindakLanjut::query()
            ->where('id_petugas', $user->id_user);

        $totalPengaduan = Pengaduan::count();
        $sedangDiperbaiki = (clone $statistikTugas)->statusKode('ON PROGRES')->count();
        $selesai = (clone $statistikTugas)->statusKode('DONE')->count();

        return view('dashboard.asisten', compact(
            'user',
            'tugas',
            'notifikasi',
            'totalPengaduan',
            'sedangDiperbaiki',
            'selesai'
        ));
    }

    protected function dashboardKepalaLab($user)
    {
        $totalLaporan = Pengaduan::count();
        $selesai = Pengaduan::statusKode('DONE')->count();
        $proses = Pengaduan::statusKode('HANDLED')->count();
        $tertunda = Pengaduan::statusKode('NEW')->count();

        $daftarLaporan = Pengaduan::with([
            'fasilitas.laboratorium',
            'pelapor',
            'statusData',
            'fotoUtama',
            'fotos',
            'tindakLanjut.asisten',
        ])
            ->orderByDesc('id_pengaduan')
            ->take(8)
            ->get();

        return view('dashboard.kepalalab', compact(
            'user',
            'totalLaporan',
            'selesai',
            'proses',
            'tertunda',
            'daftarLaporan'
        ));
    }


    public function updatePengaduan(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'deskripsi_kerusakan' => ['nullable', 'string', 'max:2000'],
            'status_pengaduan' => ['nullable', 'in:NEW,HANDLED,DONE,CANCEL,NO_SPAREPART'],
        ]);

        if (isset($validated['deskripsi_kerusakan'])) {
            $pengaduan->deskripsi_kerusakan = $validated['deskripsi_kerusakan'];
        }

        if (isset($validated['status_pengaduan'])) {
            $pengaduan->status_pengaduan = $validated['status_pengaduan'];
        }

        $pengaduan->save();

        return back()->with('success', 'Data laporan berhasil diperbarui.');
    }

    public function destroyPengaduan(Pengaduan $pengaduan)
    {
        $pengaduan->delete();

        return back()->with('success', 'Laporan berhasil dihapus dan tidak akan muncul di laporan, riwayat, atau rekapitulasi.');
    }

    public function exportRekapsulasiExcel(Request $request)
    {
        $rows = $this->rekapsulasiQuery($request)->get();
        $lines = [];
        $lines[] = ['Tanggal', 'Pelapor', 'Lokasi Masalah', 'Fasilitas', 'Status', 'Deskripsi'];

        foreach ($rows as $item) {
            $lines[] = [
                optional($item->tanggal_lapor)->format('d/m/Y') ?: '-',
                data_get($item, 'pelapor.nama', 'Guest'),
                data_get($item, 'fasilitas.laboratorium.nama_laboratorium', '-'),
                data_get($item, 'fasilitas.nama_fasilitas', '-'),
                $this->statusLabel($item->status_pengaduan),
                $item->deskripsi_kerusakan ?: '-',
            ];
        }

        $csv = "\xEF\xBB\xBF";
        foreach ($lines as $line) {
            $escaped = array_map(function ($value) {
                $value = str_replace('"', '""', (string) $value);
                return '"' . $value . '"';
            }, $line);
            $csv .= implode(';', $escaped) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rekapitulasi-laporan.xls"',
        ]);
    }

    public function exportRekapsulasiPdf(Request $request)
    {
        $rows = $this->rekapsulasiQuery($request)->get();

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Rekapitulasi Laporan</title>'
            . '<style>body{font-family:Arial,sans-serif;color:#111827}h1{font-size:20px}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border:1px solid #d1d5db;padding:8px;text-align:left}th{background:#f3f4f6}@media print{button{display:none}}</style>'
            . '</head><body><button onclick="window.print()">Cetak / Simpan PDF</button><h1>Rekapitulasi Laporan</h1><table><thead><tr><th>Tanggal</th><th>Pelapor</th><th>Lokasi</th><th>Fasilitas</th><th>Status</th><th>Deskripsi</th></tr></thead><tbody>';

        foreach ($rows as $item) {
            $html .= '<tr>'
                . '<td>' . e(optional($item->tanggal_lapor)->format('d/m/Y') ?: '-') . '</td>'
                . '<td>' . e(data_get($item, 'pelapor.nama', 'Guest')) . '</td>'
                . '<td>' . e(data_get($item, 'fasilitas.laboratorium.nama_laboratorium', '-')) . '</td>'
                . '<td>' . e(data_get($item, 'fasilitas.nama_fasilitas', '-')) . '</td>'
                . '<td>' . e($this->statusLabel($item->status_pengaduan)) . '</td>'
                . '<td>' . e($item->deskripsi_kerusakan ?: '-') . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table><script>window.addEventListener("load",()=>setTimeout(()=>window.print(),400));</script></body></html>';

        return response($html);
    }

    public function importRekapsulasiTemplate()
    {
        $lines = [
            ['Tanggal', 'Pelapor', 'Lokasi Masalah', 'Fasilitas', 'Status', 'Deskripsi'],
            ['2026-06-25', 'Budi', 'Lab Startup', 'PC-01', 'NEW', 'Contoh deskripsi kerusakan'],
        ];

        $csv = "\xEF\xBB\xBF";
        foreach ($lines as $line) {
            $escaped = array_map(function ($value) {
                $value = str_replace('"', '""', (string) $value);
                return '"' . $value . '"';
            }, $line);
            $csv .= implode(';', $escaped) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-rekapitulasi.csv"',
        ]);
    }

    public function importRekapsulasi(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:4096'],
        ]);

        $path = $validated['file']->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return back()->withErrors(['file' => 'File tidak bisa dibaca.']);
        }

        $created = 0;
        $headerSkipped = false;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < 6) {
                $row = str_getcsv(implode(';', $row), ',');
            }

            if (!$headerSkipped) {
                $headerSkipped = true;
                if (isset($row[0]) && stripos((string) $row[0], 'tanggal') !== false) {
                    continue;
                }
            }

            [$tanggal, $pelaporNama, $lokasi, $fasilitasNama, $status, $deskripsi] = array_pad($row, 6, null);

            $pelapor = User::where('nama', trim((string) $pelaporNama))->first() ?: User::role('laboran')->first() ?: User::first();
            $fasilitas = FasilitasLab::activeQr()->where('nama_fasilitas', trim((string) $fasilitasNama))->first() ?: FasilitasLab::activeQr()->first();

            if (!$pelapor || !$fasilitas) {
                continue;
            }

            Pengaduan::create([
                'id_user' => $pelapor->id_user,
                'id_fasilitas' => $fasilitas->id_fasilitas,
                'tanggal_lapor' => $tanggal ? date('Y-m-d', strtotime((string) $tanggal)) : now()->toDateString(),
                'status_pengaduan' => $this->normalizeStatusCode($status),
                'deskripsi_kerusakan' => $deskripsi ?: 'Import rekapitulasi',
            ]);

            $created++;
        }

        fclose($handle);

        return back()->with('success', "Import selesai. {$created} laporan berhasil dibuat.");
    }



    protected function normalizeStatusCode(?string $status): string
    {
        $normalized = strtoupper(trim((string) $status));
        $normalized = str_replace(['-', ' '], '_', $normalized);

        return match ($normalized) {
            'NEW', 'BARU' => 'NEW',
            'HANDLED', 'ON_PROGRESS', 'ON_PROGRES', 'PROGRESS', 'PROGRES' => 'HANDLED',
            'DONE', 'SELESAI' => 'DONE',
            'CANCEL', 'CANCELLED', 'DIBATALKAN' => 'CANCEL',
            'NO_SPAREPART', 'NO_SPARE_PART', 'TIDAK_ADA_SPAREPART', 'TIDAK_ADA_SPARE_PART' => 'NO_SPAREPART',
            default => 'NEW',
        };
    }

    protected function statusLabel(?string $status): string
    {
        return match ($status) {
            'NEW' => 'New',
            'HANDLED' => 'On Progress',
            'DONE' => 'Done',
            'CANCEL' => 'Cancel',
            'NO_SPAREPART' => 'No Sparepart',
            default => $status ?: '-',
        };
    }

    protected function rekapsulasiQuery(Request $request)
    {
        $query = Pengaduan::with([
            'fasilitas.laboratorium',
            'pelapor',
            'statusData',
            'fotoUtama',
            'fotos',
            'tindakLanjut.asisten',
            'tindakLanjut.penugas',
        ]);

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_lapor', $request->input('tanggal'));
        }

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
                    ->orWhereHas('pelapor', function ($u) use ($keyword) {
                        $u->where('nama', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('fasilitas', function ($f) use ($keyword) {
                        $f->where('nama_fasilitas', 'like', "%{$keyword}%");
                    });
            });
        }

        return $request->input('sort') === 'terlama'
            ? $query->orderBy('tanggal_lapor')->orderBy('id_pengaduan')
            : $query->orderByDesc('tanggal_lapor')->orderByDesc('id_pengaduan');
    }

}
