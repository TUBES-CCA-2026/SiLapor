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
</style>

@php
    // --- DETEKSI RUTE SECARA DINAMIS AGAR ANTI-CRASH ---
    $dashboardRoute = Route::has('dashboard') ? route('dashboard') : '#';
    
    $pengaduanRoute = '#';
    if (Route::has('pengaduan.index')) {
        $pengaduanRoute = route('pengaduan.index');
    } elseif (Route::has('pengaduans.index')) {
        $pengaduanRoute = route('pengaduans.index');
    }

    $tindakLanjutRoute = '#';
    if (Route::has('tindak-lanjut.index')) {
        $tindakLanjutRoute = route('tindak-lanjut.index');
    } elseif (Route::has('tindak_lanjut.index')) {
        $tindakLanjutRoute = route('tindak_lanjut.index');
    } elseif (Route::has('tindaklanjut.index')) {
        $tindakLanjutRoute = route('tindaklanjut.index');
    }

    $buatPengaduanRoute = '#';
    if (Route::has('pengaduan.manual.create')) {
        $buatPengaduanRoute = route('pengaduan.manual.create');
    } elseif (Route::has('pengaduan.create')) {
        $buatPengaduanRoute = route('pengaduan.create');
    } elseif (Route::has('pengaduans.create')) {
        $buatPengaduanRoute = route('pengaduans.create');
    }

@endphp

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    <!-- SIDEBAR KIRI (SINKRON DENGAN HALAMAN TINDAK LANJUT) -->
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white ... transition-all duration-300 -translate-x-full md:translate-x-0 md:sticky md:top-0 h-screen">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <!-- Brand Logo SiLapor -->
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </div>

            <!-- List Menu Navigasi -->
            <nav class="mt-10 space-y-2">
                <!-- Dashboard (ACTIVE) -->
                <a href="{{ $dashboardRoute }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-solid fa-table-columns text-lg text-[#0090F5]"></i>
                        <span>Dashboard</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>

                <!-- Pengaduan -->
                <a href="{{ $pengaduanRoute }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-file-lines text-lg"></i>
                    <span>Pengaduan</span>
                </a>

                <!-- Tindak Lanjut -->
                <a href="{{ $tindakLanjutRoute }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    <span>Tindak Lanjut</span>
                </a>

                <!-- Riwayat -->
                <a href="{{ route('riwayat.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Riwayat</span>
                </a>

                <!-- Teknisi -->
                <a href="{{ route('teknisi.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span>Teknisi</span>
                </a>

                <!-- Profil -->
                <a href="{{ route('profile.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-user text-lg"></i>
                    <span>Profil</span>
                </a>
            </nav>
        </div>

        <!-- Logout Section -->
        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px] md:rounded-br-none">
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
                <p class="text-2xl md:text-3xl font-extrabold text-[#2C3E50]">{{ str_pad($s[1], 3, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
        @endforeach
    </section>

    <!-- TABEL -->
    <section class="bg-white border rounded-3xl overflow-hidden shadow-figma-container">
        <div class="p-5 md:px-8 md:py-6 border-b flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-lg md:text-xl text-gray-800">Pengaduan Terbaru</h2>
            @if($buatPengaduanRoute !== '#')
            <a href="{{ route('pengaduan.create') }}" class="bg-[#0090F5] hover:bg-blue-600 transition-colors text-white px-5 py-2.5 rounded-xl font-bold text-sm w-full sm:w-auto text-center">+ Buat Pengaduan</a>
            @endif
        </div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left min-w-[800px]"> <!-- Set min-width agar tidak penyok di mobile -->
                <thead class="bg-[#F8FAFC] text-gray-400 uppercase text-[10px] md:text-xs font-extrabold tracking-wider border-b">
                    <tr>
                        <th class="py-4 px-6">ID</th>
                        <th class="py-4 px-6">Pelapor</th>
                        <th class="py-4 px-6">Lokasi</th>
                        <th class="py-4 px-6">Fasilitas</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($tugas as $t)
                        <tr class="hover:bg-slate-50 transition-colors cursor-pointer text-xs md:text-sm" onclick="toggleDetails('row-{{ $idTugas }}')">
                            <td class="py-4 px-6 font-bold text-[#64748B]">PGD-{{ str_pad($t->id_pengaduan ?? $t->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 px-6 text-gray-700 font-medium">{{ $t->pengaduan?->user?->name ?? 'Ray' }}</td>
                            <td class="py-4 px-6 text-gray-500">{{ $t->pengaduan?->fasilitas?->laboratorium?->nama_laboratorium ?? 'Lab' }}</td>
                            <td class="py-4 px-6 text-gray-800 font-semibold">{{ $t->pengaduan?->fasilitas?->nama_fasilitas ?? 'PC' }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-block text-[10px] font-bold px-3 py-1 rounded-lg {{ $t->status_penanganan === 'DONE' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                                    {{ $t->status_penanganan === 'DONE' ? 'Done' : 'Progress' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center text-[#0090F5] font-bold">Detail</td>
                        </tr>
                        <!-- Detail Row -->
                        <tr id="row-{{ $idTugas }}" class="hidden bg-gray-50">
                            <td colspan="6" class="p-4 md:p-6 border-b">
                                <!-- Form Grid -->
                                <form method="POST" action="{{ $updateAction }}" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    @csrf @method('PATCH')
                                    <textarea name="catatan_perbaikan" class="w-full border rounded-2xl p-3 text-sm focus:ring-2 focus:ring-[#0090F5]/20" rows="2" placeholder="Catatan perbaikan...">{{ $t->catatan_perbaikan }}</textarea>
                                    <div class="flex gap-2">
                                        <select name="status_penanganan" class="flex-1 border rounded-2xl p-3 text-sm bg-white">
                                            <option value="ON PROGRES" {{ $t->status_penanganan == 'ON PROGRES' ? 'selected' : '' }}>On Progress</option>
                                            <option value="DONE" {{ $t->status_penanganan == 'DONE' ? 'selected' : '' }}>Done</option>
                                        </select>
                                        <button type="submit" class="bg-[#0090F5] text-white px-6 rounded-2xl font-bold text-sm">Simpan</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-gray-400">Belum ada pengaduan.</td></tr>
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