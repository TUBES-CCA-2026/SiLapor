@extends('layouts.app')

@section('title', 'Pengaduan - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $selectedFacilityId = (string) old('id_fasilitas', '');
    $selectedFacility = $facilities->firstWhere('id_fasilitas', old('id_fasilitas'));
    $selectedKodeBarang = $selectedFacility?->no_fasilitas ?: '-';
    $selectedNamaFasilitas = $selectedFacility?->nama_fasilitas ?: '-';
    $selectedLabName = $selectedFacility?->laboratorium?->nama_laboratorium ?: '-';
    $selectedLabLocation = $selectedFacility?->laboratorium?->lokasi;
    $selectedLokasiLab = $selectedLabLocation ? $selectedLabName . ' - ' . $selectedLabLocation : $selectedLabName;
@endphp

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); }
    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
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

                <a href="{{ route('pengaduan.index') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-regular fa-file-lines text-lg text-[#0090F5]"></i>
                        <span>Pengaduan</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>

                <a href="{{ route('tindak-lanjut.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    <span>Tindak Lanjut</span>
                </a>
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Riwayat</span>
                </a>
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span>Teknisi</span>
                </a>
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-user text-lg"></i>
                    <span>Profil</span>
                </a>
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

    <main class="flex-1 px-4 py-6 md:px-10 md:py-8 overflow-x-hidden w-full min-w-0">
        <header class="flex items-center justify-between pb-8">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Pengaduan</h1>
            </div>

            <div class="bg-[#0090F5] text-white px-5 py-2.5 rounded-2xl flex items-center gap-3.5 shadow-md">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0090F5] shrink-0">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div class="text-left flex flex-col justify-center leading-tight">
                    <span class="text-[11px] font-light opacity-90 block">Selamat datang,</span>
                    <span class="text-sm font-extrabold block tracking-wide">{{ auth()->user()->nama ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <div class="bg-white p-8 rounded-[32px] shadow-figma-container border border-gray-150 w-full">
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-gray-800">Form Pengaduan Kerusakan</h2>
                <p class="text-sm text-gray-500 mt-1">Nama pelapor otomatis dari akun login. Pilih fasilitas agar kode barang, nama fasilitas, dan lokasi lab terisi otomatis.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Pelapor</label>
                        <input type="text" value="{{ auth()->user()->nama ?? '-' }}" readonly
                               class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fasilitas yang Dilaporkan</label>
                        <select id="id_fasilitas" name="id_fasilitas" required
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]">
                            <option value="">— Pilih fasilitas —</option>
                            @foreach ($facilities as $item)
                                <option value="{{ $item->id_fasilitas }}" {{ $selectedFacilityId === (string) $item->id_fasilitas ? 'selected' : '' }}>
                                    {{ $item->nama_fasilitas }}
                                    @if ($item->no_fasilitas)
                                        ({{ $item->no_fasilitas }})
                                    @endif
                                    — {{ $item->laboratorium?->nama_laboratorium ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kode Barang</label>
                        <input id="kode_barang" type="text" value="{{ $selectedKodeBarang }}" readonly
                               class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Fasilitas</label>
                        <input id="nama_fasilitas" type="text" value="{{ $selectedNamaFasilitas }}" readonly
                               class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Lab</label>
                        <input id="lokasi_lab" type="text" value="{{ $selectedLokasiLab }}" readonly
                               class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Kerusakan</label>
                    <textarea name="deskripsi_kerusakan" rows="5" required
                              class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]"
                              placeholder="Jelaskan kerusakan fasilitas secara singkat dan jelas.">{{ old('deskripsi_kerusakan') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload Foto Kerusakan</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center bg-[#F8FAFC] cursor-pointer hover:border-[#0090F5] transition-colors">
                        <input type="file" name="foto_kerusakan" accept="image/*" class="hidden" id="fileInput" required>
                        <label for="fileInput" class="flex flex-col items-center cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-[#0090F5] mb-2"></i>
                            <span id="fileLabel" class="text-[#0090F5] font-bold">Upload foto</span>
                            <span class="text-xs text-gray-400 mt-1">Klik untuk memilih foto format JPG/PNG/WEBP, maksimal 4 MB</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <button type="submit" {{ $facilities->isEmpty() ? 'disabled' : '' }}
                            class="w-full bg-[#0090F5] hover:bg-[#007cd5] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold py-4 rounded-2xl shadow-md transition-all">
                        Kirim Pengaduan
                    </button>

                    <a href="{{ route('scan.index') }}" class="flex items-center justify-center gap-2 text-gray-500 hover:text-[#0090F5] transition-colors font-medium text-sm">
                        <i class="fa-solid fa-qrcode"></i>
                        Gunakan QR Code untuk pelaporan instan
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    const facilities = @json($facilityPayload);
    const facilityMap = Object.fromEntries(facilities.map((item) => [String(item.id), item]));

    function fillFacilityDetail(id) {
        const detail = facilityMap[String(id)] || null;
        document.getElementById('kode_barang').value = detail?.kode_barang || '-';
        document.getElementById('nama_fasilitas').value = detail?.nama_fasilitas || '-';
        document.getElementById('lokasi_lab').value = detail?.lokasi_lab || '-';
    }

    const facilitySelect = document.getElementById('id_fasilitas');
    if (facilitySelect) {
        facilitySelect.addEventListener('change', function () {
            fillFacilityDetail(this.value);
        });
        fillFacilityDetail(facilitySelect.value);
    }

    const fileInput = document.getElementById('fileInput');
    const fileLabel = document.getElementById('fileLabel');
    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function () {
            fileLabel.textContent = this.files?.[0]?.name || 'Upload foto';
        });
    }

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

    window.addEventListener('resize', handleResponsiveSidebar);
    window.addEventListener('load', handleResponsiveSidebar);
</script>
@endsection
