@extends('layouts.app')

@section('title', 'Dashboard Laboran - SiLapor')

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

    @media (min-width: 768px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }

    .nav-press-effect:active { transform: scale(.985); }
    .nav-active-effect { background: #F3F4F6; color: #111827; font-weight: 800; border-radius: 1.35rem; box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .02); }
    .nav-active-effect .nav-active-icon { color: #29ABE2; }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['activeMenu' => 'dashboard'])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop md:hidden">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight">DASHBOARD LABORAN</h1>
            </div>

            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        @php
            $stats = [
                ['Total Laporan', $totalLaporan ?? 0, 'fa-triangle-exclamation', 'text-[#FF4D4D]', 'bg-[#FFEAEB]'],
                ['Total Laboratorium', $totalLaboratorium ?? 0, 'fa-building', 'text-silapor-500', 'bg-[#E8F7FC]'],
                ['Total Fasilitas', $totalFasilitas ?? 0, 'fa-computer', 'text-[#22C55E]', 'bg-[#E6F9EE]'],
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

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 bg-white border rounded-[32px] overflow-hidden shadow-figma-container">
                <div class="px-8 py-6 border-b flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h2 class="font-bold text-xl text-gray-800">Akses Cepat Laboran</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('admin.users.index') }}" class="rounded-3xl border border-gray-200 p-5 hover:border-silapor-500 hover:bg-sky-50/40 transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-[#E8F7FC] text-silapor-500 flex items-center justify-center text-lg mb-4"><i class="fa-solid fa-users"></i></div>
                        <h3 class="font-bold text-[#2C3E50]">Kelola User</h3>
                        <p class="text-sm text-gray-500 mt-1">Tambah akun, ubah data, reset password pengguna.</p>
                    </a>

                    <a href="{{ route('laboratorium.index') }}" class="rounded-3xl border border-gray-200 p-5 hover:border-silapor-500 hover:bg-sky-50/40 transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-[#E6F9EE] text-[#22C55E] flex items-center justify-center text-lg mb-4"><i class="fa-solid fa-building"></i></div>
                        <h3 class="font-bold text-[#2C3E50]">Data Laboratorium</h3>
                        <p class="text-sm text-gray-500 mt-1">Kelola laboratorium, koordinator, dan data ruangan.</p>
                    </a>
                </div>
            </div>

            <div class="bg-white border rounded-[32px] overflow-hidden shadow-figma-container">
                <div class="px-6 py-5 border-b">
                    <h2 class="font-bold text-lg text-gray-800">Ringkasan Sistem</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Pengguna</span>
                        <span class="font-bold text-[#2C3E50]">{{ $totalPengguna ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Laporan Diproses</span>
                        <span class="font-bold text-[#2C3E50]">{{ $proses ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Laporan Done</span>
                        <span class="font-bold text-[#2C3E50]">{{ $selesai ?? 0 }}</span>
                    </div>

                    <div class="pt-4 border-t">
                        <h3 class="font-bold text-sm text-gray-700 mb-3">Laboratorium Terdaftar</h3>
                        <div class="space-y-3 max-h-[240px] overflow-y-auto custom-scrollbar pr-1">
                            @forelse($laboratoriumList as $lab)
                                <div class="rounded-2xl border border-gray-100 px-4 py-3">
                                    <p class="font-semibold text-[#2C3E50] text-sm">{{ $lab->nama_laboratorium }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        PJ: {{ $lab->penanggungJawabUser?->nama ?? 'Belum ditentukan' }}
                                        · {{ $lab->fasilitas_count ?? 0 }} fasilitas
                                    </p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400">Belum ada data laboratorium.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white border rounded-[32px] overflow-hidden shadow-figma-container">
            <div class="px-8 py-6 border-b flex flex-col sm:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-xl text-gray-800">Laporan Terbaru</h2>
                <a href="{{ route('laporan.index') }}" class="bg-silapor-500 hover:bg-silapor-600 transition-colors text-white px-6 py-3 rounded-xl font-bold text-sm w-full sm:w-auto text-center">
                    Lihat Semua Laporan
                </a>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left min-w-[980px]">
                    <thead class="bg-[#F8FAFC] text-gray-500 uppercase text-xs font-extrabold tracking-wider border-b">
                        <tr>
                            <th class="py-5 px-6">ID PGD</th>
                            <th class="py-5 px-6">Pelapor</th>
                            <th class="py-5 px-6">Lokasi Masalah</th>
                            <th class="py-5 px-6">Fasilitas</th>
                            <th class="py-5 px-6">Status</th>
                            <th class="py-5 px-6">PJ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pengaduanList as $item)
                            @php
                                $statusKode = $item->status_pengaduan;
                                $statusStyle = match($statusKode) {
                                    'DONE' => 'bg-[#E6F9EE] text-[#22C55E]',
                                    'HANDLED' => 'bg-[#FFF9E6] text-[#FBBF24]',
                                    default => 'bg-[#E8F7FC] text-silapor-500',
                                };
                                $statusLabel = match($statusKode) {
                                    'DONE' => 'Done',
                                    'HANDLED' => 'On Progress',
                                    default => 'New',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-5 px-6 text-sm font-semibold text-[#64748B]">PGD-{{ str_pad((string) $item->id_pengaduan, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-5 px-6 text-sm text-gray-700 font-medium">{{ $item->pelapor?->nama ?? '-' }}</td>
                                <td class="py-5 px-6 text-sm text-gray-500">{{ $item->fasilitas?->laboratorium?->nama_laboratorium ?? '-' }}</td>
                                <td class="py-5 px-6 text-sm text-gray-800 font-semibold">{{ $item->fasilitas?->kategori?->nama_kategori ?? '-' }} ({{ $item->fasilitas?->no_fasilitas ?? '-' }})</td>
                                <td class="py-5 px-6"><span class="inline-block text-xs font-bold px-4 py-1.5 rounded-md text-center {{ $statusStyle }}">{{ $statusLabel }}</span></td>
                                <td class="py-5 px-6 text-sm text-gray-700">{{ $item->tindakLanjut?->asisten?->nama ?? 'Belum ditugaskan' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 text-center text-gray-400">Belum ada pengaduan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
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
