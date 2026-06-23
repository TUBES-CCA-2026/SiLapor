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
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
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
    <main class="flex-1 px-4 py-6 md:px-10 md:py-8 space-y-8 overflow-x-hidden w-full min-w-0">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4">
            <div class="flex items-center gap-4">
                <!-- Hamburger menu untuk mobile & tablet di bawah 850px -->
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider">DASHBOARD ASISTEN LAB</h1>
            </div>
            
            <div class="bg-[#0090F5] text-white px-5 py-2.5 rounded-2xl flex items-center gap-3.5 shadow-md w-full sm:w-auto">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0090F5] shrink-0"><i class="fa-solid fa-user text-xl"></i></div>
                <div class="text-left flex flex-col justify-center leading-tight">
                    <span class="text-[11px] font-light opacity-90 block">Selamat datang,</span>
                    <span class="text-sm font-extrabold block tracking-wide">{{ Auth::user()->name ?? 'Asisten Lab' }}</span>
                </div>
            </div>
        </header>

        <!-- STATISTIK (DIBUNGKUS DENGAN FALLBACK AMAN) -->
        @php
            $tugas = $tugas ?? collect([]);
            $totalPengaduan = $tugas->count();
            $sedangDiperbaiki = $tugas->where('status_penanganan', 'ON PROGRES')->count();
            $selesai = $tugas->where('status_penanganan', 'DONE')->count();
        @endphp

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white border rounded-[28px] p-7 flex items-center shadow-figma-card">
                <div class="w-16 h-16 rounded-full bg-[#FFEAEB] flex items-center justify-center text-[#FF4D4D] text-2xl font-bold">!</div>
                <div class="ml-5"><p class="text-sm text-gray-500 font-bold">Total Pengaduan</p><p class="text-3xl font-extrabold text-[#2C3E50]">{{ str_pad($totalPengaduan, 3, '0', STR_PAD_LEFT) }}</p></div>
            </div>
            <div class="bg-white border rounded-[28px] p-7 flex items-center shadow-figma-card">
                <div class="w-16 h-16 rounded-full bg-[#EAF5FE] flex items-center justify-center text-[#0090F5] text-2xl"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                <div class="ml-5"><p class="text-sm text-gray-500 font-bold">Sedang Diperbaiki</p><p class="text-3xl font-extrabold text-[#2C3E50]">{{ str_pad($sedangDiperbaiki, 3, '0', STR_PAD_LEFT) }}</p></div>
            </div>
            <div class="bg-white border rounded-[28px] p-7 flex items-center shadow-figma-card sm:col-span-2 lg:col-span-1">
                <div class="w-16 h-16 rounded-full bg-[#E6F9EE] flex items-center justify-center text-[#22C55E] text-2xl"><i class="fa-solid fa-circle-check"></i></div>
                <div class="ml-5"><p class="text-sm text-gray-500 font-bold">Selesai</p><p class="text-3xl font-extrabold text-[#2C3E50]">{{ str_pad($selesai, 3, '0', STR_PAD_LEFT) }}</p></div>
            </div>
        </section>

        <!-- TABEL -->
        <section class="bg-white border rounded-[32px] overflow-hidden shadow-figma-container">
            <div class="px-8 py-6 border-b flex flex-col sm:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-xl text-gray-800">Pengaduan Terbaru</h2>
                @if($buatPengaduanRoute !== '#')
                <a href="{{ route('pengaduan.create') }}"class="bg-[#0090F5] hover:bg-blue-600 transition-colors text-white px-6 py-3 rounded-xl font-bold text-sm w-full sm:w-auto text-center">+ Buat Pengaduan</a>
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
                                <td class="py-5 px-6 text-sm text-gray-700 font-medium">{{ $t->pengaduan?->user?->name ?? 'Ray' }}</td>
                                <td class="py-5 px-6 text-sm text-gray-500">{{ $t->pengaduan?->fasilitas?->laboratorium?->nama_laboratorium ?? 'Lab. Computer Network' }}</td>
                                <td class="py-5 px-6 text-sm text-gray-800 font-semibold">{{ $t->pengaduan?->fasilitas?->nama_fasilitas ?? 'Komputer 01' }}</td>
                                <td class="py-5 px-6 text-sm text-gray-500">{{ $t->pengaduan?->created_at ? $t->pengaduan->created_at->format('d/m/Y') : '13/06/2026' }}</td>
                                <td class="py-5 px-6">
                                    <span class="inline-block text-xs font-bold px-4 py-1.5 rounded-md text-center {{ $t->status_penanganan === 'DONE' ? 'bg-[#E6F9EE] text-[#22C55E]' : 'bg-[#FFF9E6] text-[#FBBF24]' }}">
                                        {{ $t->status_penanganan === 'DONE' ? 'Done' : 'On Progress' }}
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
                                            <p class="text-sm font-bold text-gray-700 mt-4 mb-2">Catatan Perbaikan</p>
                                            <textarea name="catatan_perbaikan" class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0090F5]/20" rows="3" required>{{ $t->catatan_perbaikan }}</textarea>
                                        </div>
                                        <div class="flex flex-col gap-3">
                                            <p class="text-sm font-bold text-gray-700">Update Status</p>
                                            <select name="status_penanganan" class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0090F5]/20 bg-white">
                                                <option value="ON PROGRES" {{ $t->status_penanganan == 'ON PROGRES' ? 'selected' : '' }}>On Progress</option>
                                                <option value="DONE" {{ $t->status_penanganan == 'DONE' ? 'selected' : '' }}>Done</option>
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