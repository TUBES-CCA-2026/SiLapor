@extends('layouts.app')

@section('title', 'Rekapitulasi | SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.page-styles')

@php
    $user = auth()->user();
    $activeMenu = 'rekapsulasi';
    $rows = $daftarLaporan ?? collect();
    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };
    $statusMeta = function ($status) {
        return match ($status) {
            'NEW' => ['label' => 'Baru', 'class' => 'new'],
            'HANDLED' => ['label' => 'On Progress', 'class' => 'progress'],
            'DONE' => ['label' => 'Selesai', 'class' => 'done'],
            'CANCEL' => ['label' => 'Cancel', 'class' => 'cancel'],
            'NO_SPAREPART' => ['label' => 'No Sparepart', 'class' => 'no-sparepart'],
            default => ['label' => $status ?: '-', 'class' => 'new'],
        };
    };
@endphp

@once
<style>
    .filter-card { background:#fff; border:1px solid #E5E7EB; border-radius:2rem; box-shadow:0 15px 50px rgba(0,0,0,.05); padding:1.5rem; }
    .table-card { background:#fff; border:1px solid #E5E7EB; border-radius:2rem; box-shadow:0 15px 50px rgba(0,0,0,.05); overflow:hidden; }
    .form-control { width:100%; border:1px solid #D1D5DB; border-radius:.875rem; padding:.75rem 1rem; background:#fff; outline:none; font-size:.875rem; }
    .form-control:focus { border-color:#0090F5; box-shadow:0 0 0 3px rgba(0,144,245,.14); }
    .btn-primary { background:#0090F5; color:#fff; border:0; border-radius:.875rem; padding:.75rem 1rem; font-weight:800; cursor:pointer; }
    .btn-secondary { background:#fff; color:#475569; border:1px solid #D1D5DB; border-radius:.875rem; padding:.75rem 1rem; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .btn-export-pdf { background:#fff; color:#DC2626; border:1px solid #FCA5A5; border-radius:.875rem; padding:.75rem 1rem; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .btn-export-pdf:hover { background:#DC2626; color:#fff; }
    .btn-export-excel { background:#fff; color:#16A34A; border:1px solid #86EFAC; border-radius:.875rem; padding:.75rem 1rem; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .btn-export-excel:hover { background:#16A34A; color:#fff; }
    .import-form { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
    .report-table { width:100%; border-collapse:collapse; min-width:980px; }
    .report-table thead { background:#F8FAFC; color:#64748B; text-transform:uppercase; font-size:.75rem; font-weight:800; letter-spacing:.04em; }
    .report-table th,.report-table td { padding:1rem 1.25rem; text-align:left; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
    .report-table td { font-size:.875rem; color:#374151; }
    .report-table tr:hover td { background:#F8FAFC; }
    .laporan-status { min-width:112px; height:28px; padding:0 10px; display:inline-flex; align-items:center; justify-content:center; border-radius:7px; font-size:12px; font-weight:800; white-space:nowrap; }
    .laporan-status.progress { color:#756000; background:#FFD400; }
    .laporan-status.done { color:#187C28; background:#59FF45; }
    .laporan-status.new { color:#095E9C; background:#D8ECFF; }
    .laporan-status.cancel { color:#B91C1C; background:#FEE2E2; }
    .laporan-status.no-sparepart { color:#374151; background:#E5E7EB; }
    .detail-btn { border:1px solid #0090F5; color:#0090F5; background:#EEF8FF; border-radius:.5rem; padding:.45rem 1rem; font-size:.8rem; font-weight:800; cursor:pointer; text-decoration:none; }
    .detail-btn:hover { background:#0090F5; color:#fff; }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['user' => $user, 'activeMenu' => $activeMenu])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6 overflow-x-hidden">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Rekapitulasi</h1>
            </div>
            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        <section class="filter-card">
            <div class="mb-4">
                <h2 class="text-lg font-extrabold text-[#2C3E50]">Filter Laporan</h2>
            </div>
            <form action="{{ $routeSafe('rekapsulasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control">
                <select name="id_penanggung_jawab" class="form-control">
                    <option value="">Semua Penanggung Jawab</option>
                    @foreach(($penanggungJawabs ?? collect()) as $pj)
                        <option value="{{ $pj->id_user }}" @selected((string) request('id_penanggung_jawab') === (string) $pj->id_user)>{{ $pj->nama }}</option>
                    @endforeach
                </select>
                <select name="id_laboratorium" class="form-control">
                    <option value="">Semua Lokasi</option>
                    @foreach(($laboratoriums ?? collect()) as $lab)
                        <option value="{{ $lab->id_laboratorium }}" @selected((string) request('id_laboratorium') === (string) $lab->id_laboratorium)>{{ $lab->nama_laboratorium }}</option>
                    @endforeach
                </select>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari laporan..." class="form-control">
                <select name="sort" class="form-control">
                    <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>Terbaru</option>
                    <option value="terlama" @selected(request('sort') === 'terlama')>Terlama</option>
                </select>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="NEW" @selected(request('status') === 'NEW')>Baru</option>
                    <option value="HANDLED" @selected(request('status') === 'HANDLED')>On Progress</option>
                    <option value="DONE" @selected(request('status') === 'DONE')>Selesai</option>
                </select>
                <select name="id_fasilitas" class="form-control">
                    <option value="">Semua Fasilitas</option>
                    @foreach(($fasilitasList ?? collect()) as $fasilitas)
                        <option value="{{ $fasilitas->id_fasilitas }}" @selected((string) request('id_fasilitas') === (string) $fasilitas->id_fasilitas)>{{ $fasilitas->nama_fasilitas }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1"><i class="fa-solid fa-filter mr-2"></i>Filter</button>
                    <a href="{{ $routeSafe('rekapsulasi.index') }}" class="btn-secondary"><i class="fa-solid fa-rotate-left mr-2"></i>Reset</a>
                </div>
            </form>
            <div class="mt-4 flex flex-col lg:flex-row justify-between gap-3">
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('rekapsulasi.export.pdf', request()->query()) }}" class="btn-export-pdf"><i class="fa-solid fa-file-pdf mr-2"></i>Cetak PDF</a>
                    <a href="{{ route('rekapsulasi.export.excel', request()->query()) }}" class="btn-export-excel"><i class="fa-solid fa-file-excel mr-2"></i>Cetak Excel</a>
                </div>
                @if(auth()->user()?->role === 'laboran')
                    <form action="{{ route('rekapsulasi.import') }}" method="POST" enctype="multipart/form-data" class="import-form">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt,.xls,.xlsx" class="form-control" style="max-width: 280px;">
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-file-import mr-2"></i>Import Spreadsheet</button>
                    </form>
                @endif
            </div>
        </section>

        <section class="table-card">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-extrabold text-[#2C3E50]">Daftar Hasil Rekapitulasi</h2>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pelapor</th>
                            <th>Lokasi Masalah</th>
                            <th>Fasilitas</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $item)
                            @php
                                $status = $statusMeta($item->status_pengaduan ?? null);
                                $detailUrl = route('dashboard.pengaduan.detail', $item);
                            @endphp
                            <tr>
                                <td>{{ $item->tanggal_lapor ? \Carbon\Carbon::parse($item->tanggal_lapor)->format('d/m/Y') : '-' }}</td>
                                <td>{{ data_get($item, 'pelapor.nama', 'Guest') }}</td>
                                <td>{{ data_get($item, 'fasilitas.laboratorium.nama_laboratorium', '-') }}</td>
                                <td>{{ data_get($item, 'fasilitas.nama_fasilitas', '-') }}</td>
                                <td><span class="laporan-status {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                <td class="text-center"><button type="button" class="detail-btn" data-detail-url="{{ $detailUrl }}">Detail</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 text-center text-gray-400">Belum ada data rekapitulasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($rows, 'links'))
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $rows->withQueryString()->links() }}
                </div>
            @endif
        </section>
        @include('partials.detail-modal')
    </main>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
@endsection
