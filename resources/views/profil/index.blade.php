@extends('layouts.app')

@section('title', 'Profil - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    
    {{-- Sidebar (Konsisten dengan halaman Laporan) --}}
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

                <a href="{{ route('rekapsulasi.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                    <span>Rekapsulasi</span>
                </a>

                {{-- Menu Profil (Aktif) --}}
                <a href="{{ route('profil.index') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-regular fa-user text-lg text-[#0090F5]"></i>
                        <span>Profil</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>
            </nav>
        </div>

        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px]">
            <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    {{-- Main Content --}}
    <main class="flex-1 px-6 py-6 md:px-10 md:py-8 space-y-6 overflow-x-hidden">
        
        <header class="flex items-center justify-between pb-2">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 md:hidden">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
            <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase font-figma">PROFIL</h1>
            
            <a href="#" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-50 shadow-sm transition-all">
                Edit Profil <i class="fa-solid fa-pen ml-2"></i>
            </a>
        </header>

        {{-- Main Card --}}
        <div class="bg-white border border-gray-150 rounded-[36px] p-8 shadow-figma-container">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Avatar Section --}}
                <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-[28px] border border-gray-100">
                    <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center text-gray-400 mb-4 border-2 border-gray-100 shadow-inner">
                        <i class="fa-solid fa-user text-5xl"></i>
                    </div>
                    <span class="px-4 py-1.5 bg-[#0090F5] text-white text-xs font-bold rounded-full">Kepala Lab</span>
                </div>

                {{-- Form Section --}}
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Kepala LAB</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-medium">
                            {{ Auth::user()->name ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ID Kepala LAB</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-medium">
                            {{ Auth::user()->id ?? '-' }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-medium">
                            {{ Auth::user()->email ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">No. HP</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-medium">
                            {{ Auth::user()->no_hp ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Role</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-medium">
                            {{ Auth::user()->role ?? 'Kepala Lab' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action --}}
            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                <button class="px-6 py-3 bg-[#034C5F] text-white font-bold rounded-2xl hover:bg-[#023a48] transition-all shadow-lg">
                    Ubah Password <i class="fa-solid fa-gear ml-2"></i>
                </button>
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