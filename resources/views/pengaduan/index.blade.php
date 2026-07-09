@extends('layouts.app')

@section('title', 'Pengaduan - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $user = auth()->user();
    $role = $user?->role;
    $activeMenu = 'pengaduan';

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $labs = $laboratoriums ?? collect();
    $cats = $categories ?? collect();
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

    /* Searchable Select Styles */
    .pgd-select-wrapper {
        position: relative;
    }

    .pgd-select-trigger {
        width: 100%;
        border: 1px solid #E2E8F0;
        border-radius: 1rem;
        padding: 0.875rem 2.75rem 0.875rem 1.25rem;
        background: #F8FAFC;
        font-size: 0.875rem;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        outline: none;
        transition: all 0.2s ease;
        text-align: left;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pgd-select-trigger:focus,
    .pgd-select-trigger.active {
        border-color: #0090F5;
        box-shadow: 0 0 0 3px rgba(0, 144, 245, 0.12);
        background: #fff;
    }

    .pgd-select-trigger.has-value {
        color: #1E293B;
        font-weight: 600;
    }

    .pgd-select-chevron {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #94A3B8;
        font-size: 0.75rem;
        transition: transform 0.2s ease;
    }

    .pgd-select-wrapper.open .pgd-select-chevron {
        transform: translateY(-50%) rotate(180deg);
    }

    .pgd-select-dropdown {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 1rem;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        z-index: 60;
        display: none;
        overflow: hidden;
    }

    .pgd-select-wrapper.open .pgd-select-dropdown {
        display: block;
        animation: pgdDropIn 0.15s ease-out;
    }

    @keyframes pgdDropIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pgd-select-search {
        width: 100%;
        border: none;
        border-bottom: 1px solid #F1F5F9;
        padding: 0.75rem 1rem;
        font-size: 0.8125rem;
        color: #334155;
        outline: none;
        background: #FAFBFC;
    }

    .pgd-select-search::placeholder {
        color: #94A3B8;
    }

    .pgd-select-options {
        max-height: 220px;
        overflow-y: auto;
        list-style: none;
        margin: 0;
        padding: 0.375rem 0;
    }

    .pgd-select-options::-webkit-scrollbar {
        width: 5px;
    }

    .pgd-select-options::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 3px;
    }

    .pgd-select-options li {
        padding: 0.625rem 1rem;
        font-size: 0.8125rem;
        color: #475569;
        cursor: pointer;
        transition: background 0.12s ease;
        font-weight: 500;
    }

    .pgd-select-options li:hover {
        background: #F0F9FF;
        color: #0090F5;
    }

    .pgd-select-options li.selected {
        background: #EFF8FF;
        color: #0090F5;
        font-weight: 700;
    }

    .pgd-select-options li.empty-msg {
        color: #94A3B8;
        font-style: italic;
        cursor: default;
        text-align: center;
        padding: 1rem;
    }

    .pgd-select-options li.empty-msg:hover {
        background: transparent;
        color: #94A3B8;
    }

    .pgd-field-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .pgd-field-label .required-star {
        color: #EF4444;
        margin-left: 2px;
    }

    .pgd-readonly-input {
        width: 100%;
        border: 1px solid #E2E8F0;
        border-radius: 1rem;
        padding: 0.875rem 1.25rem;
        background: #F1F5F9;
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748B;
        cursor: not-allowed;
    }

    .pgd-form-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #F1F5F9;
    }

    .pgd-form-section-title i {
        color: #0090F5;
        font-size: 0.875rem;
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

        {{-- Alert messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl font-bold text-sm">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('duplicate_error'))
            <div class="bg-amber-50 border border-amber-200 text-amber-700 px-5 py-4 rounded-2xl font-bold text-sm">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ session('duplicate_error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl font-bold text-sm">
                <i class="fa-solid fa-circle-xmark mr-2"></i>
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>@if(!$loop->last), @endif
                @endforeach
            </div>
        @endif

        <div class="bg-white p-8 rounded-[32px] shadow-figma-container border border-gray-100 w-full">
            <div class="mb-8">
                <h2 class="text-xl font-extrabold text-gray-800">
                    Form Pengaduan Kerusakan
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Pilih lokasi lab, kategori, dan kode barang. Nama fasilitas akan terisi otomatis.
                </p>
            </div>

            <form action="{{ $routeSafe('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="pengaduanForm">
                @csrf

                {{-- Section: Identitas Pelapor --}}
                <div>
                    <div class="pgd-form-section-title">
                        <i class="fa-solid fa-user"></i>
                        <span>Identitas Pelapor</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="pgd-field-label">
                                Nama Pelapor
                            </label>

                            <input
                                type="text"
                                value="{{ $user->nama ?? $user->name ?? '-' }}"
                                readonly
                                class="pgd-readonly-input"
                            >
                        </div>
                    </div>
                </div>

                {{-- Section: Informasi Fasilitas --}}
                <div>
                    <div class="pgd-form-section-title">
                        <i class="fa-solid fa-laptop-medical"></i>
                        <span>Informasi Fasilitas</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- 1. Lokasi Lab --}}
                        <div>
                            <label class="pgd-field-label">
                                Lokasi Lab <span class="required-star">*</span>
                            </label>

                            <div class="pgd-select-wrapper" id="lokasi-lab-wrapper">
                                <button type="button" class="pgd-select-trigger" id="lokasi-lab-trigger">
                                    Pilih lokasi lab
                                </button>
                                <span class="pgd-select-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                <div class="pgd-select-dropdown">
                                    <input type="text" class="pgd-select-search" placeholder="Cari laboratorium...">
                                    <ul class="pgd-select-options">
                                        <li data-value="all">📍 Semua Lab</li>
                                        @foreach ($labs as $lab)
                                            <li data-value="{{ $lab->id_laboratorium }}">{{ $lab->nama_laboratorium }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <input type="hidden" name="id_laboratorium_filter" id="id_laboratorium_filter" value="">
                            </div>
                        </div>

                        {{-- 2. Kategori Barang --}}
                        <div>
                            <label class="pgd-field-label">
                                Kategori Barang <span class="required-star">*</span>
                            </label>

                            <div class="pgd-select-wrapper" id="kategori-wrapper">
                                <button type="button" class="pgd-select-trigger" id="kategori-trigger">
                                    Pilih kategori barang
                                </button>
                                <span class="pgd-select-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                <div class="pgd-select-dropdown">
                                    <input type="text" class="pgd-select-search" placeholder="Cari kategori...">
                                    <ul class="pgd-select-options" id="kategori-options">
                                        @foreach ($cats as $cat)
                                            <li data-value="{{ $cat->id_kategori }}">{{ $cat->nama_kategori }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <input type="hidden" id="id_kategori_filter" value="">
                            </div>
                        </div>

                        {{-- 3. Kode Barang --}}
                        <div>
                            <label class="pgd-field-label">
                                Kode Barang <span class="required-star">*</span>
                            </label>

                            <div class="pgd-select-wrapper" id="kode-barang-wrapper">
                                <button type="button" class="pgd-select-trigger" id="kode-barang-trigger">
                                    Pilih atau cari kode barang
                                </button>
                                <span class="pgd-select-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                <div class="pgd-select-dropdown">
                                    <input type="text" class="pgd-select-search" placeholder="Cari kode barang...">
                                    <ul class="pgd-select-options" id="kode-barang-options">
                                        <li class="empty-msg">Pilih lokasi lab terlebih dahulu</li>
                                    </ul>
                                </div>
                                <input type="hidden" name="id_fasilitas" id="id_fasilitas" value="" required>
                            </div>
                        </div>

                        {{-- 4. Nama Barang / Fasilitas (readonly auto-fill) --}}
                        <div>
                            <label class="pgd-field-label">
                                Nama Barang / Fasilitas
                            </label>

                            <input
                                id="nama_fasilitas"
                                type="text"
                                value="-"
                                readonly
                                class="pgd-readonly-input"
                                placeholder="Terisi otomatis dari kode barang"
                            >
                        </div>
                    </div>
                </div>

                {{-- Section: Detail Kerusakan --}}
                <div>
                    <div class="pgd-form-section-title">
                        <i class="fa-solid fa-file-pen"></i>
                        <span>Detail Kerusakan</span>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="pgd-field-label">
                                Deskripsi Kerusakan <span class="required-star">*</span>
                            </label>

                            <textarea
                                name="deskripsi_kerusakan"
                                rows="5"
                                required
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] focus:border-[#0090F5] bg-[#F8FAFC] text-sm transition-all outline-none"
                                placeholder="Jelaskan kerusakan fasilitas secara singkat dan jelas."
                            >{{ old('deskripsi_kerusakan') }}</textarea>
                        </div>

                        <div>
                            <label class="pgd-field-label">
                                Upload Foto Kerusakan
                            </label>

                            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center bg-[#F8FAFC] cursor-pointer hover:border-[#0090F5] transition-colors" id="dropZone">
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

                                <div id="imagePreview" class="mt-4 hidden">
                                    <img id="previewImg" src="" alt="Preview" class="max-h-40 mx-auto rounded-xl shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="space-y-4">
                    <button
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-[#0090F5] hover:bg-[#007cd5] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold py-4 rounded-2xl shadow-md transition-all"
                    >
                        <i class="fa-solid fa-paper-plane mr-2"></i>
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
(function () {
    'use strict';

    // ==========================================
    // Reusable Searchable Select Component
    // ==========================================
    function initSearchableSelect(wrapperId, config) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return null;

        const trigger = wrapper.querySelector('.pgd-select-trigger');
        const dropdown = wrapper.querySelector('.pgd-select-dropdown');
        const searchInput = wrapper.querySelector('.pgd-select-search');
        const optionsList = wrapper.querySelector('.pgd-select-options');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');

        let selectedValue = '';

        function open() {
            wrapper.classList.add('open');
            trigger.classList.add('active');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            filterOptions('');
        }

        function close() {
            wrapper.classList.remove('open');
            trigger.classList.remove('active');
        }

        function isOpen() {
            return wrapper.classList.contains('open');
        }

        function filterOptions(query) {
            const items = optionsList.querySelectorAll('li:not(.empty-msg)');
            const q = query.toLowerCase();
            let visibleCount = 0;

            items.forEach(function (item) {
                const text = item.textContent.toLowerCase();
                const match = text.includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            // Show/hide empty message
            let emptyMsg = optionsList.querySelector('.empty-msg');
            if (visibleCount === 0 && items.length > 0) {
                if (!emptyMsg) {
                    emptyMsg = document.createElement('li');
                    emptyMsg.className = 'empty-msg';
                    optionsList.appendChild(emptyMsg);
                }
                emptyMsg.textContent = 'Tidak ditemukan';
                emptyMsg.style.display = '';
            } else if (emptyMsg && items.length > 0) {
                emptyMsg.style.display = 'none';
            }
        }

        function selectValue(value, label) {
            selectedValue = value;
            if (hiddenInput) hiddenInput.value = value;
            trigger.textContent = label || config.placeholder || '';
            trigger.classList.toggle('has-value', !!value);

            // Update selected state
            optionsList.querySelectorAll('li').forEach(function (li) {
                li.classList.toggle('selected', li.getAttribute('data-value') === value);
            });

            close();

            if (config.onChange) config.onChange(value, label);
        }

        function reset(placeholder) {
            selectedValue = '';
            if (hiddenInput) hiddenInput.value = '';
            trigger.textContent = placeholder || config.placeholder || '';
            trigger.classList.remove('has-value');
            optionsList.querySelectorAll('li').forEach(function (li) {
                li.classList.remove('selected');
            });
        }

        function setOptions(items) {
            optionsList.innerHTML = '';
            if (!items || items.length === 0) {
                const emptyLi = document.createElement('li');
                emptyLi.className = 'empty-msg';
                emptyLi.textContent = config.emptyMessage || 'Tidak ada data';
                optionsList.appendChild(emptyLi);
                return;
            }
            items.forEach(function (item) {
                const li = document.createElement('li');
                li.setAttribute('data-value', item.value);
                li.innerHTML = item.label;
                if (item.sublabel) {
                    li.innerHTML += ' <span style="color:#94A3B8"> — ' + item.sublabel + '</span>';
                }
                optionsList.appendChild(li);
            });
        }

        // Event Listeners
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (isOpen()) {
                close();
            } else {
                // Close all other dropdowns first
                document.querySelectorAll('.pgd-select-wrapper.open').forEach(function (w) {
                    if (w !== wrapper) w.classList.remove('open');
                });
                open();
            }
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                filterOptions(this.value);
            });
            searchInput.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        optionsList.addEventListener('click', function (e) {
            const li = e.target.closest('li');
            if (!li || li.classList.contains('empty-msg')) return;
            e.stopPropagation();

            const val = li.getAttribute('data-value');
            const label = li.textContent.trim();
            selectValue(val, label);
        });

        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        return {
            reset: reset,
            setOptions: setOptions,
            selectValue: selectValue,
            getValue: function () { return selectedValue; },
        };
    }

    // ==========================================
    // Close all dropdowns on outside click
    // ==========================================
    document.addEventListener('click', function () {
        document.querySelectorAll('.pgd-select-wrapper.open').forEach(function (w) {
            w.classList.remove('open');
            w.querySelector('.pgd-select-trigger')?.classList.remove('active');
        });
    });

    // ==========================================
    // API Fetch Helper
    // ==========================================
    const apiFasilitasUrl = @json(route('pengaduan.api.fasilitas'));

    async function fetchFasilitas(params) {
        const url = new URL(apiFasilitasUrl, window.location.origin);
        Object.keys(params).forEach(function (key) {
            if (params[key]) url.searchParams.set(key, params[key]);
        });

        try {
            const response = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            if (!response.ok) throw new Error('Network error');
            return await response.json();
        } catch (err) {
            console.error('Failed to fetch fasilitas:', err);
            return [];
        }
    }

    // ==========================================
    // Data cache
    // ==========================================
    let cachedFasilitas = [];

    // ==========================================
    // Initialize all selects
    // ==========================================
    const lokasiLabSelect = initSearchableSelect('lokasi-lab-wrapper', {
        placeholder: 'Pilih lokasi lab',
        onChange: async function (value) {
            // Reset downstream
            kategoriSelect.reset('Pilih kategori barang');
            kodeBarangSelect.reset('Pilih atau cari kode barang');
            document.getElementById('nama_fasilitas').value = '-';
            document.getElementById('id_fasilitas').value = '';

            // Fetch fasilitas for this lab
            const params = {};
            if (value && value !== 'all') {
                params.id_laboratorium = value;
            }
            cachedFasilitas = await fetchFasilitas(params);

            // Extract unique categories from fetched data
            const uniqueCategories = {};
            cachedFasilitas.forEach(function (f) {
                if (f.id_kategori && f.id_kategori !== '0' && f.id_kategori !== '') {
                    uniqueCategories[f.id_kategori] = f.nama_kategori;
                }
            });

            const catOptions = Object.keys(uniqueCategories).map(function (id) {
                return { value: id, label: uniqueCategories[id] };
            });

            kategoriSelect.setOptions(catOptions.length > 0 ? catOptions : []);

            // Also update kode barang with all items from this lab
            updateKodeBarangOptions(cachedFasilitas);
        }
    });

    const kategoriSelect = initSearchableSelect('kategori-wrapper', {
        placeholder: 'Pilih kategori barang',
        emptyMessage: 'Pilih lokasi lab terlebih dahulu',
        onChange: function (value) {
            // Reset downstream
            kodeBarangSelect.reset('Pilih atau cari kode barang');
            document.getElementById('nama_fasilitas').value = '-';
            document.getElementById('id_fasilitas').value = '';

            // Filter kode barang by category
            let filtered = cachedFasilitas;
            if (value) {
                filtered = cachedFasilitas.filter(function (f) {
                    return String(f.id_kategori) === String(value);
                });
            }

            updateKodeBarangOptions(filtered);
        }
    });

    const kodeBarangSelect = initSearchableSelect('kode-barang-wrapper', {
        placeholder: 'Pilih atau cari kode barang',
        emptyMessage: 'Pilih lokasi lab terlebih dahulu',
        onChange: function (value) {
            // Auto-fill nama fasilitas
            const found = cachedFasilitas.find(function (f) {
                return String(f.id) === String(value);
            });
            document.getElementById('nama_fasilitas').value = found ? found.nama_fasilitas : '-';
        }
    });

    function updateKodeBarangOptions(items) {
        if (!items || items.length === 0) {
            kodeBarangSelect.setOptions([]);
            return;
        }

        kodeBarangSelect.setOptions(
            items.map(function (f) {
                return {
                    value: f.id,
                    label: f.no_fasilitas || '-',
                    sublabel: f.nama_fasilitas + ' (' + f.nama_laboratorium + ')',
                };
            })
        );
    }

    // ==========================================
    // File Upload Preview
    // ==========================================
    const fileInput = document.getElementById('fileInput');
    const fileLabel = document.getElementById('fileLabel');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function () {
            const file = this.files?.[0];
            if (file) {
                fileLabel.textContent = file.name;

                // Show preview
                if (imagePreview && previewImg) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                fileLabel.textContent = 'Upload foto';
                if (imagePreview) imagePreview.classList.add('hidden');
            }
        });
    }

    // ==========================================
    // Sidebar Toggle
    // ==========================================
    window.handleResponsiveSidebar = function () {
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
    };

    window.toggleSidebar = function () {
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
    };

    window.addEventListener('resize', handleResponsiveSidebar);
    window.addEventListener('load', handleResponsiveSidebar);
})();
</script>
@endsection