@extends('layouts.app')

@section('title', 'Dashboard Asisten - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'dashboard';
    $pageTitle = 'DASHBOARD ASISTEN';

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

@endphp

@once
<style>
    .font-figma {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .shadow-figma-card {
        box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.03);
    }

    .shadow-figma-container {
        box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05);
    }

    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #F1F5F9;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
    }

    @media (min-width: 850px) {
        .sidebar-desktop {
            transform: translateX(0) !important;
        }

        .hide-on-desktop {
            display: none !important;
        }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['user' => $user, 'activeMenu' => 'dashboard'])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight uppercase">
                    DASHBOARD ASISTEN
                </h1>
            </div>

            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @php
                $formatStatistik = function ($value) {
                    $value = (int) ($value ?? 0);

                    return $value > 0 ? (string) $value : '-';
                };

                $stats = [
                    ['Total Pengaduan', $formatStatistik($totalPengaduan ?? null), 'fa-triangle-exclamation', 'text-[#FF4D4D]', 'bg-[#FFEAEB]'],
                    ['Sedang Diperbaiki', $formatStatistik($sedangDiperbaiki ?? null), 'fa-screwdriver-wrench', 'text-[#0090F5]', 'bg-[#EAF5FE]'],
                    ['Done', $formatStatistik($selesai ?? null), 'fa-circle-check', 'text-[#22C55E]', 'bg-[#E6F9EE]'],
                ];
            @endphp

            @foreach($stats as [$label, $value, $icon, $textColor, $bgColor])
                <div class="bg-white border border-gray-100 rounded-3xl p-5 md:p-6 flex items-center shadow-figma-card">
                    <div class="w-14 h-14 rounded-full {{ $bgColor }} flex items-center justify-center {{ $textColor }} text-xl shrink-0">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>

                    <div class="ml-4">
                        <p class="text-xs text-gray-500 font-bold">
                            {{ $label }}
                        </p>
                        <p class="text-2xl md:text-3xl font-extrabold text-[#2C3E50]">
                            {{ $value }}
                        </p>
                    </div>
                </div>
            @endforeach
        </section>

        @php
            $buatPengaduanRoute = Route::has('pengaduan.index') ? route('pengaduan.index') : '#';
        @endphp

        <section class="bg-white border border-gray-100 rounded-[32px] overflow-hidden shadow-figma-container">
            <div class="px-8 py-6 border-b flex flex-col sm:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-xl text-gray-800">
                    Pengaduan Terbaru
                </h2>

                @if($buatPengaduanRoute !== '#')
                    <a href="{{ $buatPengaduanRoute }}" class="bg-[#0090F5] hover:bg-blue-600 transition-colors text-white px-6 py-3 rounded-xl font-bold text-sm w-full sm:w-auto text-center">
                        + Buat Pengaduan
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left min-w-[1000px]">
                    <thead class="bg-[#F8FAFC] text-gray-500 uppercase text-xs font-extrabold tracking-wider border-b">
                        <tr>
                            <th class="py-5 px-6">ID PGD</th>
                            <th class="py-5 px-6">Pelapor</th>
                            <th class="py-5 px-6">Lokasi Masalah</th>
                            <th class="py-5 px-6">Fasilitas</th>
                            <th class="py-5 px-6">Tanggal Lapor</th>
                            <th class="py-5 px-6">Status</th>
                            <th class="py-5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tugas as $t)
                            @php
                                $idTugas = $t->id_tindak_lanjut ?? $t->id;
                                $pengaduan = $t->pengaduan;

                                $idPengaduan = $pengaduan?->id_pengaduan
                                    ?? $t->id_pengaduan
                                    ?? $idTugas;

                                $pelapor = data_get($pengaduan, 'pelapor.nama')
                                    ?? data_get($pengaduan, 'user.nama')
                                    ?? data_get($pengaduan, 'user.name')
                                    ?? 'Tidak diketahui';

                                $lokasi = data_get($pengaduan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                                $fasilitas = data_get($pengaduan, 'fasilitas.kategori.nama_kategori', '-') . ' (' . data_get($pengaduan, 'fasilitas.no_fasilitas', '-') . ')';

                                $tanggalLapor = data_get($pengaduan, 'tanggal_lapor')
                                    ? \Carbon\Carbon::parse(data_get($pengaduan, 'tanggal_lapor'))->format('d/m/Y')
                                    : (
                                        data_get($pengaduan, 'created_at')
                                            ? \Carbon\Carbon::parse(data_get($pengaduan, 'created_at'))->format('d/m/Y')
                                            : '-'
                                    );

                                $statusPenanganan = $t->status_penanganan ?? 'ON PROGRES';

                                $badgeClass = match($statusPenanganan) {
                                    'DONE' => 'bg-[#E6F9EE] text-[#22C55E]',
                                    'CANCEL' => 'bg-[#FEE2E2] text-[#DC2626]',
                                    'NO SPAREPART', 'NO_SPAREPART' => 'bg-[#E5E7EB] text-[#374151]',
                                    default => 'bg-[#FFF9E6] text-[#F59E0B]',
                                };

                                $statusLabel = match($statusPenanganan) {
                                    'DONE' => 'Done',
                                    'CANCEL' => 'Cancel',
                                    'NO SPAREPART', 'NO_SPAREPART' => 'No Sparepart',
                                    default => 'On Progress',
                                };

                                $updateAction = Route::has('tindak-lanjut.update')
                                    ? route('tindak-lanjut.update', $t)
                                    : '#';

                                $fotoKerusakanUrl = data_get($pengaduan, 'foto_kerusakan_url');
                            @endphp

                            <tr class="hover:bg-slate-50 cursor-pointer" onclick="toggleDetails('row-{{ $idTugas }}')">
                                <td class="py-5 px-6 text-sm font-semibold text-[#64748B]">
                                    PGD-{{ str_pad((string) $idPengaduan, 3, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="py-5 px-6 text-sm text-gray-700 font-medium">
                                    {{ $pelapor }}
                                </td>

                                <td class="py-5 px-6 text-sm text-gray-500">
                                    {{ $lokasi }}
                                </td>

                                <td class="py-5 px-6 text-sm text-gray-800 font-semibold">
                                    {{ $fasilitas }}
                                </td>

                                <td class="py-5 px-6 text-sm text-gray-500">
                                    {{ $tanggalLapor }}
                                </td>

                                <td class="py-5 px-6">
                                    <span class="inline-block text-xs font-bold px-4 py-1.5 rounded-md text-center {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="py-5 px-6 text-center" onclick="event.stopPropagation()">
                                    <button type="button" onclick="toggleDetails('row-{{ $idTugas }}')" class="text-xs font-bold text-[#0090F5] bg-sky-50 px-4 py-2 rounded-lg">
                                        Detail
                                    </button>
                                </td>
                            </tr>

                            <tr id="row-{{ $idTugas }}" class="hidden bg-gray-50">
                                <td colspan="7" class="px-8 py-6 border-b">
                                    <form method="POST" action="{{ $updateAction }}" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <p class="text-sm font-bold text-gray-700 mb-2">
                                                Detail Kerusakan
                                            </p>

                                            <div class="bg-white p-4 rounded-2xl border border-gray-200 text-sm text-gray-600 italic">
                                                "{{ $pengaduan?->deskripsi_kerusakan ?? 'Tidak ada deskripsi.' }}"
                                            </div>

                                            @if($fotoKerusakanUrl)
                                                <div class="mt-4">
                                                    <p class="text-sm font-bold text-gray-700 mb-2">
                                                        Foto Kerusakan
                                                    </p>

                                                    <img src="{{ $fotoKerusakanUrl }}" alt="Foto Kerusakan" class="w-full max-h-48 object-cover rounded-2xl border border-gray-200 shadow-sm">
                                                </div>
                                            @endif

                                            <p class="text-sm font-bold text-gray-700 mt-4 mb-2">
                                                Catatan Perbaikan
                                            </p>

                                            <textarea name="catatan_perbaikan" class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0090F5]/20" rows="3">{{ $t->catatan_perbaikan }}</textarea>
                                        </div>

                                        <div class="flex flex-col gap-3">
                                            <p class="text-sm font-bold text-gray-700">
                                                Update Status
                                            </p>

                                            <select name="status_penanganan" class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0090F5]/20 bg-white">
                                                <option value="ON PROGRES" {{ $statusPenanganan === 'ON PROGRES' ? 'selected' : '' }} style="background-color: #FBBF24; color: #fff;">
                                                    On Progress
                                                </option>
                                                <option value="DONE" {{ $statusPenanganan === 'DONE' ? 'selected' : '' }} style="background-color: #4ADE80; color: #fff;">
                                                    Done
                                                </option>
                                                <option value="CANCEL" {{ $statusPenanganan === 'CANCEL' ? 'selected' : '' }} style="background-color: #EF4444; color: #fff;">
                                                    Cancel
                                                </option>
                                                <option value="NO SPAREPART" {{ in_array($statusPenanganan, ['NO SPAREPART', 'NO_SPAREPART'], true) ? 'selected' : '' }} style="background-color: #9CA3AF; color: #fff;">
                                                    No Sparepart
                                                </option>
                                            </select>

                                            <button type="submit" class="bg-[#0090F5] hover:bg-blue-600 transition-colors text-white px-6 py-3 rounded-2xl font-bold text-sm w-full">
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-gray-400">
                                    Belum ada pengaduan.
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
    function handleResize() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');

        if (!sidebar || !overlay) return;

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

        if (!sidebar || !overlay) return;

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function toggleDetails(id) {
        const row = document.getElementById(id);

        if (!row) return;

        row.classList.toggle('hidden');
    }

    window.addEventListener('resize', handleResize);
    window.addEventListener('load', handleResize);
</script>
@endsection