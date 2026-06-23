@extends('layouts.app')

<<<<<<< HEAD
@section('title', 'Pengaduan - SiLapor')

@section('content')
=======
@section('title', 'Form Pengaduan - SiLapor')

@section('content')
<!-- Import Google Fonts & FontAwesome untuk icon persis seperti Figma -->
>>>>>>> b342d8f (dashboard-pengaduan)
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
<<<<<<< HEAD
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-container { box-shadow: 0 15px 50px rgba(0, 0, 0, .05); }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }
</style>

@php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'pengaduan';
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
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto custom-scrollbar">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4">
=======
    /* Custom CSS untuk mendapatkan presisi pixel-perfect sesuai Figma PENGADUAN.png */
    .font-figma {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .shadow-figma-container {
        box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05);
    }
    
    /* Menghilangkan panah bawaan select agar serasi seperti mockup */
    .select-clean {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%237E8B9B' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1.25rem center;
        background-size: 1rem;
    }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    
    <!-- SIDEBAR KIRI PENUH (Dari Atas ke Bawah - Persis Figma) -->
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:sticky md:top-0 md:h-screen rounded-r-[36px] shadow-sm shrink-0">
        
        <!-- Bagian Atas Sidebar (Logo & Navigasi) -->
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <!-- Brand Logo SiLapor -->
            <div class="flex items-center gap-3 px-4">
>>>>>>> b342d8f (dashboard-pengaduan)
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
<<<<<<< HEAD
            </a>

            @php
<<<<<<< HEAD
<<<<<<< HEAD
                $activeMenu = 'pengaduan';
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
                $menuItems = \App\Support\SidebarMenu::forRole($sidebarRole);
            @endphp
>>>>>>> 2a3988f (bismillah)
=======
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
        <div class="mt-auto p-8 border-t border-gray-100 bg-white rounded-br-[36px] md:rounded-br-none">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden" onclick="toggleSidebar()"></div>

    <!-- KONTEN UTAMA -->
    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-8">
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Pengaduan</h1>
    </div>
    
    <div class="bg-[#0090F5] text-white px-4 py-2 rounded-2xl flex items-center gap-3 shadow-md w-full sm:w-auto">
        <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[#0090F5] shrink-0">
            <i class="fa-solid fa-user text-sm"></i>
        </div>
        <div class="text-left overflow-hidden">
            <span class="text-[10px] opacity-80 block uppercase tracking-wider">Selamat datang</span>
            <span class="text-xs font-bold block truncate">
                {{ Auth::user()->name ?? Auth::user()->nama ?? 'User' }}
            </span>
        </div>
    </div>
    </header>

        <div class="bg-white p-8 rounded-[32px] shadow-figma-container border border-gray-150 w-full">
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-gray-800">Form Pengaduan Kerusakan</h2>
                <p class="text-sm text-gray-500 mt-1">Nama pelapor otomatis dari akun login. Pilih fasilitas agar kode barang, nama fasilitas, dan lokasi lab terisi otomatis.</p>
            </div>
<form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Pelapor</label>
                        <input type="text" value="{{ auth()->user()->nama ?? '-' }}" readonly
                               class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fasilitas yang Dilaporkan</label>
                        @php
                                $selectedFacilityId = (string) old(
                                    'id_fasilitas',
                                    request('id_fasilitas', '')
                                );

                                $selectedFacility = collect($facilities ?? [])->first(function ($facility) use ($selectedFacilityId) {
                                    return (string) $facility->id_fasilitas === $selectedFacilityId;
                                });

                                $selectedKodeBarang = $selectedFacility?->no_fasilitas ?? '-';
                                $selectedNamaFasilitas = $selectedFacility?->nama_fasilitas ?? '-';

                                $selectedLabName = $selectedFacility?->laboratorium?->nama_laboratorium ?? '-';
                                $selectedLabLocation = $selectedFacility?->laboratorium?->lokasi;

                                $selectedLokasiLab = $selectedLabLocation
                                    ? $selectedLabName . ' - ' . $selectedLabLocation
                                    : $selectedLabName;
                            @endphp
                        <select id="id_fasilitas" name="id_fasilitas" required
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]">
                            <option value="">— Pilih fasilitas —</option>
                            @foreach ($facilities as $item)
                                <option value="{{ $item->id_fasilitas }}" @selected((string) $selectedFacilityId === (string) $item->id_fasilitas)>
                                    {{ $item->nama_fasilitas }}
                                    @if ($item->no_fasilitas)
                                        ({{ $item->no_fasilitas }})
                                    @endif
                                    — {{ $item->laboratorium?->nama_laboratorium ?? '-' }}
                                </option>
                            @endforeach
