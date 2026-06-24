@extends('layouts.app')

@section('title', 'Riwayat - SiLapor')

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
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </div>

            <nav class="mt-10 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-table-columns text-lg"></i> <span>Dashboard</span>
                </a>
                <a href="{{ route('pengaduan.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-file-lines text-lg"></i> <span>Pengaduan</span>
                </a>
                <a href="{{ route('tindak-lanjut.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i> <span>Tindak Lanjut</span>
                </a>
                <a href="{{ route('riwayat.index') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-solid fa-clock-rotate-left text-lg text-[#0090F5]"></i> <span>Riwayat</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>
                <a href="{{ route('teknisi.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i> <span>Teknisi</span>
                </a>
                <a href="{{ route('profile.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-user text-lg"></i> <span>Profil</span>
                </a>
            </nav>
        </div>
        <!-- Logout Section -->
        <div class="p-8">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
                <i class="fa-solid fa-right-from-bracket text-lg"></i> <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- MAIN CONTENT -->
    <main class="flex-1 px-4 py-6 md:px-10 md:py-8">
        <header class="flex items-center justify-between pb-8 border-b border-gray-100/50 mb-6">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-600">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h1 class="text-2xl font-extrabold text-[#2C3E50] uppercase tracking-wider">RIWAYAT</h1>
            </div>
        </header>

        <section class="bg-white rounded-[32px] shadow-figma-container border border-gray-150 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-[#F8FAFC] text-gray-700">
                        <tr>
                            <th class="px-8 py-5 font-bold text-sm">ID</th>
                            <th class="px-6 py-5 font-bold text-sm">Lokasi Masalah</th>
                            <th class="px-6 py-5 font-bold text-sm">Tanggal Lapor</th>
                            <th class="px-6 py-5 font-bold text-sm">Tanggal Selesai</th>
                            <th class="px-6 py-5 font-bold text-sm text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($riwayat as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-8 py-5 text-sm text-gray-600 font-bold">{{ $item->id }}</td>
                            <td class="px-6 py-5 text-sm text-gray-600">{{ $item->fasilitas->nama_fasilitas ?? 'N/A' }}</td>
                            <td class="px-6 py-5 text-sm text-gray-600">{{ $item->created_at->format('d-m-Y') }}</td>
                            <td class="px-6 py-5 text-sm text-gray-600">{{ $item->updated_at->format('d-m-Y') }}</td>
                            <td class="px-6 py-5 text-center">
                                <a href="#" class="px-6 py-2 border border-[#0090F5] text-[#0090F5] text-xs font-bold rounded-xl hover:bg-[#0090F5] hover:text-white transition-all">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-gray-400">Belum ada riwayat pengaduan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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