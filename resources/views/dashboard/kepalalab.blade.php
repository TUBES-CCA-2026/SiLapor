@extends('layouts.app')

@section('title', 'Dashboard Kepala Lab - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $user = $user ?? auth()->user();
    $activeMenu = 'dashboard';

    $routeSafe = function (string $name, string $fallback = '#') {
    if (! \Illuminate\Support\Facades\Route::has($name)) {
        return $fallback;
    }

    try {
        return route($name);
    } catch (\Throwable $e) {
        return $fallback;
    }
};

$menuItems = [
    ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
    ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
    ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
    ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
    ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
];

    $menuItems = \App\Support\SidebarMenu::forRole($user?->role);

    $statusMeta = function ($status) {
        return match ($status) {
            'NEW' => ['label' => 'New', 'class' => 'new'],
            'HANDLED' => ['label' => 'On Progress', 'class' => 'progress'],
            'DONE' => ['label' => 'Done', 'class' => 'done'],
            'CANCEL' => ['label' => 'Cancel', 'class' => 'cancel'],
            'NO_SPAREPART' => ['label' => 'No Sparepart', 'class' => 'no-sparepart'],
            default => ['label' => $status ?: '-', 'class' => 'new'],
        };
    };
@endphp

@once
<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-card { box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.02); }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #F1F5F9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
    .laporan-status { min-width: 112px; height: 28px; padding: 0 10px; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px; font-size: 12px; font-weight: 800; white-space: nowrap; }
    .laporan-status.progress { color: #756000; background: #FFD400; }
    .laporan-status.done { color: #187C28; background: #59FF45; }
    .laporan-status.new { color: #095E9C; background: #D8ECFF; }
    .laporan-status.cancel { color: #B91C1C; background: #FEE2E2; }
    .laporan-status.no-sparepart { color: #374151; background: #E5E7EB; }

    .modal-backdrop { position: fixed; inset: 0; z-index: 60; padding: 20px; background: rgba(15, 23, 42, .35); display: grid; place-items: center; }
    .modal-backdrop[hidden] { display: none !important; }
    .modal-card { width: min(520px, 96vw); max-height: 92vh; overflow: hidden; border-radius: 1.5rem; background: #fff; box-shadow: 0 18px 35px rgba(0,0,0,.18); }
    .modal-header { height: 58px; padding: 0 20px; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; }
    .modal-header h2 { margin: 0; color: #404040; font-size: 16px; font-weight: 800; }
    .modal-close { border: 0; background: transparent; color: #4a4a4a; font-size: 38px; font-weight: 800; line-height: 1; cursor: pointer; padding: 0; }
    .modal-body { padding: 34px 32px 36px; overflow-y: auto; max-height: calc(92vh - 58px); }
    .detail-photo-wrap { display: grid; gap: 10px; width: min(100%, 280px); max-height: 360px; margin: 0 auto 20px; border: 1px solid #E5E7EB; border-radius: 18px; overflow-y: auto; background: #f1f1f1; padding: 8px; }
    .modal-photo { width: 100%; height: 160px; display: block; object-fit: cover; border-radius: 12px; }
    .modal-photo-placeholder { width: 100%; min-height: 160px; display: grid; place-items: center; color: #777; font-size: 13px; font-weight: 700; }
    .detail-panel { width: min(100%, 420px); margin: 0 auto; border: 1px solid #E5E7EB; border-radius: 20px; overflow: hidden; background: #f7f7f7; }
    .modal-row { min-height: 38px; display: grid; grid-template-columns: 96px 12px 1fr; align-items: center; padding: 0 16px; background: #f0f0f0; color: #555; font-size: 14px; }
    .modal-row:nth-child(even) { background: #e8e8e8; }
    .modal-row-description { min-height: 116px; align-items: start; padding-top: 14px; padding-bottom: 14px; }
    .description-box { min-height: 76px; padding: 14px; border: 1px solid #E5E7EB; border-radius: 18px; background: #fff; color: #555; line-height: 1.45; white-space: pre-wrap; }
    .status-badge { display: inline-block; padding: 2px 8px; border-radius: 5px; font-size: 11px; line-height: 1.25; }
    .status-badge.new { color: #0b5b9c; background: #d8ecff; }
    .status-badge.done { color: #0f7433; background: #d9f7e3; }
    .status-badge.progress { color: #6b5700; background: #ffe03d; }
    .status-badge.cancel { color: #B91C1C; background: #FEE2E2; }
    .status-badge.no-sparepart { color: #374151; background: #E5E7EB; }
    .loading-line { height: 13px; margin: 13px 0; border-radius: 30px; background: linear-gradient(90deg, #edf2f7, #f8fbff, #edf2f7); }
    .loading-line.short { width: 60%; }

    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto custom-scrollbar">
            <a href="{{ $routeSafe('dashboard') }}" class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </a>

            <nav class="mt-10 space-y-7">
                @foreach($menuItems as [$key, $label, $icon, $url])
                    @if($activeMenu === $key)
                        <a href="{{ $url }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                            <div class="flex items-center gap-3.5">
                                <i class="{{ $icon }} text-lg text-[#0090F5]"></i>
                                <span>{{ $label }}</span>
                            </div>
                            <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                        </a>
                    @else
                        <a href="{{ $url }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                            <i class="{{ $icon }} text-lg"></i>
                            <span>{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <div class="mt-auto p-8 border-t border-gray-100 bg-white rounded-br-[36px] md:rounded-br-none">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ $routeSafe('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden" onclick="toggleSidebar()"></div>

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6 overflow-x-hidden">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase font-figma">DASHBOARD</h1>
            </div>

            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        <div class="bg-white border border-gray-100 rounded-[36px] p-6 md:p-8 shadow-figma-container space-y-8">
            <section class="space-y-4">
                <h2 class="text-lg font-bold text-[#2C3E50] tracking-tight">Ringkasan Laporan</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white border border-gray-200 rounded-[20px] p-4 flex items-center gap-4 shadow-figma-card">
                        <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center text-[#0090F5] shrink-0"><i class="fa-regular fa-file-lines text-xl"></i></div>
                        <div><p class="text-xs font-bold text-gray-400">Total Laporan</p><p class="text-2xl font-extrabold text-[#2C3E50] leading-tight">{{ $totalLaporan ?? 0 }}</p><p class="text-[10px] text-gray-400 font-medium">Semua laporan</p></div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-[20px] p-4 flex items-center gap-4 shadow-figma-card">
                        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-[#22C55E] shrink-0"><i class="fa-regular fa-square-check text-xl"></i></div>
                        <div><p class="text-xs font-bold text-gray-400">Done</p><p class="text-2xl font-extrabold text-[#2C3E50] leading-tight">{{ $selesai ?? 0 }}</p><p class="text-[10px] text-gray-400 font-medium">Laporan done</p></div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-[20px] p-4 flex items-center gap-4 shadow-figma-card">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-[#F59E0B] shrink-0"><i class="fa-regular fa-clock text-xl"></i></div>
                        <div><p class="text-xs font-bold text-gray-400">Proses</p><p class="text-2xl font-extrabold text-[#2C3E50] leading-tight">{{ $proses ?? 0 }}</p><p class="text-[10px] text-gray-400 font-medium">Sedang diproses</p></div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-[20px] p-4 flex items-center gap-4 shadow-figma-card">
                        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-[#EF4444] shrink-0"><i class="fa-regular fa-circle-xmark text-xl"></i></div>
                        <div><p class="text-xs font-bold text-gray-400">Tertunda</p><p class="text-2xl font-extrabold text-[#2C3E50] leading-tight">{{ $tertunda ?? 0 }}</p><p class="text-[10px] text-gray-400 font-medium">Perlu diproses</p></div>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-[#2C3E50] tracking-tight">Daftar Laporan Terbaru</h2>

                <div class="border border-gray-300 rounded-[18px] overflow-hidden bg-white">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[900px]">
                            <thead>
                                <tr class="bg-[#034C5F] text-white text-sm font-semibold tracking-wide">
                                    <th class="py-3 px-6">Lokasi Masalah</th>
                                    <th class="py-3 px-6">Tanggal</th>
                                    <th class="py-3 px-6">Fasilitas</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($daftarLaporan ?? [] as $row)
                                    @php
                                        $status = $statusMeta($row->status_pengaduan ?? null);
                                        $detailUrl = \Illuminate\Support\Facades\Route::has('dashboard.pengaduan.detail')
                                            ? route('dashboard.pengaduan.detail', $row)
                                            : '#';
                                    @endphp
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3.5 px-6 text-sm text-gray-700 font-medium">
                                            <div class="flex items-center gap-3"><i class="fa-solid fa-location-dot text-[#EF4444]"></i><span>{{ $row->fasilitas?->laboratorium?->nama_laboratorium ?? 'Lab Tidak Diketahui' }}</span></div>
                                        </td>
                                        <td class="py-3.5 px-6 text-sm text-gray-500">{{ $row->tanggal_lapor ? \Carbon\Carbon::parse($row->tanggal_lapor)->format('d-m-Y') : ($row->created_at ? $row->created_at->format('d-m-Y') : '-') }}</td>
                                        <td class="py-3.5 px-6 text-sm text-gray-600">{{ $row->fasilitas?->kategori?->nama_kategori ?? '-' }} ({{ $row->fasilitas?->no_fasilitas ?? '-' }})</td>
                                        <td class="py-3.5 px-6 text-center"><span class="laporan-status {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                        <td class="py-3.5 px-6 text-center">
                                            <button type="button" class="inline-block text-xs font-bold text-[#0090F5] bg-white border border-[#0090F5] hover:bg-sky-50 px-5 py-1 rounded-md transition-all shadow-sm detail-btn" data-detail-url="{{ $detailUrl }}">Detail</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-10 text-center text-gray-400 font-medium italic">Belum ada data laporan terbaru yang masuk.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-xs text-gray-400 font-medium pl-2">
                    Menampilkan {{ count($daftarLaporan ?? []) }} dari {{ $totalLaporan ?? 0 }} laporan
                </div>
            </section>
        </div>

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
