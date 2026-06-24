@extends('layouts.app')

@section('title', 'Dashboard Kepala Lab - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Custom CSS untuk mendapatkan presisi pixel-perfect sesuai Figma temanmu */
    .font-figma {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    /* Efek bayangan halus (soft shadow) persis mockup Figma */
    .shadow-figma-card {
        box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.02);
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
    
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:sticky md:top-0 md:h-screen rounded-r-[36px] shadow-sm shrink-0">
        
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </div>

            <nav class="mt-10 space-y-2">
                <a href="#" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-solid fa-table-columns text-lg text-[#0090F5]"></i>
                        <span>Dashboard</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>

                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-file-lines text-lg"></i>
                    <span>Laporan</span>
                </a>

                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Riwayat</span>
                </a>

                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                    <span>Rekapulasi</span>
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
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Dasboard</h1>
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

        @php
            $total = isset($laporan) ? $laporan->count() : 128;
            $selesai = isset($laporan) ? $laporan->where('status', 'DONE')->count() : 96;
            $proses = isset($laporan) ? $laporan->where('status', 'ON PROGRES')->count() : 24;
            $tertunda = isset($laporan) ? $laporan->where('status', 'PENDING')->count() : 8;
            
            // Dummy data array untuk menyamakan visual tabel figma sebelum data DB asli masuk
            $dummyLaporan = array_fill(0, 8, [
                'lokasi' => 'Lab. Computer Network',
                'tanggal' => '21-07-2026',
                'fasilitas' => 'Monitor'
            ]);
            
            $daftarLaporan = isset($laporanTerbaru) ? $laporanTerbaru : $dummyLaporan;
        @endphp

        <div class="bg-white border border-gray-150 rounded-[36px] p-6 md:p-8 shadow-figma-container space-y-8">
            
            <section class="space-y-4">
                <h2 class="text-lg font-bold text-[#2C3E50] tracking-tight">Ringkasan Laporan</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <div class="bg-white border border-gray-200 rounded-[20px] p-4 flex items-center gap-4 shadow-figma-card">
                        <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center text-[#0090F5] shrink-0">
                            <i class="fa-regular fa-file-lines text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400">Total Laporan</p>
                            <p class="text-2xl font-extrabold text-[#2C3E50] leading-tight">{{ $total }}</p>
                            <p class="text-[10px] text-gray-400 font-medium">Semua laporan</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-[20px] p-4 flex items-center gap-4 shadow-figma-card">
                        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-flex text-[#22C55E] shrink-0">
                            <i class="fa-regular fa-square-check text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400">Selesai</p>
                            <p class="text-2xl font-extrabold text-[#2C3E50] leading-tight">{{ $selesai }}</p>
                            <p class="text-[10px] text-gray-400 font-medium">Laporan selesai</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-[20px] p-4 flex items-center gap-4 shadow-figma-card">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-[#F59E0B] shrink-0">
                            <i class="fa-regular fa-clock text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400">Proses</p>
                            <p class="text-2xl font-extrabold text-[#2C3E50] leading-tight">{{ $proses }}</p>
                            <p class="text-[10px] text-gray-400 font-medium">Sedang diproses</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-[20px] p-4 flex items-center gap-4 shadow-figma-card">
                        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-[#EF4444] shrink-0">
                            <i class="fa-regular fa-circle-xmark text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400">Tertunda</p>
                            <p class="text-2xl font-extrabold text-[#2C3E50] leading-tight">{{ $tertunda }}</p>
                            <p class="text-[10px] text-gray-400 font-medium">Perlu diproses</p>
                        </div>
                    </div>

                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-[#2C3E50] tracking-tight">Daftar Laporan Terbaru</h2>
                
                <div class="border border-gray-300 rounded-[18px] overflow-hidden bg-white">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[700px]">
                            <thead>
                                <tr class="bg-[#034C5F] text-white text-sm font-semibold tracking-wide">
                                    <th class="py-3 px-6 w-[45%]">Lokasi Masalah</th>
                                    <th class="py-3 px-6 w-[20%]">Tanggal</th>
                                    <th class="py-3 px-6 w-[20%]">Fasilitas</th>
                                    <th class="py-3 px-6 w-[15%] text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($daftarLaporan as $row)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3.5 px-6 text-sm text-gray-700 font-medium flex items-center gap-3">
                                            <i class="fa-solid fa-location-dot text-[#EF4444]"></i>
                                            <span>{{ is_array($row) ? $row['lokasi'] : $row->pengaduan->fasilitas->laboratorium->nama_laboratorium }}</span>
                                        </td>
                                        <td class="py-3.5 px-6 text-sm text-gray-500">
                                            {{ is_array($row) ? $row['tanggal'] : $row->created_at->format('d-m-Y') }}
                                        </td>
                                        <td class="py-3.5 px-6 text-sm text-gray-600">
                                            {{ is_array($row) ? $row['fasilitas'] : $row->pengaduan->fasilitas->nama_fasilitas }}
                                        </td>
                                        <td class="py-3.5 px-6 text-center">
                                            <a href="#" class="inline-block text-xs font-bold text-[#0090F5] bg-white border border-[#0090F5] hover:bg-sky-50 px-5 py-1 rounded-md transition-all shadow-sm">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="text-xs text-gray-400 font-medium pl-2">
                    Menampilkan 1 - {{ count($daftarLaporan) }} dari {{ $total }} laporan
                </div>
            </section>

        </div>
    </main>
</div>

<script>
    // Fungsi interaktif buka/tutup sidebar mobile (Konsisten dengan fungsi asisten)
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