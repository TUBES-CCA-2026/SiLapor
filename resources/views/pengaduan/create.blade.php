@extends('layouts.app')

@section('title', 'Pengaduan Manual - SiLapor')

@if(!auth()->check())
    @section('suppress_global_notification', 'true')
@endif

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $isQr = $mode === 'qr';

    $selectedFacilityId = $isQr
        ? (string) $fasilitas->id_fasilitas
        : (string) old('id_fasilitas', '');

    $selectedFacility = $isQr
        ? $fasilitas
        : ($facilities->firstWhere('id_fasilitas', old('id_fasilitas')) ?? null);



    $backUrl = null;
    $backLabel = 'Kembali';

    if (auth()->check()) {
        if (auth()->user()->isAsisten()) {
            $backUrl = route('pengaduan.index');
            $backLabel = 'Kembali ke Halaman Pengaduan';
        } else {
            $backUrl = route('dashboard');
            $backLabel = 'Kembali ke Dashboard';
        }
    } else {
        $backUrl = route('login');
        $backLabel = 'Kembali ke Login';
    }

    $labs = $laboratoriums ?? collect();
    $cats = $categories ?? collect();
    $apiUrl = route('pengaduan.manual.api.fasilitas');
@endphp

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }

    .pgd-select-wrapper { position: relative; }

    .pgd-select-trigger {
        width: 100%; border: 1px solid #E2E8F0; border-radius: 0.75rem;
        padding: 0.65rem 2.5rem 0.65rem 1rem; background: #F8FAFC;
        font-size: 0.8125rem; font-weight: 500; color: #334155;
        cursor: pointer; outline: none; transition: all 0.2s ease;
        text-align: left; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .pgd-select-trigger:focus, .pgd-select-trigger.active {
        border-color: #29ABE2; box-shadow: 0 0 0 3px rgba(41, 171, 226, 0.12); background: #fff;
    }
    .pgd-select-trigger.has-value { color: #1E293B; font-weight: 600; }

    .pgd-select-chevron {
        position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%);
        pointer-events: none; color: #94A3B8; font-size: 0.7rem; transition: transform 0.2s ease;
    }
    .pgd-select-wrapper.open .pgd-select-chevron { transform: translateY(-50%) rotate(180deg); }

    .pgd-select-dropdown {
        position: absolute; left: 0; right: 0; top: calc(100% + 6px);
        background: #fff; border: 1px solid #E2E8F0; border-radius: 0.75rem;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1); z-index: 60; display: none; overflow: hidden;
    }
    .pgd-select-wrapper.open .pgd-select-dropdown {
        display: block; animation: pgdDropIn 0.15s ease-out;
    }
    @keyframes pgdDropIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

    .pgd-select-search {
        width: 100%; border: none; border-bottom: 1px solid #F1F5F9;
        padding: 0.625rem 0.85rem; font-size: 0.8125rem; color: #334155; outline: none; background: #FAFBFC;
    }
    .pgd-select-search::placeholder { color: #94A3B8; }

    .pgd-select-options {
        max-height: 200px; overflow-y: auto; list-style: none; margin: 0; padding: 0.25rem 0;
    }
    .pgd-select-options::-webkit-scrollbar { width: 5px; }
    .pgd-select-options::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
    .pgd-select-options li {
        padding: 0.5rem 0.85rem; font-size: 0.8125rem; color: #475569; cursor: pointer;
        transition: background 0.12s ease; font-weight: 500;
    }
    .pgd-select-options li:hover { background: #E8F7FC; color: #29ABE2; }
    .pgd-select-options li.selected { background: #E8F7FC; color: #1B8DC4; font-weight: 700; }
    .pgd-select-options li.empty-msg {
        color: #94A3B8; font-style: italic; cursor: default; text-align: center; padding: 0.75rem;
    }
    .pgd-select-options li.empty-msg:hover { background: transparent; color: #94A3B8; }

    .pgd-field-label { display: block; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem; }
    .pgd-field-label .required-star { color: #EF4444; margin-left: 2px; }

    .pgd-readonly-input {
        width: 100%; border: 1px solid #E2E8F0; border-radius: 0.75rem;
        padding: 0.65rem 1rem; background: #F1F5F9; font-size: 0.8125rem;
        font-weight: 600; color: #64748B; cursor: not-allowed;
    }

    .pgd-section-title {
        display: flex; align-items: center; gap: 0.4rem;
        font-size: 0.7rem; font-weight: 800; color: #94A3B8;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 0.75rem; padding-bottom: 0.35rem; border-bottom: 1px solid #F1F5F9;
    }
    .pgd-section-title i { color: #29ABE2; font-size: 0.8rem; }
</style>

<div class="font-figma min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 mb-6">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-silapor-500 to-silapor-700 flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-lg"></i>
                </div>
                <span class="font-bold text-lg text-gray-900">SiLapor</span>
            </div>
        </div>

        {{-- Guest / Login Alert --}}
        @if ($isGuest)
            <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-3 flex items-center justify-between gap-3">
                <span>Anda melapor tanpa login. Nama pelapor wajib dipilih dari user yang sudah terdaftar.</span>
                <a href="{{ route('login') }}" class="whitespace-nowrap font-semibold text-silapor-600 hover:underline">Login dulu →</a>
            </div>
        @else
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                Nama pelapor otomatis memakai akun login: <strong>{{ auth()->user()->nama }}</strong>.
            </div>
        @endif

        {{-- Duplicate Error --}}
        @if(session('duplicate_error'))
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 font-semibold">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ session('duplicate_error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 font-semibold">
                <i class="fa-solid fa-circle-xmark mr-1"></i>
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>@if(!$loop->last), @endif
                @endforeach
            </div>
        @endif

         <p class="text-gray-500 text-sm mb-6">
            @if($isQr)
                Kode barang dan nama fasilitas otomatis terkunci berdasarkan QR Code yang dipindai.
            @else
                Pilih lokasi lab, kategori, dan kode barang. Nama fasilitas akan terisi otomatis.
            @endif
        </p>

        <form
            method="POST"
            action="{{ $isQr
                ? route('pengaduan.qr.store', $fasilitas->qr_code)
                : route('pengaduan.manual.store') }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf

            {{-- Section: Identitas Pelapor --}}
            <div>
                <div class="pgd-section-title">
                    <i class="fa-solid fa-user"></i>
                    <span>Identitas Pelapor</span>
                </div>

                @if ($isGuest)
                    <div>
                        <label class="pgd-field-label">
                            Nama Pelapor <span class="required-star">*</span>
                        </label>

                        <div class="pgd-select-wrapper" id="pelapor-wrapper">
                            <button type="button" class="pgd-select-trigger" id="pelapor-trigger">
                                — Pilih nama user terdaftar —
                            </button>
                            <span class="pgd-select-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                            <div class="pgd-select-dropdown">
                                <input type="text" class="pgd-select-search" placeholder="Cari pelapor...">
                                <ul class="pgd-select-options">
                                    @foreach ($users as $u)
                                        <li data-value="{{ $u->id_user }}">{{ $u->nama }} ({{ $u->role }})</li>
                                    @endforeach
                                </ul>
                            </div>
                            <input type="hidden" name="id_user" id="id_user" value="{{ old('id_user') }}" required>
                        </div>

                        <p class="text-xs text-gray-400 mt-1">
                            Pilih nama sesuai data user yang sudah terdaftar.
                        </p>
                    </div>
                @else
                    <div>
                        <label class="pgd-field-label">Nama Pelapor</label>
                        <input type="text" value="{{ auth()->user()->nama }}" readonly class="pgd-readonly-input">
                    </div>
                @endif
            </div>

            {{-- Section: Informasi Fasilitas --}}
            <div>
                <div class="pgd-section-title">
                    <i class="fa-solid fa-laptop-medical"></i>
                    <span>Informasi Fasilitas</span>
                </div>

                @if ($isQr)
                    {{-- QR Mode: locked fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- 1. Lokasi Lab --}}
                        <div>
                            <label class="pgd-field-label">Lokasi Lab</label>
                            <input type="text" value="{{ $selectedFacility?->laboratorium ? ($selectedFacility->laboratorium->nama_laboratorium . ($selectedFacility->laboratorium->lokasi ? ' - ' . $selectedFacility->laboratorium->lokasi : '')) : '-' }}" readonly class="pgd-readonly-input">
                        </div>

                        {{-- 2. Kategori Barang --}}
                        <div>
                            <label class="pgd-field-label">Kategori Barang</label>
                            <input type="text" value="{{ $selectedFacility?->kategori?->nama_kategori ?? '-' }}" readonly class="pgd-readonly-input">
                        </div>

                        {{-- 3. Kode Barang --}}
                        <div>
                            <label class="pgd-field-label">Kode Barang</label>
                            <input type="text" value="{{ $selectedFacility?->no_fasilitas ?? '-' }}" readonly class="pgd-readonly-input">
                        </div>
                    </div>
                @else
                    {{-- Manual Mode: cascading dropdowns --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                        <li data-value="all">Semua Lab</li>
                                        @foreach ($labs as $lab)
                                            <li data-value="{{ $lab->id_laboratorium }}">{{ $lab->nama_laboratorium }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <input type="hidden" id="id_laboratorium_filter" value="">
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
                                <input type="hidden" name="id_fasilitas" id="id_fasilitas" value="{{ $selectedFacilityId }}" required>
                            </div>
                        </div>


                    </div>
                @endif
            </div>

            {{-- Section: Detail Kerusakan --}}
            <div>
                <div class="pgd-section-title">
                    <i class="fa-solid fa-file-pen"></i>
                    <span>Detail Kerusakan</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="pgd-field-label">
                            Deskripsi Kerusakan <span class="required-star">*</span>
                        </label>

                        <textarea
                            name="deskripsi_kerusakan"
                            rows="4"
                            required
                            placeholder="Contoh: Monitor tidak menyala sama sekali sejak pagi ini."
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-silapor-500 focus:border-silapor-500 transition-all"
                        >{{ old('deskripsi_kerusakan') }}</textarea>
                    </div>

                    <div>
                        <label class="pgd-field-label">Foto Kerusakan</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center bg-[#F8FAFC] cursor-pointer hover:border-silapor-500 transition-colors">
                            <input type="file" name="foto_kerusakan" accept="image/*" capture="environment" class="hidden" id="fileInput">
                            <label for="fileInput" class="flex flex-col items-center cursor-pointer">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-silapor-500 mb-1"></i>
                                <span id="fileLabel" class="text-silapor-500 font-bold text-sm">Upload foto</span>
                                <span class="text-xs text-gray-400 mt-1">JPG/PNG/WEBP, maksimal 4 MB</span>
                            </label>
                            <div id="imagePreview" class="mt-3 hidden">
                                <img id="previewImg" src="" alt="Preview" class="max-h-32 mx-auto rounded-lg shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-silapor-500 hover:bg-silapor-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-xl py-3 shadow-sm transition-all text-sm"
            >
                <i class="fa-solid fa-paper-plane mr-2"></i>
                Kirim Pengaduan {{ $isQr ? 'QR' : 'Manual' }}
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-gray-100 flex flex-col sm:flex-row gap-3 text-center text-sm">
            @if ($backUrl)
                <a href="{{ $backUrl }}" class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-gray-600 font-semibold transition hover:bg-gray-50">
                    {{ $backLabel }}
                </a>
            @endif

            @if ($isQr)
                <a href="{{ auth()->check() && auth()->user()?->isAsisten() ? route('pengaduan.index') : route('pengaduan.manual.create') }}" class="flex-1 rounded-xl bg-sky-50 px-4 py-2.5 text-silapor-500 font-semibold transition hover:bg-sky-100">
                    Buat pengaduan manual
                </a>
            @else
                <a href="{{ route('scan.index') }}" class="flex-1 rounded-xl bg-sky-50 px-4 py-2.5 text-silapor-500 font-semibold transition hover:bg-sky-100">
                    Gunakan scan QR
                </a>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    // ==========================================
    // Searchable Select Component (same as index)
    // ==========================================
    function initSearchableSelect(wrapperId, config) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return null;

        const trigger = wrapper.querySelector('.pgd-select-trigger');
        const dropdown = wrapper.querySelector('.pgd-select-dropdown');
        const searchInput = wrapper.querySelector('.pgd-select-search');
        const optionsList = wrapper.querySelector('.pgd-select-options');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');

        let selectedValue = hiddenInput ? hiddenInput.value : '';

        function open() {
            wrapper.classList.add('open');
            trigger.classList.add('active');
            if (searchInput) { searchInput.value = ''; searchInput.focus(); }
            filterOptions('');
        }

        function close() {
            wrapper.classList.remove('open');
            trigger.classList.remove('active');
        }

        function isOpen() { return wrapper.classList.contains('open'); }

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

            let emptyMsg = optionsList.querySelector('.empty-msg');
            if (visibleCount === 0 && items.length > 0) {
                if (!emptyMsg) { emptyMsg = document.createElement('li'); emptyMsg.className = 'empty-msg'; optionsList.appendChild(emptyMsg); }
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
            optionsList.querySelectorAll('li').forEach(function (li) { li.classList.remove('selected'); });
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
                if (item.sublabel) li.innerHTML += ' <span style="color:#94A3B8"> — ' + item.sublabel + '</span>';
                optionsList.appendChild(li);
            });
        }

        // Event Listeners
        trigger.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            if (isOpen()) { close(); } else {
                document.querySelectorAll('.pgd-select-wrapper.open').forEach(function (w) { if (w !== wrapper) w.classList.remove('open'); });
                open();
            }
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () { filterOptions(this.value); });
            searchInput.addEventListener('click', function (e) { e.stopPropagation(); });
        }

        optionsList.addEventListener('click', function (e) {
            const li = e.target.closest('li');
            if (!li || li.classList.contains('empty-msg')) return;
            e.stopPropagation();
            selectValue(li.getAttribute('data-value'), li.textContent.trim());
        });

        dropdown.addEventListener('click', function (e) { e.stopPropagation(); });

        // If there's an initial value, set trigger text
        if (selectedValue) {
            const initial = optionsList.querySelector('[data-value="' + selectedValue + '"]');
            if (initial) {
                trigger.textContent = initial.textContent.trim();
                trigger.classList.add('has-value');
            }
        }

        return { reset, setOptions, selectValue, getValue: function () { return selectedValue; } };
    }

    // Close all on outside click
    document.addEventListener('click', function () {
        document.querySelectorAll('.pgd-select-wrapper.open').forEach(function (w) {
            w.classList.remove('open');
            w.querySelector('.pgd-select-trigger')?.classList.remove('active');
        });
    });

    // ==========================================
    // Init pelapor select (guest only)
    // ==========================================
    initSearchableSelect('pelapor-wrapper', {
        placeholder: '— Pilih nama user terdaftar —',
    });

    // ==========================================
    // Cascading Fasilitas Logic (manual mode only)
    // ==========================================
    const isQrMode = @json($isQr);

    if (!isQrMode) {
        const apiFasilitasUrl = @json($apiUrl);

        async function fetchFasilitas(params) {
            const url = new URL(apiFasilitasUrl, window.location.origin);
            Object.keys(params).forEach(function (key) {
                if (params[key]) url.searchParams.set(key, params[key]);
            });
            try {
                const response = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error('Network error');
                return await response.json();
            } catch (err) {
                console.error('Failed to fetch fasilitas:', err);
                return [];
            }
        }

        let cachedFasilitas = [];

        const lokasiLabSelect = initSearchableSelect('lokasi-lab-wrapper', {
            placeholder: 'Pilih lokasi lab',
            onChange: async function (value) {
                kategoriSelect.reset('Pilih kategori barang');
                kodeBarangSelect.reset('Pilih atau cari kode barang');
                document.getElementById('id_fasilitas').value = '';

                const params = {};
                if (value && value !== 'all') {
                    params.id_laboratorium = value;
                }
                cachedFasilitas = await fetchFasilitas(params);

                // Extract unique categories
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
                updateKodeBarangOptions(cachedFasilitas);
            }
        });

        const kategoriSelect = initSearchableSelect('kategori-wrapper', {
            placeholder: 'Pilih kategori barang',
            emptyMessage: 'Pilih lokasi lab terlebih dahulu',
            onChange: function (value) {
                kodeBarangSelect.reset('Pilih atau cari kode barang');
                document.getElementById('id_fasilitas').value = '';

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
                // No action needed for nama_fasilitas
            }
        });

        function updateKodeBarangOptions(items) {
            if (!items || items.length === 0) {
                kodeBarangSelect.setOptions([]);
                return;
            }
            kodeBarangSelect.setOptions(items.map(function (f) {
                return {
                    value: f.id,
                    label: f.no_fasilitas || '-',
                    sublabel: (f.nama_kategori || '-') + ' (' + f.nama_laboratorium + ')',
                };
            }));
        }
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
})();
</script>
@endsection