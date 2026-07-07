@extends('layouts.app')

@section('title', 'Kelola Laboratorium | SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.page-styles')

@php
    $user = auth()->user();
    $activeMenu = 'laboratorium';
    $isLaboran = $user?->role === 'laboran';
    $isKoordinator = $user?->role === 'koordinator_lab';
@endphp

@once
<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .lab-card { background:#fff; border:1px solid #E5E7EB; border-radius:2rem; box-shadow:0 15px 50px rgba(0,0,0,.05); overflow:hidden; }
    .lab-body { padding:1.5rem; }
    .form-control { width:100%; border:1px solid #D1D5DB; border-radius:.75rem; padding:.65rem .85rem; background:#fff; outline:none; font-size:.86rem; }
    .form-control:focus { border-color:#0090F5; box-shadow:0 0 0 3px rgba(0,144,245,.12); }
    .btn-mini { min-height:36px; border-radius:.7rem; padding:.5rem .85rem; font-size:.78rem; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:.4rem; cursor:pointer; border:0; transition:.18s ease; white-space:nowrap; }
    .btn-primary-mini { background:#0090F5; color:#fff; }
    .btn-primary-mini:hover { background:#007CD5; }
    .btn-outline-mini { background:#EEF8FF; color:#0090F5; border:1px solid #0090F5; }
    .btn-outline-mini:hover { background:#0090F5; color:#fff; }
    .btn-danger-mini { background:#FEE2E2; color:#DC2626; border:1px solid #FCA5A5; }
    .btn-danger-mini:hover { background:#DC2626; color:#fff; }
    .btn-danger-mini:disabled { opacity:.45; cursor:not-allowed; }
    .lab-form { display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)) auto; gap:.85rem; align-items:end; margin-bottom:1.25rem; padding:1rem; border:1px solid #E5E7EB; border-radius:1rem; background:#fff; }
    .lab-list { border:1px solid #E5E7EB; border-radius:1rem; overflow:hidden; background:#fff; }
    .lab-item { padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9; }
    .lab-item:last-child { border-bottom:0; }
    .lab-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .lab-title { margin:0; color:#111827; font-weight:800; }
    .lab-meta { margin:.22rem 0 0; color:#64748B; font-size:.84rem; }
    .lab-pj { margin:.22rem 0 0; color:#94A3B8; font-size:.78rem; }
    .pj-form { display:grid; grid-template-columns: 1fr 1fr auto; gap:.75rem; align-items:end; margin-top:.75rem; padding:1rem; border:1px solid #DCE6F1; border-radius:1rem; background:#F8FAFC; }
    .field-label { display:block; font-size:.72rem; font-weight:800; color:#64748B; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.35rem; }
    @media (max-width: 920px) {
        .lab-form { grid-template-columns:1fr; }
        .pj-form { grid-template-columns:1fr; }
        .lab-row { align-items:flex-start; flex-direction:column; }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['user' => $user, 'activeMenu' => $activeMenu])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6 overflow-x-hidden">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Laboratorium</h1>
            </div>
            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        <section class="lab-card">
            <div class="lab-body">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <h2 class="text-lg font-extrabold text-[#2C3E50] m-0">Data Laboratorium</h2>
                    @if(!$isKoordinator)
                    <a href="{{ route('fasilitas.index') }}" class="btn-mini btn-outline-mini">
                        <i class="fa-solid fa-qrcode"></i>Fasilitas
                    </a>
                    @endif
                </div>

                {{-- Form Tambah Lab: hanya untuk laboran --}}
                @if($isLaboran)
                <form method="POST" action="{{ route('laboratorium.store') }}" class="lab-form">
                    @csrf
                    <input name="nama_laboratorium" placeholder="Nama lab" required class="form-control">
                    <input name="kode_laboratorium" placeholder="Kode" class="form-control">
                    <input name="lokasi" placeholder="Lokasi" class="form-control">
                    <button class="btn-mini btn-primary-mini" type="submit">
                        <i class="fa-solid fa-plus"></i>Lab
                    </button>
                </form>
                @endif

                <div class="lab-list">
                    @forelse($laboratoriums as $lab)
                        <div class="lab-item">
                            <div class="lab-row">
                                <div>
                                    <p class="lab-title">
                                        {{ $lab->nama_laboratorium }}
                                        @if($lab->kode_laboratorium)
                                            <span class="text-xs text-gray-400 font-semibold">({{ $lab->kode_laboratorium }})</span>
                                        @endif
                                    </p>
                                    <p class="lab-meta">{{ $lab->lokasi ?? '—' }}</p>
                                    <p class="lab-pj">
                                        PJ: {{ $lab->penanggungJawabUser?->nama ?? 'Belum ditentukan' }}
                                        · Pendamping: {{ $lab->pendampingUser?->nama ?? 'Belum ditentukan' }}
                                        · {{ $lab->fasilitas_count }} fasilitas
                                    </p>
                                </div>

                                @if($isKoordinator)
                                <button type="button" class="btn-mini btn-outline-mini" onclick="document.getElementById('pj-form-{{ $lab->id_laboratorium }}').toggleAttribute('hidden')">
                                    <i class="fa-solid fa-pen"></i>Set PJ
                                </button>
                                @endif
                            </div>

                            {{-- Koordinator: form set PJ & Pendamping --}}
                            @if($isKoordinator)
                            <div id="pj-form-{{ $lab->id_laboratorium }}" hidden class="pj-form">
                                <form method="POST" action="{{ route('laboratorium.update', $lab) }}" style="display:contents;">
                                    @csrf
                                    @method('PATCH')
                                    <div class="relative custom-searchable-select">
                                        <label class="field-label">Penanggung Jawab</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                placeholder="— Pilih PJ —" 
                                                class="form-control searchable-select-trigger cursor-pointer bg-white"
                                                readonly
                                            >
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400" style="margin-top: 10px;">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </span>
                                        </div>
                                        <div class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50 hidden searchable-select-dropdown" style="max-height: 280px; overflow: hidden; width: 100%; min-width: 200px;">
                                            <div class="p-2 border-b border-gray-100">
                                                <input 
                                                    type="text" 
                                                    placeholder="Cari asisten..." 
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-silapor-500 searchable-select-search"
                                                >
                                            </div>
                                            <ul class="max-h-48 overflow-y-auto py-1 text-sm text-gray-700 searchable-select-options">
                                                <li data-value="" class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-gray-400">— Pilih PJ —</li>
                                                @foreach($asistenList as $asisten)
                                                    <li data-value="{{ $asisten->id_user }}" class="px-4 py-2 hover:bg-gray-50 cursor-pointer">
                                                        {{ $asisten->nama }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <input type="hidden" name="id_penanggung_jawab" value="{{ $lab->id_penanggung_jawab }}">
                                    </div>
                                    <div class="relative custom-searchable-select">
                                        <label class="field-label">Pendamping</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                placeholder="— Pilih Pendamping —" 
                                                class="form-control searchable-select-trigger cursor-pointer bg-white"
                                                readonly
                                            >
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400" style="margin-top: 10px;">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </span>
                                        </div>
                                        <div class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50 hidden searchable-select-dropdown" style="max-height: 280px; overflow: hidden; width: 100%; min-width: 200px;">
                                            <div class="p-2 border-b border-gray-100">
                                                <input 
                                                    type="text" 
                                                    placeholder="Cari asisten..." 
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-silapor-500 searchable-select-search"
                                                >
                                            </div>
                                            <ul class="max-h-48 overflow-y-auto py-1 text-sm text-gray-700 searchable-select-options">
                                                <li data-value="" class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-gray-400">— Pilih Pendamping —</li>
                                                @foreach($asistenList as $asisten)
                                                    <li data-value="{{ $asisten->id_user }}" class="px-4 py-2 hover:bg-gray-50 cursor-pointer">
                                                        {{ $asisten->nama }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <input type="hidden" name="id_pendamping" value="{{ $lab->id_pendamping }}">
                                    </div>
                                    <button type="submit" class="btn-mini btn-primary-mini">
                                        <i class="fa-solid fa-floppy-disk"></i>Simpan
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @empty
                        <p class="m-0 p-6 text-sm text-gray-400">Belum ada data laboratorium.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.custom-searchable-select').forEach(function (wrapper) {
            const trigger = wrapper.querySelector('.searchable-select-trigger');
            const dropdown = wrapper.querySelector('.searchable-select-dropdown');
            const searchInput = wrapper.querySelector('.searchable-select-search');
            const optionsList = wrapper.querySelector('.searchable-select-options');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const options = optionsList.querySelectorAll('li');

            // Set initial trigger text
            const initialValue = hiddenInput.value;
            const initialOption = Array.from(options).find(opt => opt.getAttribute('data-value') === initialValue);
            if (initialOption) {
                trigger.value = initialOption.textContent.trim();
            }

            // Click trigger to open dropdown
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

            // Filtering search inputs
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase();
                options.forEach(function (option) {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(query) ? '' : 'none';
                });
            });

            // Select an option
            options.forEach(function (option) {
                option.addEventListener('click', function () {
                    const val = option.getAttribute('data-value');
                    const text = option.textContent.trim();
                    hiddenInput.value = val;
                    trigger.value = val ? text : '';
                    dropdown.classList.add('hidden');
                });
            });
        });

        // Click outside closes dropdowns
        document.addEventListener('click', function () {
            document.querySelectorAll('.searchable-select-dropdown').forEach(d => d.classList.add('hidden'));
        });
    });
</script>
@endsection
