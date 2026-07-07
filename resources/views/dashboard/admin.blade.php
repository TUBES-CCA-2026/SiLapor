@extends('layouts.app')

@section('title', 'Dashboard Admin - SiLapor')

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

    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }

    .nav-press-effect:active { transform: scale(.985); }
    .nav-active-effect { background: #F3F4F6; color: #111827; font-weight: 800; border-radius: 1.35rem; box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .02); }
    .nav-active-effect .nav-active-icon { color: #0090F5; }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    {{-- Sidebar --}}
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </div>

            @php
                $menuItems = \App\Support\SidebarMenu::forRole('admin');
                $activeMenu = 'dashboard';
            @endphp
            <nav class="mt-10 space-y-7">
                @foreach($menuItems as [$key, $label, $icon, $url])
                    @if($activeMenu === $key)
                        <a href="{{ $url }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                            <div class="flex items-center gap-3.5">
                                <i class="{{ $icon }} text-lg text-[#0090F5]"></i>
                                <span>{{ $label }}</span>
                            </div>
                            <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                        </a>
                    @else
                        <a href="{{ $url }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                            <i class="{{ $icon }} text-lg"></i>
                            <span>{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px] md:rounded-br-none">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden" onclick="toggleSidebar()"></div>

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight">DASHBOARD ADMIN</h1>
            </div>

            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        @php
            $stats = [
                ['Total Pengguna', $totalPengguna ?? 0, 'fa-users', 'text-[#0090F5]', 'bg-[#EAF5FE]'],
                ['Total Fasilitas', $totalFasilitas ?? 0, 'fa-computer', 'text-[#22C55E]', 'bg-[#E6F9EE]'],
                ['Total Laboratorium', $totalLaboratorium ?? 0, 'fa-building', 'text-[#F59E0B]', 'bg-[#FFF9E6]'],
            ];
        @endphp

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
            @foreach($stats as $s)
                <div class="bg-white border rounded-3xl p-5 md:p-6 flex items-center shadow-figma-card">
                    <div class="w-14 h-14 rounded-full {{ $s[4] }} flex items-center justify-center {{ $s[3] }} text-xl shrink-0">
                        <i class="fa-solid {{ $s[2] }}"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500 font-bold">{{ $s[0] }}</p>
                        <p class="text-2xl md:text-3xl font-extrabold text-[#2C3E50]">{{ ((int) $s[1]) > 0 ? (string) ((int) $s[1]) : '-' }}</p>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="grid grid-cols-1 gap-6">
            <div class="bg-white border rounded-[32px] overflow-hidden shadow-figma-container">
                <div class="px-8 py-6 border-b flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h2 class="font-bold text-xl text-gray-800">Akses Cepat Admin</h2>
                    <span class="text-sm text-gray-400">Pilih menu di bawah untuk mengelola sistem</span>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('admin.users.index') }}" class="rounded-3xl border border-gray-200 p-6 hover:border-[#0090F5] hover:bg-sky-50/40 transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-[#EAF5FE] text-[#0090F5] flex items-center justify-center text-2xl mb-4"><i class="fa-solid fa-users-gear"></i></div>
                            <h3 class="font-bold text-lg text-[#2C3E50]">Kelola User</h3>
                            <p class="text-sm text-gray-500 mt-2">Tambah akun, ubah profil, reset password, dan import data pengguna.</p>
                        </div>
                        <span class="text-xs font-bold text-[#0090F5] mt-6 inline-flex items-center gap-1">Kelola Akun <i class="fa-solid fa-arrow-right"></i></span>
                    </a>

                    <a href="{{ route('fasilitas.index') }}" class="rounded-3xl border border-gray-200 p-6 hover:border-[#0090F5] hover:bg-sky-50/40 transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-[#E6F9EE] text-[#22C55E] flex items-center justify-center text-2xl mb-4"><i class="fa-solid fa-qrcode"></i></div>
                            <h3 class="font-bold text-lg text-[#2C3E50]">Fasilitas & Pembuatan QR</h3>
                            <p class="text-sm text-gray-500 mt-2">Kelola daftar fasilitas laboratorium komputer dan cetak atau regenerasi QR Code.</p>
                        </div>
                        <span class="text-xs font-bold text-[#22C55E] mt-6 inline-flex items-center gap-1">Kelola QR <i class="fa-solid fa-arrow-right"></i></span>
                    </a>
                </div>
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
</script>
@endsection
