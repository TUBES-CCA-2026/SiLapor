@extends('layouts.app')

@section('title', 'Pengaduan - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'pengaduan';
    $pageTitle = 'PENGADUAN';

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $selectedFacilityId = (string) old('id_fasilitas', request('id_fasilitas', ''));

    $selectedFacility = collect($facilities ?? [])->first(function ($facility) use ($selectedFacilityId) {
        return (string) $facility->id_fasilitas === $selectedFacilityId;
    });

    $selectedKodeBarang = $selectedFacility?->no_fasilitas ?? '-';
    $selectedNamaFasilitas = $selectedFacility?->nama_fasilitas ?? '-';
    $selectedLabName = $selectedFacility?->laboratorium?->nama_laboratorium ?? '-';
    $selectedLabLocation = $selectedFacility?->laboratorium?->lokasi;

    $selectedLokasiLab = $selectedLabLocation
        ? $selectedLabName . ' - ' . $selectedLabLocation
        : $selectedLabName;
@endphp

@once
<style>
    .font-figma {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .shadow-figma-container {
        box-shadow: 0 15px 50px rgba(0, 0, 0, .05);
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
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
    @include('partials.sidebar', ['user' => auth()->user(), 'activeMenu' => 'pengaduan'])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-8">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">
                    Pengaduan
                </h1>
            </div>

            @include('partials.user-welcome-box', ['user' => $user ?? auth()->user()])
        </header>

        <div class="bg-white p-8 rounded-[32px] shadow-figma-container border border-gray-100 w-full">
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-gray-800">
                    Form Pengaduan Kerusakan
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Nama pelapor otomatis dari akun login. Pilih fasilitas agar kode barang, nama fasilitas, dan lokasi lab terisi otomatis.
                </p>
            </div>

            <form action="{{ $routeSafe('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nama Pelapor
                        </label>

                        <input
                            type="text"
                            value="{{ $user->nama ?? $user->name ?? '-' }}"
                            readonly
                            class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Fasilitas yang Dilaporkan
                        </label>

                        <div class="relative custom-searchable-select">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    placeholder="— Pilih fasilitas —" 
                                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC] searchable-select-trigger cursor-pointer font-medium text-gray-700"
                                    readonly
                                >
                                <span class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-chevron-down text-sm"></i>
                                </span>
                            </div>
                            <div class="absolute left-0 right-0 mt-2 bg-white border border-gray-250 rounded-2xl shadow-lg z-50 hidden searchable-select-dropdown">
                                <div class="p-3 border-b border-gray-100">
                                    <input 
                                        type="text" 
                                        placeholder="Cari fasilitas..." 
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0090F5] searchable-select-search"
                                    >
                                </div>
                                <ul class="max-h-60 overflow-y-auto py-2 text-sm text-gray-700 searchable-select-options">
                                    <li data-value="" class="px-5 py-2.5 hover:bg-gray-50 cursor-pointer text-gray-400">— Pilih fasilitas —</li>
                                    @foreach ($facilities as $item)
                                        <li data-value="{{ $item->id_fasilitas }}" class="px-5 py-2.5 hover:bg-gray-50 cursor-pointer">
                                            {{ $item->nama_fasilitas }} — {{ $item->laboratorium?->nama_laboratorium ?? '-' }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <input type="hidden" name="id_fasilitas" id="id_fasilitas" value="{{ $selectedFacilityId }}" required>
                        </div>

                        @if ($facilities->isEmpty())
                            <p class="text-xs text-red-500 mt-1">
                                Belum ada fasilitas yang dapat dipilih.
                            </p>
                        @endif
                    </div>
                </div>

                <input type="hidden" id="kode_barang" value="{{ $selectedKodeBarang }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nama Fasilitas
                        </label>

                        <input
                            id="nama_fasilitas"
                            type="text"
                            value="{{ $selectedNamaFasilitas }}"
                            readonly
                            class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Lokasi Lab
                        </label>

                        <input
                            id="lokasi_lab"
                            type="text"
                            value="{{ $selectedLokasiLab }}"
                            readonly
                            class="w-full border border-gray-200 rounded-2xl px-5 py-4 bg-gray-100 text-gray-600"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Deskripsi Kerusakan
                    </label>

                    <textarea
                        name="deskripsi_kerusakan"
                        rows="5"
                        required
                        class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]"
                        placeholder="Jelaskan kerusakan fasilitas secara singkat dan jelas."
                    >{{ old('deskripsi_kerusakan') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Upload Foto Kerusakan
                    </label>

                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center bg-[#F8FAFC] cursor-pointer hover:border-[#0090F5] transition-colors">
                        <input
                            type="file"
                            name="foto_kerusakan"
                            accept="image/*"
                            class="hidden"
                            id="fileInput"
                        >

                        <label for="fileInput" class="flex flex-col items-center cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-[#0090F5] mb-2"></i>
                            <span id="fileLabel" class="text-[#0090F5] font-bold">
                                Upload foto
                            </span>
                            <span class="text-xs text-gray-400 mt-1">
                                Klik untuk memilih foto format JPG/PNG/WEBP, maksimal 4 MB
                            </span>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <button
                        type="submit"
                        {{ $facilities->isEmpty() ? 'disabled' : '' }}
                        class="w-full bg-[#0090F5] hover:bg-[#007cd5] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold py-4 rounded-2xl shadow-md transition-all"
                    >
                        Kirim Pengaduan
                    </button>

                    <a href="{{ $routeSafe('scan.index') }}" class="flex items-center justify-center gap-2 text-gray-500 hover:text-[#0090F5] transition-colors font-medium text-sm">
                        <i class="fa-solid fa-qrcode"></i>
                        Gunakan QR Code untuk pelaporan instan
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    // Searchable Select Component Logic
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.custom-searchable-select').forEach(function (wrapper) {
            const trigger = wrapper.querySelector('.searchable-select-trigger');
            const dropdown = wrapper.querySelector('.searchable-select-dropdown');
            const searchInput = wrapper.querySelector('.searchable-select-search');
            const optionsList = wrapper.querySelector('.searchable-select-options');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const options = optionsList.querySelectorAll('li');

            // Update trigger text initially
            const initialValue = hiddenInput.value;
            const initialOption = Array.from(options).find(opt => opt.getAttribute('data-value') === initialValue);
            if (initialOption) {
                trigger.value = initialOption.textContent.trim();
            }

            // Open/close dropdown
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                document.querySelectorAll('.searchable-select-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.add('hidden');
                });
                dropdown.classList.toggle('hidden');
                if (!dropdown.classList.contains('hidden')) {
                    searchInput.value = '';
                    options.forEach(opt => opt.style.display = '');
                    searchInput.focus();
                }
            });

            dropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            // Filter search
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase();
                options.forEach(function (option) {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(query) ? '' : 'none';
                });
            });

            // Select value
            options.forEach(function (option) {
                option.addEventListener('click', function () {
                    const val = option.getAttribute('data-value');
                    const text = option.textContent.trim();
                    hiddenInput.value = val;
                    trigger.value = val ? text : '';
                    dropdown.classList.add('hidden');
                    
                    // Dispatch change event to trigger other calculations
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
        });

        document.addEventListener('click', function () {
            document.querySelectorAll('.searchable-select-dropdown').forEach(d => d.classList.add('hidden'));
        });
    });

    const facilities = @json($facilityPayload ?? []);
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

        if (!sidebar || !overlay) return;

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

        if (!sidebar || !overlay) return;

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