@extends('layouts.app')

@section('title', 'Dashboard Asisten - SiLapor')

@section('content')
<!-- Import Google Fonts & FontAwesome untuk icon persis seperti Figma -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Custom CSS untuk mendapatkan presisi pixel-perfect sesuai Figma */
    .font-figma {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    /* Efek bayangan halus (soft shadow) persis mockup Figma */
    .shadow-figma-card {
        box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.03);
    }
    .shadow-figma-container {
        box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05);
    }
    
    /* Scrollbar minimalis untuk tabel */
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
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    
    <!-- SIDEBAR KIRI PENUH (Dari Atas ke Bawah - Persis Figma) -->
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:sticky md:top-0 md:h-screen rounded-r-[36px] shadow-sm shrink-0">
        
        <!-- Bagian Atas Sidebar (Logo & Navigasi) -->
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
                <a href="/dashboard" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-solid fa-table-columns text-lg text-[#0090F5]"></i>
                        <span>Dashboard</span>
                    </div>
                    <!-- Indicator bar vertical biru kanan -->
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>

                <!-- Pengaduan (Inaktif di halaman Dashboard) -->
                <a href="{{route('pengaduan.manual.create')}}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-file-lines text-lg"></i>
                    <span>Pengaduan</span>
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
            
            <!-- Form Logout Laravel -->
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
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Dashboard Asisten Lab</h1>
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

        <!-- PROSES PENANGANAN DATA STATISTIK -->
        @php
            $totalPengaduan = $tugas->count();
            $sedangDiperbaiki = $tugas->where('status_penanganan', 'ON PROGRES')->count();
            $selesai = $tugas->where('status_penanganan', 'DONE')->count();
        @endphp

        <!-- TIGA KARTU STATISTIK (Persis Figma) -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total Pengaduan -->
            <div class="bg-white border border-gray-150 rounded-[28px] p-7 flex items-center justify-between shadow-figma-card">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-full bg-[#FFEAEB] border border-[#FFCCD0] flex items-center justify-center shrink-0">
                        <span class="text-2xl font-bold text-[#FF4D4D]">!</span>
                    </div>
                    <div>
                        <p class="text-[#7E8B9B] text-sm font-bold tracking-wide">Total<br>Pengaduan</p>
                        <p class="text-[40px] font-extrabold text-[#2C3E50] leading-none mt-1">
                            {{ str_pad($totalPengaduan, 3, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card 2: Sedang Diperbaiki -->
            <div class="bg-white border border-gray-150 rounded-[28px] p-7 flex items-center justify-between shadow-figma-card">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-full bg-[#EAF5FE] border border-[#CDE5FC] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-screwdriver-wrench text-2xl text-[#0090F5]"></i>
                    </div>
                    <div>
                        <p class="text-[#7E8B9B] text-sm font-bold tracking-wide">Sedang<br>Diperbaiki</p>
                        <p class="text-[40px] font-extrabold text-[#2C3E50] leading-none mt-1">
                            {{ str_pad($sedangDiperbaiki, 3, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card 3: Selesai -->
            <div class="bg-white border border-gray-150 rounded-[28px] p-7 flex items-center justify-between shadow-figma-card">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-full bg-[#E6F9EE] border border-[#C2F1D5] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-check text-2xl text-[#22C55E]"></i>
                    </div>
                    <div>
                        <p class="text-[#7E8B9B] text-sm font-bold tracking-wide">Selesai</p>
                        <p class="text-[40px] font-extrabold text-[#2C3E50] leading-none mt-1">
                            {{ str_pad($selesai, 3, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTAINER TABEL LAPORAN (Persis Figma) -->
        <section class="bg-white border border-gray-150 rounded-[32px] overflow-hidden shadow-figma-container">
            <!-- Header Atas Tabel -->
            <div class="px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-gray-100">
                <h2 class="font-bold text-xl text-gray-800 tracking-tight">Pengaduan Terbaru</h2>
                
                <!-- Tombol Buat Pengaduan Biru (Figma Style) -->
                <a href="/lapor/manual" class="flex items-center gap-2 bg-[#0090F5] hover:bg-[#007cd5] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-md transition-colors">
                    <i class="fa-solid fa-circle-plus text-lg"></i>
                    Buat Pengaduan
                </a>
            </div>

            <!-- Area Tabel Utama -->
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-[#F8FAFC] text-[#64748B] text-xs font-extrabold uppercase tracking-wider border-b border-gray-150">
                            <th class="py-5 px-6 text-center leading-tight">ID<br><span class="text-[10px] font-semibold text-[#94A3B8]">PGD</span></th>
                            <th class="py-5 px-6">Pelapor</th>
                            <th class="py-5 px-6">Lokasi Masalah</th>
                            <th class="py-5 px-6">Fasilitas</th>
                            <th class="py-5 px-6">Tanggal Lapor</th>
                            <th class="py-5 px-6 text-center">Status</th>
                            <th class="py-5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tugas as $t)
                            <!-- Baris Utama Tabel -->
                            <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer" onclick="toggleDetails('row-{{ $t->id_tindak_lanjut }}')">
                                <!-- ID PGD -->
                                <td class="py-5 px-6 text-center text-sm font-semibold text-[#64748B]">
                                    PGD-{{ str_pad($t->id_tindak_lanjut, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <!-- Pelapor -->
                                <td class="py-5 px-6 text-sm text-gray-700 font-medium">
                                    {{ $t->pengaduan->user->name ?? 'Ray' }}
                                </td>
                                <!-- Lokasi Masalah -->
                                <td class="py-5 px-6 text-sm text-gray-500">
                                    {{ $t->pengaduan->fasilitas->laboratorium->nama_laboratorium }}
                                </td>
                                <!-- Nama Fasilitas -->
                                <td class="py-5 px-6 text-sm text-gray-800 font-medium">
                                    {{ $t->pengaduan->fasilitas->nama_fasilitas }}
                                </td>
                                <!-- Tanggal Lapor -->
                                <td class="py-5 px-6 text-sm text-gray-500">
                                    {{ $t->pengaduan->created_at ? $t->pengaduan->created_at->format('d/m/Y') : '13/06/2026' }}
                                </td>
                                <!-- Status Badges -->
                                <td class="py-5 px-6 text-center">
                                    @if ($t->status_penanganan === 'DONE')
                                        <span class="inline-block text-xs font-bold px-5 py-1.5 rounded-md bg-[#4ADE80] text-white min-w-[100px] text-center">
                                            Done
                                        </span>
                                    @else
                                        <span class="inline-block text-xs font-bold px-5 py-1.5 rounded-md bg-[#FBBF24] text-white min-w-[100px] text-center">
                                            On Progress
                                        </span>
                                    @endif
                                </td>
                                <!-- Tombol Aksi Detail -->
                                <td class="py-5 px-6 text-center" onclick="event.stopPropagation()">
                                    <button type="button" onclick="toggleDetails('row-{{ $t->id_tindak_lanjut }}')" class="px-5 py-1 text-xs font-bold text-[#0090F5] bg-white border border-[#CDE5FC] hover:bg-sky-50 hover:border-[#0090F5] transition-all rounded-lg">
                                        Detail
                                    </button>
                                </td>
                            </tr>

                            <!-- Baris Expandable Form Input Penanganan -->
                            <tr id="row-{{ $t->id_tindak_lanjut }}" class="hidden bg-[#F8FAFC]">
                                <td colspan="7" class="px-8 py-6 border-b border-gray-150">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                                        
                                        <!-- Detail Kerusakan -->
                                        <div class="space-y-3">
                                            <h4 class="font-extrabold text-sm text-[#2C3E50] uppercase tracking-wide">Detail Kerusakan</h4>
                                            <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-sm space-y-3">
                                                <p class="text-sm text-gray-600 leading-relaxed">{{ $t->pengaduan->deskripsi_kerusakan }}</p>
                                                @if($t->status_penanganan === 'DONE' && $t->catatan_perbaikan)
                                                    <div class="pt-3 border-t border-gray-100 mt-2">
                                                        <span class="text-xs font-extrabold text-[#22C55E]">Catatan Penyelesaian:</span>
                                                        <p class="text-sm text-gray-500 italic mt-1">"{{ $t->catatan_perbaikan }}"</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Form Input Tindak Lanjut Perbaikan -->
                                        <div>
                                            @if ($t->status_penanganan !== 'DONE')
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
                                            @else
                                                <div class="bg-[#E6F9EE] border border-[#C2F1D5] rounded-2xl p-5 flex items-center gap-3.5 text-[#1E7E44]">
                                                    <i class="fa-solid fa-circle-check text-2xl"></i>
                                                    <span class="text-sm font-bold">Tugas perbaikan ini telah selesai dikerjakan.</span>
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <!-- Tampilan Jika Data Kosong -->
                            <tr>
                                <td colspan="7" class="py-12 text-center">
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
    // Fungsi interaktif untuk toggle detail baris tabel
    function toggleDetails(rowId) {
        const detailsRow = document.getElementById(rowId);
        if (detailsRow) {
            detailsRow.classList.toggle('hidden');
        }
    }

    // Fungsi interaktif buka/tutup sidebar mobile
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