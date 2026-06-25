@extends('layouts.app')

@section('title', 'Rekapsulasi - SiLapor')

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
    
    {{-- Sidebar --}}
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
                <a href="{{ route('laporan.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                    <span>Laporan</span>
                </a>
                <a href="{{ route('riwayat.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('rekapsulasi.index') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-solid fa-file-invoice text-lg text-[#0090F5]"></i>
                        <span>Rekapsulasi</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>
                <a href="{{ route('profil.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-user text-lg"></i>
                    <span>Profil</span>
                </a>
            </nav>
        </div>
        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px]">
            <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <main class="flex-1 px-6 py-6 md:px-10 md:py-8 space-y-6 overflow-x-hidden">
        
        <header class="flex items-center justify-between pb-2">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none md:hidden">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase font-figma">REKAPSULASI</h1>
            </div>
            
            {{-- Profil Dinamis (Konsisten) --}}
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
            
            {{-- Filter Section --}}
            <section class="bg-gray-50 p-6 rounded-[24px] border border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Filter Laporan</h3>
                <form action="{{ route('rekapsulasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <input type="text" name="tanggal" placeholder="Tanggal" class="p-3 rounded-xl border border-gray-300 text-sm">
                    <select name="penanggung_jawab" class="p-3 rounded-xl border border-gray-300 text-sm text-gray-500">
                        <option value="">Semua Penanggung Jawab</option>
                    </select>
                    <select name="lokasi" class="p-3 rounded-xl border border-gray-300 text-sm text-gray-500">
                        <option value="">Semua Lokasi</option>
                    </select>
                    <input type="text" name="search" placeholder="Cari Laporan..." class="p-3 rounded-xl border border-gray-300 text-sm">
                    <select name="urutan" class="p-3 rounded-xl border border-gray-300 text-sm text-gray-500">
                        <option value="desc">Terbaru</option>
                        <option value="asc">Terlama</option>
                    </select>
                    <select name="status" class="p-3 rounded-xl border border-gray-300 text-sm text-gray-500">
                        <option value="">Semua Status</option>
                        <option value="On Progress">On Progress</option>
                        <option value="Done">Done</option>
                    </select>
                    <select name="fasilitas" class="p-3 rounded-xl border border-gray-300 text-sm text-gray-500">
                        <option value="">Semua Fasilitas</option>
                    </select>
                    <a href="{{ route('rekapsulasi.index') }}" class="flex items-center justify-center gap-2 p-3 rounded-xl bg-white border border-gray-300 text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                </form>
            </section>

            {{-- Table Section --}}
            <section>
                <h2 class="text-lg font-bold text-[#2C3E50] mb-4">Daftar Laporan</h2>
                <div class="border border-gray-300 rounded-[18px] overflow-hidden bg-white">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[900px]">
                            <thead>
                                <tr class="bg-[#034C5F] text-white text-sm font-semibold tracking-wide">
                                    <th class="py-3.5 px-6">Tanggal</th>
                                    <th class="py-3.5 px-6">Penanggung Jawab</th>
                                    <th class="py-3.5 px-6">Lokasi Masalah</th>
                                    <th class="py-3.5 px-6">Fasilitas</th>
                                    <th class="py-3.5 px-6 text-center">Status</th>
                                    <th class="py-3.5 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($daftarLaporan as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3.5 px-6 text-sm text-gray-600">{{ $item->created_at->format('d-m-Y') }}</td>
                                    <td class="py-3.5 px-6 text-sm text-gray-700 font-medium">{{ $item->user->nama ?? 'N/A' }}</td>
                                    <td class="py-3.5 px-6 text-sm text-gray-700">{{ $item->lokasi_masalah ?? '-' }}</td>
                                    <td class="py-3.5 px-6 text-sm text-gray-600">{{ $item->fasilitas ?? '-' }}</td>
                                    <td class="py-3.5 px-6 text-center">
                                        @if($item->status == 'Done')
                                            <span class="inline-block px-3 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700 border border-green-200">Done</span>
                                        @else
                                            <span class="inline-block px-3 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">On Progress</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6 text-center">
                                        <button class="text-xs font-bold text-[#0090F5] bg-white border border-[#0090F5] px-5 py-1 rounded-md hover:bg-sky-50">Detail</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-gray-400">Belum ada data laporan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
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