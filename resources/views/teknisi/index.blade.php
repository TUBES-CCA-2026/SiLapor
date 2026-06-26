@extends('layouts.app')

@section('title', 'Daftar Teknisi - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.04); }
    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; position: sticky !important; top: 0; height: 100vh; }
        .hide-on-desktop { display: none !important; }
    }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    
    <!-- SIDEBAR KIRI -->
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </div>

            @php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'teknisi';
    $pageTitle = $pageTitle ?? strtoupper(str_replace('-', ' ', $activeMenu));

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $roleLabel = match($role) {
        'laboran' => 'Laboran',
        'koordinator_lab' => 'Koordinator Lab',
        'asisten' => 'Asisten Lab',
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
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } else {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    }
@endphp

            @php
                $menuItems = \App\Support\SidebarMenu::forRole($sidebarRole ?? $role ?? auth()->user()?->role);
            @endphp
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
                <i class="fa-solid fa-right-from-bracket text-lg"></i> <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-600">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h1 class="text-2xl font-extrabold text-[#2C3E50] uppercase tracking-wider">Daftar Teknisi</h1>
            </div>
            @include('partials.user-welcome-box', ['user' => $user ?? auth()->user()])
        </header>

        <section class="bg-white border border-gray-150 rounded-[32px] overflow-hidden shadow-figma-container p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[#64748B] text-xs font-extrabold uppercase tracking-wider border-b border-gray-150">
                        <th class="py-5 px-6">ID Teknisi</th>
                        <th class="py-5 px-6">Nama Teknisi</th>
                        <th class="py-5 px-6">Keahlian</th>
                        <th class="py-5 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($teknisi as $t)
                    <tr class="hover:bg-slate-50">
                        <td class="py-5 px-6 font-medium text-gray-500">TKN-{{ str_pad($t->id_teknisi, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-5 px-6 font-semibold text-gray-800">{{ $t->nama_teknisi }}</td>
                        <td class="py-5 px-6 text-gray-600">{{ $t->keahlian }}</td>
                        <td class="py-5 px-6"><span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Tersedia</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-10 text-center text-gray-400">Data teknisi belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
@endsection