=======
            </div>

            <!-- List Menu Navigasi -->
            <nav class="mt-10 space-y-2">
                <!-- Dashboard (Inaktif di halaman Pengaduan) -->
                <a href="/dashboard" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-table-columns text-lg"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Pengaduan (ACTIVE) -->
                <a href="/pengaduan" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-regular fa-file-lines text-lg text-[#0090F5]"></i>
                        <span>Pengaduan</span>
                    </div>
                    <!-- Indicator bar vertical biru kanan -->
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>

                <!-- Tindak Lanjut -->
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    <span>Tindak Lanjut</span>
                </a>

                <!-- Riwayat -->
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Riwayat</span>
                </a>

                <!-- Teknisi -->
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span>Teknisi</span>
                </a>

                <!-- Profil -->
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-user text-lg"></i>
                    <span>Profil</span>
                </a>
            </nav>
        </div>

        <!-- Bagian Bawah Sidebar (Logout - Terintegrasi Form Laravel) -->
        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px]">
            <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            
            <!-- Form Logout -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Overlay Latar Belakang untuk Mobile Sidebar -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- KONTEN UTAMA (Kanan) -->
    <main class="flex-1 px-6 py-6 md:px-10 md:py-8 space-y-8 overflow-x-hidden">
        
        <!-- HEADER NAVBAR SEJAJAR (Persis Figma) -->
        <header class="flex items-center justify-between pb-4">
            <div class="flex items-center gap-4">
                <!-- Hamburger menu untuk mobile & tablet -->
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none md:hidden">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Pengaduan</h1>
            </div>

            <!-- Profil Widget Kanan (Persis Figma) -->
            <div class="bg-[#0090F5] text-white px-5 py-2.5 rounded-2xl flex items-center gap-3.5 shadow-md">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0090F5] shrink-0">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div class="text-left flex flex-col justify-center leading-tight min-w-[100px]">
                    <span class="text-[11px] font-light opacity-90 block">Selamat datang,</span>
                    <span class="text-sm font-extrabold block tracking-wide">
                        {{ Auth::check() ? (Auth::user()->name ?: (Auth::user()->nama ?: (Auth::user()->username ?: 'Budi Asisten'))) : 'Budi Asisten' }}
                    </span>
                </div>
            </div>
        </header>

        <!-- CONTAINER FORM UTAMA (Persis Figma PENGADUAN.png) -->
        <section class="max-w-4xl mx-auto bg-white border border-gray-150 rounded-[32px] p-8 md:p-10 shadow-figma-container">
            <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Baris 1: Pelapor & Lokasi Masalah (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Input Pelapor -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-bold text-sm tracking-wide">Pelapor</label>
                        <select name="id_user" required class="select-clean w-full px-5 py-3.5 text-sm bg-white border border-gray-200 focus:border-[#0090F5] focus:ring-2 focus:ring-[#0090F5]/10 rounded-2xl outline-none transition-all text-gray-500">
                            <option value="">Pilih</option>
                            <!-- Daftar Pelapor Dinamis -->
                            @isset($users)
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            @else
                                <option value="{{ Auth::id() }}">{{ Auth::check() ? (Auth::user()->name ?: 'Budi Asisten') : 'Budi Asisten' }}</option>
                            @endisset
                        </select>
                    </div>

                    <!-- Input Lokasi Masalah -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-bold text-sm tracking-wide">Lokasi Masalah</label>
                        <select name="id_laboratorium" required class="select-clean w-full px-5 py-3.5 text-sm bg-white border border-gray-200 focus:border-[#0090F5] focus:ring-2 focus:ring-[#0090F5]/10 rounded-2xl outline-none transition-all text-gray-500">
                            <option value="">Pilih</option>
                            <!-- Daftar Laboratorium Dinamis -->
                            @isset($laboratoriums)
                                @foreach($laboratoriums as $lab)
                                    <option value="{{ $lab->id_laboratorium }}">{{ $lab->nama_laboratorium }}</option>
                                @endforeach
                            @else
                                <option value="1">Lab. Computer Network</option>
                                <option value="2">Lab. Software Engineering</option>
                                <option value="3">Lab. Multimedia & Game</option>
                            @endisset
>>>>>>> b342d8f (dashboard-pengaduan)
                        </select>
                    </div>
                </div>

<<<<<<< HEAD
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kode Barang</label>
                        <input
                                id="kode_barang"
                                type="text"
                                value="{{ $selectedKodeBarang }}"
                                readonly
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600"
                            >
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Fasilitas</label>
                        <input
                            id="nama_fasilitas"
                            type="text"
                            value="{{ $selectedNamaFasilitas }}"
                            readonly
                            class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600"
                        >                    
            </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Lab</label>
                        <input
                            id="lokasi_lab"
                            type="text"
                            value="{{ $selectedLokasiLab }}"
                            readonly
                            class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Kerusakan</label>
                    <textarea name="deskripsi_kerusakan" rows="5" required
                              class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]"
                              placeholder="Jelaskan kerusakan fasilitas secara singkat dan jelas.">{{ old('deskripsi_kerusakan') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload Foto Kerusakan</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center bg-[#F8FAFC] cursor-pointer hover:border-[#0090F5] transition-colors">
                        <input type="file" name="foto_kerusakan" accept="image/*" class="hidden" id="fileInput" required>
                        <label for="fileInput" class="flex flex-col items-center cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-[#0090F5] mb-2"></i>
                            <span id="fileLabel" class="text-[#0090F5] font-bold">Upload foto</span>
                            <span class="text-xs text-gray-400 mt-1">Klik untuk memilih foto format JPG/PNG/WEBP, maksimal 4 MB</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <button type="submit" {{ $facilities->isEmpty() ? 'disabled' : '' }}
                            class="w-full bg-[#0090F5] hover:bg-[#007cd5] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold py-4 rounded-2xl shadow-md transition-all">
                        Kirim Pengaduan
                    </button>

                    <a href="{{ route('scan.index') }}" class="flex items-center justify-center gap-2 text-gray-500 hover:text-[#0090F5] transition-colors font-medium text-sm">
                        <i class="fa-solid fa-qrcode"></i>
                        Gunakan QR Code untuk pelaporan instan
                    </a>
                </div>
            </form>
        </div>
=======
                <!-- Baris 2: Fasilitas (Penuh) -->
                <div class="space-y-2">
                    <label class="block text-gray-700 font-bold text-sm tracking-wide">Fasilitas</label>
                    <input type="text" name="nama_fasilitas" placeholder="" required
                           class="w-full px-5 py-3.5 text-sm bg-white border border-gray-200 focus:border-[#0090F5] focus:ring-2 focus:ring-[#0090F5]/10 rounded-2xl outline-none transition-all text-gray-800">
                </div>

                <!-- Baris 3: Deskripsi Kerusakan (Penuh) -->
                <div class="space-y-2">
                    <label class="block text-gray-700 font-bold text-sm tracking-wide">Deskripsi kerusakan</label>
                    <textarea name="deskripsi_kerusakan" rows="6" required
                              class="w-full px-5 py-4 text-sm bg-white border border-gray-200 focus:border-[#0090F5] focus:ring-2 focus:ring-[#0090F5]/10 rounded-[24px] outline-none transition-all text-gray-800 leading-relaxed"></textarea>
                </div>

                <!-- Baris 4: Upload Foto Kerusakan (Penuh) -->
                <div class="space-y-2">
                    <label class="block text-gray-700 font-bold text-sm tracking-wide">Upload foto kerusakan</label>
                    
                    <div onclick="document.getElementById('file-upload').click()" 
                         class="border-2 border-dashed border-[#CDE5FC] rounded-[24px] p-8 flex flex-col items-center justify-center gap-3 bg-[#F8FAFC] hover:bg-sky-50/50 cursor-pointer transition-all">
                        
                        <!-- Upload Icon Cloud -->
                        <div class="text-[#0090F5]">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        
                        <!-- Upload Texts -->
                        <div class="text-center space-y-1">
                            <span class="text-[#0090F5] font-extrabold text-sm block">Upload foto</span>
                            <span id="file-name" class="text-xs text-gray-400 block">klik untuk memilih foto</span>
                            <span class="text-xs text-gray-400 block">format: jpg/png</span>
                        </div>
                        
                        <!-- Input File Asli (Tersembunyi) -->
                        <input id="file-upload" type="file" name="foto_kerusakan" accept="image/*" class="hidden" onchange="showFileName(this)">
                    </div>
                </div>

                <!-- Tombol Submit: Selesai -->
                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#0090F5] hover:bg-[#007cd5] text-white py-4 rounded-[20px] font-extrabold text-lg shadow-md transition-colors tracking-wide">
                        Selesai
                    </button>
                </div>
            </form>
        </section>
>>>>>>> b342d8f (dashboard-pengaduan)
    </main>
</div>

<script>
<<<<<<< HEAD
    const facilities = @json($facilityPayload);
    const facilityMap = Object.fromEntries(facilities.map((item) => [String(item.id), item]));

    function fillFacilityDetail(id) {
        const detail = facilityMap[String(id)] || null;
        document.getElementById('kode_barang').value = detail?.kode_barang || '-';
        document.getElementById('nama_fasilitas').value = detail?.nama_fasilitas || '-';
        document.getElementById('lokasi_lab').value = detail?.lokasi_lab || '-';
    }

    const facilitySelect = document.getElementById('id_fasilitas');
    if (facilitySelect) {
        facilitySelect.addEventListener('change', function () {
            fillFacilityDetail(this.value);
        });
        fillFacilityDetail(facilitySelect.value);
    }

    const fileInput = document.getElementById('fileInput');
    const fileLabel = document.getElementById('fileLabel');
    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function () {
            fileLabel.textContent = this.files?.[0]?.name || 'Upload foto';
        });
    }

    function handleResponsiveSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');

        if (window.innerWidth < 850) {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.add('hidden');
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');

=======
    // Menampilkan nama file setelah dipilih
    function showFileName(input) {
        const fileNameSpan = document.getElementById('file-name');
        if (input.files && input.files[0]) {
            fileNameSpan.innerText = input.files[0].name;
            fileNameSpan.classList.remove('text-gray-400');
            fileNameSpan.classList.add('text-[#22C55E]', 'font-bold');
        }
    }

    // Fungsi interaktif buka/tutup sidebar mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        
>>>>>>> b342d8f (dashboard-pengaduan)
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
        }
    }
<<<<<<< HEAD

    window.addEventListener('resize', handleResponsiveSidebar);
    window.addEventListener('load', handleResponsiveSidebar);
</script>
@endsection
=======
</script>
@endsection
>>>>>>> b342d8f (dashboard-pengaduan)
