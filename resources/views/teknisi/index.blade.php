@extends('layouts.app')

@section('title', 'Daftar Teknisi - SiLapor')

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
    
    @include('partials.sidebar', ['user' => auth()->user(), 'activeMenu' => 'teknisi'])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-600">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h1 class="text-2xl font-extrabold text-[#2C3E50] uppercase tracking-wider">Daftar Teknisi</h1>
            </div>
            @include('partials.user-welcome-box', ['user' => $user ?? auth()->user()])
        </header>

        <section class="bg-white border border-gray-150 rounded-[32px] overflow-hidden shadow-figma-container p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[#64748B] text-xs font-extrabold uppercase tracking-wider border-b border-gray-150">
                        <th class="py-5 px-6">ID Teknisi</th>
                        <th class="py-5 px-6">Nama Teknisi</th>
                        <th class="py-5 px-6">Keahlian</th>
                        <th class="py-5 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($teknisi as $t)
                    <tr class="hover:bg-slate-50">
                        <td class="py-5 px-6 font-medium text-gray-500">TKN-{{ str_pad($t->id_teknisi, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-5 px-6 font-semibold text-gray-800">{{ $t->nama_teknisi }}</td>
                        <td class="py-5 px-6 text-gray-600">{{ $t->keahlian }}</td>
                        <td class="py-5 px-6"><span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Tersedia</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-10 text-center text-gray-400">Data teknisi belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
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
