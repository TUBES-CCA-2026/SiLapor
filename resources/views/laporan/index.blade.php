@extends('layouts.app')

@section('title', 'Laporan Kepala Lab - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #F1F5F9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:sticky md:top-0 md:h-screen rounded-r-[36px] shadow-sm shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </div>

            <nav class="mt-10 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-table-columns text-lg"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('laporan.index') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-solid fa-file-invoice text-lg text-[#0090F5]"></i>
                        <span>Laporan</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>

                <a href="{{ route('riwayat.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Riwayat</span>
                </a>

                <a href="{{ route('rekapsulasi.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                    <span>Rekapsulasi</span>
                </a>

                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-user text-lg"></i>
                    <span>Profil</span>
                </a>
            </nav>
        </div>

        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px]">
            <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <main class="flex-1 px-6 py-6 md:px-10 md:py-8 space-y-6 overflow-x-hidden">
        
        <header class="flex items-center justify-between pb-2">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none md:hidden">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase font-figma">LAPORAN</h1>
            </div>

            <div class="bg-[#0090F5] text-white px-5 py-2.5 rounded-2xl flex items-center gap-3.5 shadow-md">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0090F5] shrink-0">
                    <i class="fa-solid fa-user-tie text-xl"></i>
                </div>
                <div class="text-left flex flex-col justify-center leading-tight min-w-[110px]">
                    <span class="text-[11px] font-light opacity-90 block">Selamat datang,</span>
                    <span class="text-sm font-extrabold block tracking-wide">
                        {{ Auth::check() ? (Auth::user()->name ?: (Auth::user()->nama ?: 'Kepala Lab')) : 'Kepala Lab' }}
                    </span>
                </div>
            </div>
        </header>

        <div class="bg-white border border-gray-150 rounded-[36px] p-6 md:p-8 shadow-figma-container space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-lg font-bold text-[#2C3E50] tracking-tight">Daftar Laporan</h2>
                
                <form action="{{ route('laporan.index') }}" method="GET" class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari laporan..." 
                           class="w-full pl-4 pr-12 py-2.5 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-[#0090F5] font-medium transition-all">
                    <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-gray-800">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </button>
                </form>
            </div>

            <div class="border border-gray-300 rounded-[18px] overflow-hidden bg-white">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead>
                            <tr class="bg-[#034C5F] text-white text-sm font-semibold tracking-wide">
                                <th class="py-3.5 px-6">Tanggal</th>
                                <th class="py-3.5 px-6">Pelapor</th>
                                <th class="py-3.5 px-6">Fasilitas</th>
                                <th class="py-3.5 px-6">Lokasi Masalah</th>
                                <th class="py-3.5 px-6">Deskripsi Kerusakan</th>
                                <th class="py-3.5 px-6 text-center">Status</th>
                                <th class="py-3.5 px-6 text-center"></th> </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($daftarLaporan as $row)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3.5 px-6 text-sm text-gray-600">
                                        {{ $row->created_at ? $row->created_at->format('d-m-Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-sm text-gray-700 font-medium">
                                        {{ $row->user->name ?? ($row->user->nama ?? 'User') }}
                                    </td>
                                    <td class="py-3.5 px-6 text-sm text-gray-600">
                                        {{ $row->fasilitas->nama_fasilitas ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-sm text-gray-700 font-medium">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-location-dot text-[#EF4444]"></i>
                                            <span>{{ $row->fasilitas->laboratorium->nama_laboratorium ?? 'Lab Tidak Diketahui' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-6 text-sm text-gray-500 italic max-w-xs truncate">
                                        "{{ $row->deskripsi_kerusakan ?? '-' }}"
                                    </td>
                                    <td class="py-3.5 px-6 text-center">
                                        <span class="inline-flex items-center gap-2 text-xs font-bold bg-[#FFD02B] text-black px-4 py-1.5 rounded-md shadow-sm border border-amber-300">
                                            {{ $row->status ?? 'On Progress' }}
                                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-center">
                                        <a href="{{ route('laporan.show', $row->id_pengaduan ?? $row->id) }}" 
                                           class="inline-block text-xs font-bold text-[#0090F5] bg-white border border-[#0090F5] hover:bg-sky-50 px-5 py-1 rounded-md transition-all shadow-sm">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-gray-400 font-medium italic">
                                        Belum ada data laporan yang cocok atau tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-xs text-gray-400 font-medium">
                    Menampilkan {{ $daftarLaporan->firstItem() ?? 0 }} sampai {{ $daftarLaporan->lastItem() ?? 0 }} dari {{ $daftarLaporan->total() }} laporan
                </div>
                <div class="font-figma">
                    {{ $daftarLaporan->appends(['search' => $search])->links() }}
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        
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
</script>
@endsection