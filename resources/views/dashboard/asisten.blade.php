@extends('layouts.app')

@section('title', 'Dashboard Asisten - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-card { box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.03); }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #F1F5F9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
    
    /* Custom breakpoint untuk sidebar 850px */
    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }

    .nav-press-effect:active { transform: scale(.985); }
    .nav-active-effect { background: #F3F4F6; color: #111827; font-weight: 800; border-radius: 1.35rem; box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .02); }
    .nav-active-effect .nav-active-icon { color: #0090F5; }
</style>

@php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'dashboard';
    $pageTitle = $pageTitle ?? strtoupper(str_replace('-', ' ', $activeMenu));

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $roleLabel = match($role) {
        'laboran' => 'Laboran',
        'koordinator_lab' => 'Koordinator Lab',
        'asisten' => 'Asisten Lab',
        'admin' => 'Admin',
        default => 'User',
    };

    if ($role === 'laboran') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
            ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
            ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
            ['laboratorium', 'Laboratorium', 'fa-regular fa-building', $routeSafe('laboratorium.index')],
            ['fasilitas', 'Fasilitas & QR', 'fa-solid fa-qrcode', $routeSafe('fasilitas.index')],
            ['users', 'Kelola User', 'fa-solid fa-users-gear', $routeSafe('admin.users.index')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } elseif ($role === 'koordinator_lab') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
            ['penugasan', 'Penugasan', 'fa-solid fa-user-check', $routeSafe('penugasan.index')],
            ['detail-laporan', 'Detail Laporan', 'fa-regular fa-rectangle-list', $routeSafe('detail-laporan.index')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } elseif ($role === 'asisten') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['pengaduan', 'Pengaduan', 'fa-regular fa-file-lines', $routeSafe('pengaduan.index')],
            ['tindak-lanjut', 'Tindak Lanjut', 'fa-solid fa-screwdriver-wrench', $routeSafe('tindak-lanjut.index')],
            ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
            ['teknisi', 'Teknisi', 'fa-solid fa-triangle-exclamation', $routeSafe('teknisi.index')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } else {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    }
@endphp

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
<<<<<<< HEAD
<<<<<<< HEAD
    <!-- SIDEBAR KIRI (SINKRON DENGAN HALAMAN TINDAK LANJUT) -->
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <!-- Brand Logo SiLapor -->
            <div class="flex items-center gap-3 px-4">
=======
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto custom-scrollbar">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4">
>>>>>>> 2a3988f (bismillah)
=======
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto custom-scrollbar">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4">
>>>>>>> 1446e82 (Istigfar)
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </a>

<<<<<<< HEAD
<<<<<<< HEAD
            <!-- List Menu Navigasi -->
            @php
                $activeMenu = 'dashboard';
                $routeSafe = function (string $name, string $fallback = '#') {
                    return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
                };
                $sidebarUser = auth()->user();
                $sidebarRole = $sidebarUser?->role;

                if ($sidebarRole === 'laboran' || $sidebarRole === 'admin') {
                    $menuItems = [
                        ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
                        ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
                        ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
                        ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
                        ['laboratorium', 'Laboratorium', 'fa-regular fa-building', $routeSafe('laboratorium.index')],
                        ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
                    ];
                } elseif ($sidebarRole === 'koordinator_lab') {
                    $menuItems = [
                        ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
                        ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
                        ['penugasan', 'Penugasan', 'fa-solid fa-user-check', $routeSafe('penugasan.index')],
                        ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
                        ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
                    ];
                } elseif ($sidebarRole === 'asisten') {
                    $menuItems = [
                        ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
                        ['pengaduan', 'Pengaduan', 'fa-regular fa-file-lines', $routeSafe('pengaduan.index')],
                        ['tindak-lanjut', 'Tindak Lanjut', 'fa-solid fa-screwdriver-wrench', $routeSafe('tindak-lanjut.index')],
                        ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
                        ['teknisi', 'Teknisi', 'fa-solid fa-triangle-exclamation', $routeSafe('teknisi.index')],
                        ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
                    ];
                } else {
                    $menuItems = [
                        ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
                        ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
                    ];
                }
            @endphp

=======
            @php
                $menuItems = \App\Support\SidebarMenu::forRole($sidebarRole);
            @endphp
>>>>>>> 2a3988f (bismillah)
=======
            @php
                $menuItems = \App\Support\SidebarMenu::forRole($sidebarRole);
            @endphp
>>>>>>> 1446e82 (Istigfar)
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
        <!-- Logout Section -->
        <div class="mt-auto p-8 border-t border-gray-100 bg-white rounded-br-[36px] md:rounded-br-none">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden" onclick="toggleSidebar()"></div>

    <!-- KONTEN UTAMA -->
    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <!-- HEADER -->
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight">DASHBOARD ASISTEN</h1>
        </div>
        
        <div class="bg-[#0090F5] text-white px-4 py-2 rounded-2xl flex items-center gap-3 shadow-md w-full sm:w-auto">
            <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[#0090F5] shrink-0">
                <i class="fa-solid fa-user text-sm"></i>
            </div>
            <div class="text-left overflow-hidden">
                <span class="text-[10px] opacity-80 block uppercase tracking-wider">Selamat datang</span>
                <span class="text-xs font-bold block truncate">{{ Auth::user()->name ?? Auth::user()->nama ?? 'User' }}</span>
            </div>
        </div>
    </header>

    <!-- STATISTIK -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
    @php
    $stats = [
        ['Total Pengaduan', $totalPengaduan ?? 0, 'text-[#FF4D4D]', 'bg-[#FFEAEB]'],
        ['Sedang Diperbaiki', $sedangDiperbaiki ?? 0, 'text-[#0090F5]', 'bg-[#EAF5FE]'],
        ['Selesai', $selesai ?? 0, 'text-[#22C55E]', 'bg-[#E6F9EE]']
    ];
    @endphp
        @foreach($stats as $s)
        <div class="bg-white border rounded-3xl p-5 md:p-6 flex items-center shadow-figma-card">
            <div class="w-14 h-14 rounded-full {{ $s[3] }} flex items-center justify-center {{ $s[2] }} text-xl shrink-0">
                <i class="fa-solid {{ $loop->index == 0 ? 'fa-triangle-exclamation' : ($loop->index == 1 ? 'fa-screwdriver-wrench' : 'fa-circle-check') }}"></i>
            </div>
            <div class="ml-4">
                <p class="text-xs text-gray-500 font-bold">{{ $s[0] }}</p>
                <p class="text-2xl md:text-3xl font-extrabold text-[#2C3E50]">{{ ((int) $s[1]) > 0 ? (string) ((int) $s[1]) : '-' }}</p>
            </div>
        </div>
        @endforeach
    </section>

    @php
    $buatPengaduanRoute = Route::has('pengaduan.create')
        ? route('pengaduan.create')
        : (Route::has('pengaduan.index') ? route('pengaduan.index') : '#');
    @endphp

        <!-- TABEL -->
        <section class="bg-white border rounded-[32px] overflow-hidden shadow-figma-container">
            <div class="px-8 py-6 border-b flex flex-col sm:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-xl text-gray-800">Pengaduan Terbaru</h2>
                @if($buatPengaduanRoute !== '#')
                <a href="{{ $buatPengaduanRoute }}" class="bg-[#0090F5] hover:bg-blue-600 transition-colors text-white px-6 py-3 rounded-xl font-bold text-sm w-full sm:w-auto text-center">+ Buat Pengaduan</a>
                @endif
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left min-w-[1000px]">
                    <thead class="bg-[#F8FAFC] text-gray-500 uppercase text-xs font-extrabold tracking-wider border-b">
                        <tr>
                            <th class="py-5 px-6">ID PGD</th>
                            <th class="py-5 px-6">Pelapor</th>
                            <th class="py-5 px-6">Lokasi Masalah</th>
                            <th class="py-5 px-6">Fasilitas</th>
                            <th class="py-5 px-6">Tanggal Lapor</th>
                            <th class="py-5 px-6">Status</th>
                            <th class="py-5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tugas as $t)
                            @php
                                $idTugas = $t->id_tindak_lanjut ?? $t->id;
                                
                                // Deteksi Rute Update Dinamis untuk form
                                $updateAction = '#';
                                if (Route::has('tindak-lanjut.update')) {
                                    $updateAction = route('tindak-lanjut.update', $idTugas);
                                } elseif (Route::has('tindak_lanjut.update')) {
                                    $updateAction = route('tindak_lanjut.update', $idTugas);
                                } elseif (Route::has('tindaklanjut.update')) {
                                    $updateAction = route('tindaklanjut.update', $idTugas);
                                }
                            @endphp
                            <tr class="hover:bg-slate-50 cursor-pointer" onclick="toggleDetails('row-{{ $idTugas }}')">
                                <td class="py-5 px-6 text-sm font-semibold text-[#64748B]">PGD-{{ str_pad($t->id_pengaduan ?? $t->pengaduan?->id ?? $idTugas, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-5 px-6 text-sm text-gray-700 font-medium">{{ $t->pengaduan?->user?->nama ?? 'Tidak diketahui' }}</td>
                                <td class="py-5 px-6 text-sm text-gray-500">{{ $t->pengaduan?->fasilitas?->laboratorium?->nama_laboratorium ?? 'Lab. Computer Network' }}</td>
                                <td class="py-5 px-6 text-sm text-gray-800 font-semibold">{{ $t->pengaduan?->fasilitas?->nama_fasilitas ?? 'Komputer 01' }}</td>
                                <td class="py-5 px-6 text-sm text-gray-500">{{ $t->pengaduan?->created_at ? $t->pengaduan->created_at->format('d/m/Y') : '13/06/2026' }}</td>
                                <td class="py-5 px-6">
                                    @php
                                            $badgeClass = match($t->status_penanganan) {
                                                'DONE' => 'bg-[#E6F9EE] text-[#22C55E]',
                                                'CANCEL' => 'bg-[#FEE2E2] text-[#DC2626]',
                                                'NO SPAREPART' => 'bg-[#E5E7EB] text-[#374151]',
                                                default => 'bg-[#FFF9E6] text-[#FBBF24]',
                                            };
                                        @endphp
                                        <span class="inline-block text-xs font-bold px-4 py-1.5 rounded-md text-center {{ $badgeClass }}">
                                        {{ match($t->status_penanganan) { 'DONE' => 'Done', 'CANCEL' => 'Cancel', 'NO SPAREPART' => 'No Sparepart', default => 'On Progress' } }}
                                    </span>
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <span class="text-xs font-bold text-[#0090F5] bg-sky-50 px-4 py-2 rounded-lg">Detail</span>
                                </td>
                            </tr>
                            <tr id="row-{{ $idTugas }}" class="hidden bg-gray-50">
                                <td colspan="7" class="px-8 py-6 border-b">
                                    <form method="POST" action="{{ $updateAction }}" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                        @csrf 
                                        @method('PATCH')
                                        <div>
                                            <p class="text-sm font-bold text-gray-700 mb-2">Detail Kerusakan</p>
                                            <div class="bg-white p-4 rounded-2xl border border-gray-200 text-sm text-gray-600 italic">
                                                "{{ $t->pengaduan?->deskripsi_kerusakan ?? 'Tidak ada deskripsi.' }}"
                                            </div>
                                            @php
                                                $fotoKerusakanUrl = $t->pengaduan?->foto_kerusakan_url;
                                            @endphp
                                            @if($fotoKerusakanUrl)
                                                <div class="mt-4">
                                                    <p class="text-sm font-bold text-gray-700 mb-2">Foto Kerusakan</p>
                                                    <img src="{{ $fotoKerusakanUrl }}" alt="Foto Kerusakan" class="w-full max-h-48 object-cover rounded-2xl border border-gray-200 shadow-sm">
                                                </div>
                                            @endif
                                            <p class="text-sm font-bold text-gray-700 mt-4 mb-2">Catatan Perbaikan</p>
                                            <textarea name="catatan_perbaikan" class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0090F5]/20" rows="3" required>{{ $t->catatan_perbaikan }}</textarea>
                                        </div>
                                        <div class="flex flex-col gap-3">
                                            <p class="text-sm font-bold text-gray-700">Update Status</p>
                                            <select name="status_penanganan" class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0090F5]/20 bg-white">
                                                <option value="ON PROGRES" {{ $t->status_penanganan == 'ON PROGRES' ? 'selected' : '' }}>On Progress</option>
                                                <option value="DONE" {{ $t->status_penanganan == 'DONE' ? 'selected' : '' }}>Done</option>
                                                <option value="CANCEL" {{ $t->status_penanganan == 'CANCEL' ? 'selected' : '' }}>Cancel</option>
                                                <option value="NO SPAREPART" {{ $t->status_penanganan == 'NO SPAREPART' ? 'selected' : '' }}>No Sparepart</option>
                                            </select>
                                            <button type="submit" class="bg-[#0090F5] hover:bg-blue-600 transition-colors text-white px-6 py-3 rounded-2xl font-bold text-sm w-full">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-10 text-center text-gray-400">Belum ada pengaduan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
    function handleResize() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (window.innerWidth < 850) {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    window.addEventListener('resize', handleResize);
    window.addEventListener('load', handleResize);

    function toggleDetails(id) {
        const row = document.getElementById(id);
        row.classList.toggle('hidden');
    }
</script>
@endsection
