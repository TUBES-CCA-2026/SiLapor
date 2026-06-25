@extends('layouts.app')

@section('title', 'Riwayat - SiLapor')

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

                <a href="{{ route('laporan.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                    <span>Laporan</span>
                </a>

                <a href="{{ route('riwayat.index') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-solid fa-clock-rotate-left text-lg text-[#0090F5]"></i>
                        <span>Riwayat</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>

                <a href="{{ route('rekapsulasi.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                    <span>Rekapsulasi</span>
                </a>

                <a href="{{ route('profil.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
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
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase font-figma">RIWAYAT</h1>
            </div>

            {{-- BADGE USER DINAMIS - OPSI 3 (NAMA | ROLE) --}}
            <div class="bg-[#0090F5] text-white px-5 py-2.5 rounded-2xl flex items-center gap-4 shadow-lg border border-white/5 transition-all hover:shadow-xl">
                {{-- Foto Profil (Header tetap Bulat agar kontras dengan Foto Profil di halaman utama) --}}
                <img src="{{ auth()->user()->foto ? asset('storage/' . auth()->user()->foto) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->nama).'&background=FFFFFF&color=0090F5' }}" 
                    alt="Profil" 
                    class="w-10 h-10 rounded-full object-cover border-2 border-white/30 shadow-sm shrink-0">
                
                <div class="text-left flex flex-col justify-center">
                    <span class="text-[11px] font-medium opacity-70 block tracking-tight">Selamat datang,</span>
                    
                    <div class="flex items-center gap-3 mt-0.5">
                        {{-- Nama Profil --}}
                        <span class="text-sm font-extrabold block tracking-wide truncate max-w-[150px]">
                            {{ auth()->user()->nama }}
                        </span>

                        {{-- Garis Pemisah Vertikal | --}}
                        <div class="h-3.5 w-[1px] bg-white/30 rounded-full"></div>

                        {{-- Role Profil --}}
                        <span class="text-[10px] font-bold opacity-80 block tracking-widest uppercase">
                            {{ auth()->user()->role ?? 'KEPALA LAB' }}
                        </span>
                    </div>
                </div>
            </div>
        </header>

        {{-- CONTAINER UTAMA DIUBAH: Hapus padding internal, tambahkan overflow-hidden --}}
        <div class="bg-white border border-gray-100 rounded-[36px] shadow-figma-container overflow-hidden">
            
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-[#E5E7EB] text-[#556980] text-sm font-bold tracking-wide">
                            <th class="py-5 px-8">ID</th>
                            <th class="py-5 px-6">PJ</th>
                            <th class="py-5 px-6">Lokasi Masalah</th>
                            <th class="py-5 px-6">Fasilitas</th>
                            <th class="py-5 px-8">Tanggal Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($riwayats as $riwayat)
                            <tr class="bg-white hover:bg-gray-50/60 transition-colors">
                                <td class="py-5 px-8 text-sm text-gray-500 font-medium">
                                    TDL-{{ sprintf('%03d', $riwayat->id_tindak_lanjut ?? ($riwayat->id ?? 1)) }}
                                </td>
                                <td class="py-5 px-6 text-sm text-gray-700 font-semibold">
                                    {{ $riwayat->user->nama ?? ($riwayat->user->name ?? ($riwayat->asisten->nama ?? 'User')) }}
                                </td>
                                <td class="py-5 px-6 text-sm text-gray-600 font-medium">
                                    {{ $riwayat->pengaduan->fasilitas->laboratorium->nama_laboratorium ?? ($riwayat->fasilitas_lab->laboratorium->nama_laboratorium ?? 'Lab Tidak Diketahui') }}
                                </td>
                                <td class="py-5 px-6 text-sm text-gray-500">
                                    {{ $riwayat->pengaduan->fasilitas->nama_fasilitas ?? ($riwayat->fasilitas_lab->nama_fasilitas ?? '-') }}
                                </td>
                                <td class="py-5 px-8 text-sm text-gray-500 font-medium">
                                    <div class="flex items-center justify-between gap-4">
                                        <span>{{ $riwayat->tanggal_penanganan ? \Carbon\Carbon::parse($riwayat->tanggal_penanganan)->format('d-m-Y') : ($riwayat->updated_at ? $riwayat->updated_at->format('d-m-Y') : '-') }}</span>
                                        
                                        <a href="{{ route('riwayat.show', $riwayat->id_tindak_lanjut ?? ($riwayat->id_pengaduan ?? $riwayat->id)) }}" 
                                           class="text-xs font-bold text-[#0090F5] hover:underline whitespace-nowrap">
                                            Detail &rarr;
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-gray-400 font-medium italic">
                                    Belum ada data riwayat yang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION PINDAH KE LUAR CONTAINER PUTIH (Menyesuaikan Gambar) --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-2">
            <div class="text-xs text-gray-400 font-medium">
                Menampilkan {{ $riwayats->firstItem() ?? 0 }} sampai {{ $riwayats->lastItem() ?? 0 }} dari {{ $riwayats->total() }} riwayat
            </div>
            <div class="font-figma">
                {{ $riwayats->links() }}
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