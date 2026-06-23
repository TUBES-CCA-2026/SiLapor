@extends('layouts.app')

@section('title', 'Tindak Lanjut - SiLapor')

@section('content')
<!-- Import Google Fonts & FontAwesome untuk icon persis figma -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Custom font sesuai Figma mockup */
    .font-figma {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    /* Efek bayangan premium figma */
    .shadow-figma-card {
        box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.02);
    }
    .shadow-figma-container {
        box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.04);
    }
    
    /* Scrollbar halus untuk tabel responsif */
    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #F1F5F9;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
    }

    /* Penyesuaian layout responsive di breakpoint khusus 850px */
    @media (min-width: 850px) {
        .sidebar-desktop {
            transform: translateX(0) !important;
            position: sticky !important;
            top: 0;
            height: 100vh;
        }
        .hide-on-desktop {
            display: none !important;
        }
    }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    
    <!-- SIDEBAR KIRI (Persis seperti mockup TINDAK LANJUT.png) -->
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        
        <!-- Bagian Atas Sidebar -->
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
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-table-columns text-lg"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Pengaduan -->
                <a href="{{ route('pengaduan.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-file-lines text-lg"></i>
                    <span>Pengaduan</span>
                </a>

                <!-- Tindak Lanjut (ACTIVE - Berwarna abu-abu tipis dengan bar biru di kanan) -->
                <a href="#" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-solid fa-screwdriver-wrench text-lg text-[#0090F5]"></i>
                        <span>Tindak Lanjut</span>
                    </div>
                    <!-- Indicator bar vertical biru kanan -->
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
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

        <!-- Bagian Bawah Sidebar (Logout) -->
        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px] md:rounded-br-none">
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

    <!-- Overlay Background saat Sidebar Mobile Terbuka -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden" onclick="toggleSidebar()"></div>

    <!-- KONTEN UTAMA (Kanan) -->
    <main class="flex-1 px-4 py-6 md:px-10 md:py-8 space-y-6 overflow-x-hidden w-full min-w-0">
        
        <!-- HEADER NAVBAR (Sesuai mockup) -->
        <header class="flex flex-row items-center justify-between gap-4 pb-4 border-b border-gray-100/50">
            <div class="flex items-center gap-4">
                <!-- Hamburger menu yang hanya muncul di layar lebar < 850px -->
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] uppercase tracking-wider">Tindak Lanjut</h1>
            </div>

            <!-- Profil Widget Kanan (Persis TINDAK LANJUT.png) -->
            <div class="bg-[#0090F5] text-white px-5 py-2.5 rounded-2xl flex items-center gap-3.5 shadow-md justify-center">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0090F5] shrink-0">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div class="text-left flex flex-col justify-center leading-tight min-w-[100px]">
                    <span class="text-sm font-extrabold block tracking-wide">
                        {{ Auth::user()->name ?? 'Nurul' }}
                    </span>
                    <span class="text-[11px] font-light opacity-90 block">Asisten Lab</span>
                </div>
            </div>
        </header>

        <!-- CONTAINER TABEL UTAMA TINDAK LANJUT -->
        <section class="bg-white border border-gray-150 rounded-[32px] overflow-hidden shadow-figma-container">
            
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[1100px]">
                    <thead>
                        <tr class="bg-[#F8FAFC] text-[#64748B] text-xs font-extrabold uppercase tracking-wider border-b border-gray-150">
                            <th class="py-5 px-6 text-center leading-tight">ID</th>
                            <th class="py-5 px-6">ID PGD</th>
                            <th class="py-5 px-6">Pelapor</th>
                            <th class="py-5 px-6">PJ</th>
                            <th class="py-5 px-6">Lokasi Masalah</th>
                            <th class="py-5 px-6">Fasilitas</th>
                            <th class="py-5 px-6 text-center">Status</th>
                            <th class="py-5 px-6">Tanggal Lapor</th>
                            <th class="py-5 px-6">Tanggal Selesai</th>
                            <th class="py-5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($tugas as $t)
                            <!-- Baris Utama Tabel -->
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- ID TDL -->
                                <td class="py-5 px-6 text-center font-medium text-gray-500">
                                    TDL-{{ str_pad($t->id_tindak_lanjut, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <!-- ID PGD -->
                                <td class="py-5 px-6 font-semibold text-[#0090F5]">
                                    PGD-{{ str_pad($t->id_pengaduan, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <!-- Pelapor -->
                                <td class="py-5 px-6 text-gray-700 font-medium">
                                    {{ $t->pengaduan?->user?->name ?? 'Ray' }}
                                </td>
                                <!-- PJ (Asisten yang ditugaskan) -->
                                <td class="py-5 px-6 text-gray-700 font-semibold">
                                    {{ $t->asisten?->name ?? 'Aan' }}
                                </td>
                                <!-- Lokasi Masalah -->
                                <td class="py-5 px-6 text-gray-600">
                                    {{ $t->pengaduan?->fasilitas?->laboratorium?->nama_laboratorium ?? 'Lab. Computer Network' }}
                                </td>
                                <!-- Fasilitas -->
                                <td class="py-5 px-6 text-gray-800 font-medium">
                                    {{ $t->pengaduan?->fasilitas?->nama_fasilitas ?? 'Komputer 01' }}
                                </td>
                                <!-- Status Badge dengan Dropdown Interaktif -->
                                <td class="py-5 px-6 text-center" onclick="event.stopPropagation()">
                                    <form method="POST" action="{{ route('tindak-lanjut.update', $t->id_tindak_lanjut) }}" id="form-status-{{ $t->id_tindak_lanjut }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status_penanganan" onchange="document.getElementById('form-status-{{ $t->id_tindak_lanjut }}').submit()" 
                                            class="inline-block text-xs font-bold px-3 py-1.5 rounded-md text-center appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#0090F5]/30
                                            {{ $t->status_penanganan === 'DONE' ? 'bg-[#4ADE80] text-white' : 'bg-[#FBBF24] text-white' }}">
                                            <option value="ON PROGRES" {{ $t->status_penanganan === 'ON PROGRES' ? 'selected' : '' }}>On Progress ▼</option>
                                            <option value="DONE" {{ $t->status_penanganan === 'DONE' ? 'selected' : '' }}>Done ▼</option>
                                        </select>
                                    </form>
                                </td>
                                <!-- Tanggal Lapor -->
                                <td class="py-5 px-6 text-gray-500">
                                    {{ $t->pengaduan?->created_at ? $t->pengaduan->created_at->format('d/m/Y') : '13/06/2026' }}
                                </td>
                                <!-- Tanggal Selesai -->
                                <td class="py-5 px-6 text-gray-500">
                                    @if($t->status_penanganan === 'DONE')
                                        {{ $t->updated_at ? $t->updated_at->format('d-m-Y') : '18-10-2026' }}
                                    @else
                                        <span class="text-gray-300 italic">mm/dd/yyyy</span>
                                    @endif
                                </td>
                                <!-- Tombol Aksi Detail -->
                                <td class="py-5 px-6 text-center">
                                    <button type="button" onclick="toggleDetails('detail-row-{{ $t->id_tindak_lanjut }}')" class="px-4 py-1 text-xs font-bold text-[#0090F5] bg-white border border-[#CDE5FC] hover:bg-sky-50 hover:border-[#0090F5] transition-all rounded-lg">
                                        Detail
                                    </button>
                                </td>
                            </tr>

                            <!-- Baris Expandable Detail Penanganan (Sesuai Desain Flexible/Responsive) -->
                            <tr id="detail-row-{{ $t->id_tindak_lanjut }}" class="hidden bg-[#F8FAFC]">
                                <td colspan="10" class="px-8 py-6 border-b border-gray-150">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                                        <!-- Detail Kerusakan -->
                                        <div class="space-y-3">
                                            <h4 class="font-extrabold text-sm text-[#2C3E50] uppercase tracking-wide">Detail Kerusakan</h4>
                                            <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-sm space-y-3">
                                                <p class="text-sm text-gray-600 leading-relaxed">
                                                    {{ $t->pengaduan?->deskripsi_kerusakan ?? 'Kerusakan pada unit hardware atau software.' }}
                                                </p>
                                                @if($t->pengaduan?->foto_kerusakan)
                                                    <div class="mt-2">
                                                        <span class="text-xs font-extrabold text-[#0090F5]">Foto Bukti:</span>
                                                        <img src="{{ asset('storage/' . $t->pengaduan->foto_kerusakan) }}" alt="Foto Kerusakan" class="mt-1 rounded-xl max-h-40 object-cover shadow-sm">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Update Catatan & Progres oleh Asisten -->
                                        <div>
                                            <h4 class="font-extrabold text-sm text-[#0090F5] uppercase tracking-wide mb-3">Tindak Lanjut Perbaikan</h4>
                                            <form method="POST" action="{{ route('tindak-lanjut.update', $t->id_tindak_lanjut) }}" class="space-y-4">
                                                @csrf
                                                @method('PATCH')
                                                
                                                <textarea name="catatan_perbaikan" rows="3" placeholder="Tuliskan tindakan/perkembangan perbaikan disini..." required
                                                          class="w-full text-sm rounded-xl border border-gray-200 p-4 focus:outline-none focus:ring-2 focus:ring-[#0090F5]/30 focus:border-[#0090F5] transition-all bg-white shadow-sm"
                                                >{{ $t->catatan_perbaikan }}</textarea>

                                                <div class="flex gap-3 justify-end">
                                                    <button type="submit" name="status_penanganan" value="ON PROGRES"
                                                            class="text-xs font-bold px-5 py-2.5 rounded-full bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors">
                                                        Simpan Progres
                                                    </button>
                                                    <button type="submit" name="status_penanganan" value="DONE"
                                                            class="text-xs font-bold px-5 py-2.5 rounded-full bg-[#0090F5] text-white hover:bg-[#007cd5] shadow-sm transition-colors">
                                                        Tandai Selesai
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <!-- Empty State jika tidak ada tugas tindak lanjut -->
                            <tr>
                                <td colspan="10" class="py-12 text-center">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <i class="fa-solid fa-clipboard-list text-5xl text-gray-300"></i>
                                        <p class="text-gray-400 text-sm font-semibold">Belum ada tugas perbaikan untuk Anda saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
    // Fungsi responsif toggle sidebar untuk mobile/tablet di bawah 850px
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

    // Toggle baris detail expandable
    function toggleDetails(rowId) {
        const detailsRow = document.getElementById(rowId);
        if (detailsRow) {
            detailsRow.classList.toggle('hidden');
        }
    }

    // Listeners untuk perubahan viewport width
    window.addEventListener('resize', handleResponsiveSidebar);
    window.addEventListener('load', handleResponsiveSidebar);
</script>
@endsection