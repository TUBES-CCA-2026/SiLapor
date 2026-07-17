@extends('layouts.app')

@section('title', 'Rekapitulasi | SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@include('partials.page-styles')

@php
    $user = auth()->user();
    $activeMenu = 'rekapsulasi';
    $rows = $daftarLaporan ?? collect();
    $canExportRekap = $user?->role === 'laboran';
    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };
    $statusMeta = function ($status) {
        return match ($status) {
            'NEW' => ['label' => 'New', 'class' => 'new'],
            'HANDLED' => ['label' => 'On Progress', 'class' => 'progress'],
            'DONE' => ['label' => 'Done', 'class' => 'done'],
            'CANCEL' => ['label' => 'Cancel', 'class' => 'cancel'],
            'NO_SPAREPART' => ['label' => 'No Sparepart', 'class' => 'no-sparepart'],
            default => ['label' => $status ?: '-', 'class' => 'new'],
        };
    };
@endphp

@once
<style>
    .filter-card { background:#fff; border:1px solid #E5E7EB; border-radius:2rem; box-shadow:0 15px 50px rgba(0,0,0,.05); padding:1.5rem; }
    .table-card { background:#fff; border:1px solid #E5E7EB; border-radius:2rem; box-shadow:0 15px 50px rgba(0,0,0,.05); overflow:hidden; }
    .form-control { width:100%; border:1px solid #D1D5DB; border-radius:.875rem; padding:.75rem 1rem; background:#fff; outline:none; font-size:.875rem; }
    .form-control:focus { border-color:#29ABE2; box-shadow:0 0 0 3px rgba(41,171,226,.14); }
    .btn-primary { background:#29ABE2; color:#fff; border:0; border-radius:.875rem; padding:.75rem 1rem; font-weight:800; cursor:pointer; }
    .btn-secondary { background:#fff; color:#475569; border:1px solid #D1D5DB; border-radius:.875rem; padding:.75rem 1rem; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .btn-mini { min-height: 36px; border-radius: .7rem; padding: .48rem .8rem; font-size: .78rem; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: .4rem; cursor: pointer; line-height: 1; transition: .18s ease; }
    .btn-export-pdf { background:#fff; color:#DC2626; border:1px solid #FCA5A5; }
    .btn-export-pdf:hover { background:#DC2626; color:#fff; }
    .btn-export-excel { background:#fff; color:#16A34A; border:1px solid #86EFAC; }
    .btn-export-excel:hover { background:#16A34A; color:#fff; }
    .btn-template { background:#fff; color:#475569; border:1px solid #CBD5E1; }
    .btn-template:hover { background:#F8FAFC; border-color:#94A3B8; }
    .btn-import { background:#29ABE2; color:#fff; border:1px solid #29ABE2; }
    .btn-import:hover { background:#1B8DC4; }
    .import-form { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
    .import-file { max-width: 210px; min-height: 36px; padding: .42rem .7rem; border-radius: .7rem; font-size: .78rem; }
    .report-table { width:100%; border-collapse:collapse; min-width:980px; }
    .report-table thead { background:#F8FAFC; color:#64748B; text-transform:uppercase; font-size:.75rem; font-weight:800; letter-spacing:.04em; }
    .report-table th,.report-table td { padding:1rem 1.25rem; text-align:left; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
    .report-table td { font-size:.875rem; color:#374151; }
    .report-table tr:hover td { background:#F8FAFC; }
    .laporan-status { min-width:112px; height:28px; padding:0 10px; display:inline-flex; align-items:center; justify-content:center; border-radius:7px; font-size:12px; font-weight:800; white-space:nowrap; }
    .laporan-status.progress { color:#756000; background:#FFD400; }
    .laporan-status.done { color:#187C28; background:#59FF45; }
    .laporan-status.new { color:#095E9C; background:#D8ECFF; }
    .laporan-status.cancel { color:#B91C1C; background:#FEE2E2; }
    .laporan-status.no-sparepart { color:#374151; background:#E5E7EB; }
    .detail-btn { border:1px solid #29ABE2; color:#29ABE2; background:#E8F7FC; border-radius:.5rem; padding:.45rem 1rem; font-size:.8rem; font-weight:800; cursor:pointer; text-decoration:none; }
    .detail-btn:hover { background:#29ABE2; color:#fff; }

    /* Custom Searchable Dropdown styles */
    .custom-select-wrapper { position: relative; width: 100%; }
    .custom-select-trigger {
        display: flex; justify-content: space-between; align-items: center;
        cursor: pointer; background: #fff; height: 46px;
        padding: 0 1rem; font-size: 0.875rem; user-select: none;
        border: 1px solid #D1D5DB; border-radius: 0.875rem;
        transition: all 0.2s; color: #374151;
    }
    .custom-select-trigger:hover { border-color: #94A3B8; }
    .custom-select-trigger.active { border-color: #29ABE2; box-shadow: 0 0 0 3px rgba(41, 171, 226, 0.14); }
    .custom-select-trigger .selected-text.placeholder { color: #9CA3AF; }
    .custom-select-trigger .cs-chevron { transition: transform 0.25s ease; font-size: 0.7rem; color: #9CA3AF; }
    .custom-select-trigger.active .cs-chevron { transform: rotate(180deg); color: #29ABE2; }
    .custom-select-options-container {
        position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 99;
        background: #fff; border: 1px solid #E5E7EB; border-radius: 1rem;
        box-shadow: 0 12px 36px rgba(0,0,0,0.10); overflow: hidden;
        display: flex; flex-direction: column;
        opacity: 0; transform: translateY(-8px); pointer-events: none;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-select-options-container.show { opacity: 1; transform: translateY(0); pointer-events: auto; }
    .custom-select-search-box {
        padding: 10px; border-bottom: 1px solid #F1F5F9; background: #F8FAFC;
        display: flex; align-items: center; gap: 8px;
    }
    .custom-select-search-box input {
        width: 100%; border: 1px solid #E2E8F0; border-radius: 0.6rem;
        padding: 7px 10px; font-size: 0.82rem; outline: none; transition: border-color 0.2s;
    }
    .custom-select-search-box input:focus { border-color: #29ABE2; }
    .custom-select-options { max-height: 200px; overflow-y: auto; }
    .custom-select-option {
        padding: 10px 14px; font-size: 0.84rem; color: #374151;
        cursor: pointer; transition: all 0.15s;
    }
    .custom-select-option:hover { background: #E8F7FC; color: #29ABE2; padding-left: 18px; }
    .custom-select-option.selected { background: #E8F7FC; color: #1B8DC4; font-weight: 700; }
    .custom-select-option.placeholder-opt { color: #9CA3AF; font-style: italic; }
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
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Rekapitulasi</h1>
            </div>
            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        <section class="filter-card">
            <div class="mb-4">
                <h2 class="text-lg font-extrabold text-[#2C3E50]">Filter Laporan</h2>
            </div>
            <form action="{{ $routeSafe('rekapsulasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <input type="text" id="filter-tanggal" name="tanggal" value="{{ request('tanggal') }}" class="form-control" placeholder="dd/mm/yyyy">
                {{-- Dropdown Koordinator (searchable) --}}
                @php
                    $selPj = ($penanggungJawabs ?? collect())->firstWhere('id_user', request('id_penanggung_jawab'));
                @endphp
                <div class="custom-select-wrapper" id="cs-koordinator">
                    <input type="hidden" name="id_penanggung_jawab" value="{{ request('id_penanggung_jawab') }}">
                    <div class="custom-select-trigger" onclick="toggleCustomSelect('cs-koordinator')">
                        <span class="selected-text {{ $selPj ? '' : 'placeholder' }}">{{ $selPj ? $selPj->nama : 'Semua Koordinator' }}</span>
                        <i class="fa-solid fa-chevron-down cs-chevron"></i>
                    </div>
                    <div class="custom-select-options-container">
                        <div class="custom-select-search-box">
                            <i class="fa-solid fa-magnifying-glass text-xs text-gray-400"></i>
                            <input type="text" placeholder="Cari koordinator..." oninput="filterCustomSelectOptions(this, 'cs-koordinator')">
                        </div>
                        <div class="custom-select-options custom-scrollbar">
                            <div class="custom-select-option placeholder-opt {{ !request('id_penanggung_jawab') ? 'selected' : '' }}" data-value="" onclick="selectCustomOption(this, 'cs-koordinator')">Semua Koordinator</div>
                            @foreach(($penanggungJawabs ?? collect()) as $pj)
                                <div class="custom-select-option {{ (string) request('id_penanggung_jawab') === (string) $pj->id_user ? 'selected' : '' }}" data-value="{{ $pj->id_user }}" onclick="selectCustomOption(this, 'cs-koordinator')">{{ $pj->nama }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Dropdown Lokasi (searchable) --}}
                @php
                    $selLab = ($laboratoriums ?? collect())->firstWhere('id_laboratorium', request('id_laboratorium'));
                @endphp
                <div class="custom-select-wrapper" id="cs-lokasi">
                    <input type="hidden" name="id_laboratorium" value="{{ request('id_laboratorium') }}">
                    <div class="custom-select-trigger" onclick="toggleCustomSelect('cs-lokasi')">
                        <span class="selected-text {{ $selLab ? '' : 'placeholder' }}">{{ $selLab ? $selLab->nama_laboratorium : 'Semua Lokasi' }}</span>
                        <i class="fa-solid fa-chevron-down cs-chevron"></i>
                    </div>
                    <div class="custom-select-options-container">
                        <div class="custom-select-search-box">
                            <i class="fa-solid fa-magnifying-glass text-xs text-gray-400"></i>
                            <input type="text" placeholder="Cari lokasi..." oninput="filterCustomSelectOptions(this, 'cs-lokasi')">
                        </div>
                        <div class="custom-select-options custom-scrollbar">
                            <div class="custom-select-option placeholder-opt {{ !request('id_laboratorium') ? 'selected' : '' }}" data-value="" onclick="selectCustomOption(this, 'cs-lokasi')">Semua Lokasi</div>
                            @foreach(($laboratoriums ?? collect()) as $lab)
                                <div class="custom-select-option {{ (string) request('id_laboratorium') === (string) $lab->id_laboratorium ? 'selected' : '' }}" data-value="{{ $lab->id_laboratorium }}" onclick="selectCustomOption(this, 'cs-lokasi')">{{ $lab->nama_laboratorium }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari laporan..." class="form-control">

                {{-- Dropdown Urutan --}}
                @php $selSort = request('sort', 'terbaru'); @endphp
                <div class="custom-select-wrapper" id="cs-sort">
                    <input type="hidden" name="sort" value="{{ $selSort }}">
                    <div class="custom-select-trigger" onclick="toggleCustomSelect('cs-sort')">
                        <span class="selected-text">{{ $selSort === 'terlama' ? 'Terlama' : 'Terbaru' }}</span>
                        <i class="fa-solid fa-chevron-down cs-chevron"></i>
                    </div>
                    <div class="custom-select-options-container">
                        <div class="custom-select-options custom-scrollbar">
                            <div class="custom-select-option {{ $selSort === 'terbaru' ? 'selected' : '' }}" data-value="terbaru" onclick="selectCustomOption(this, 'cs-sort')">Terbaru</div>
                            <div class="custom-select-option {{ $selSort === 'terlama' ? 'selected' : '' }}" data-value="terlama" onclick="selectCustomOption(this, 'cs-sort')">Terlama</div>
                        </div>
                    </div>
                </div>

                {{-- Dropdown Status --}}
                @php
                    $selStatus = request('status');
                    $statusLabels = ['NEW' => 'New', 'HANDLED' => 'On Progress', 'DONE' => 'Done', 'CANCEL' => 'Cancel', 'NO_SPAREPART' => 'No Sparepart'];
                @endphp
                <div class="custom-select-wrapper" id="cs-status">
                    <input type="hidden" name="status" value="{{ $selStatus }}">
                    <div class="custom-select-trigger" onclick="toggleCustomSelect('cs-status')">
                        <span class="selected-text {{ $selStatus ? '' : 'placeholder' }}">{{ $selStatus ? ($statusLabels[$selStatus] ?? $selStatus) : 'Semua Status' }}</span>
                        <i class="fa-solid fa-chevron-down cs-chevron"></i>
                    </div>
                    <div class="custom-select-options-container">
                        <div class="custom-select-options custom-scrollbar">
                            <div class="custom-select-option placeholder-opt {{ !$selStatus ? 'selected' : '' }}" data-value="" onclick="selectCustomOption(this, 'cs-status')">Semua Status</div>
                            @foreach($statusLabels as $val => $lbl)
                                <div class="custom-select-option {{ $selStatus === $val ? 'selected' : '' }}" data-value="{{ $val }}" onclick="selectCustomOption(this, 'cs-status')">{{ $lbl }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Dropdown Fasilitas (searchable) --}}
                @php
                    $selFas = ($fasilitasList ?? collect())->firstWhere('id_fasilitas', request('id_fasilitas'));
                @endphp
                <div class="custom-select-wrapper" id="cs-fasilitas">
                    <input type="hidden" name="id_fasilitas" value="{{ request('id_fasilitas') }}">
                    <div class="custom-select-trigger" onclick="toggleCustomSelect('cs-fasilitas')">
                        <span class="selected-text {{ $selFas ? '' : 'placeholder' }}">{{ $selFas ? ($selFas->kategori?->nama_kategori ?? 'Tanpa Kategori') . ' (' . ($selFas->no_fasilitas ?? '-') . ')' : 'Semua Fasilitas' }}</span>
                        <i class="fa-solid fa-chevron-down cs-chevron"></i>
                    </div>
                    <div class="custom-select-options-container">
                        <div class="custom-select-search-box">
                            <i class="fa-solid fa-magnifying-glass text-xs text-gray-400"></i>
                            <input type="text" placeholder="Cari fasilitas..." oninput="filterCustomSelectOptions(this, 'cs-fasilitas')">
                        </div>
                        <div class="custom-select-options custom-scrollbar">
                            <div class="custom-select-option placeholder-opt {{ !request('id_fasilitas') ? 'selected' : '' }}" data-value="" onclick="selectCustomOption(this, 'cs-fasilitas')">Semua Fasilitas</div>
                            @foreach(($fasilitasList ?? collect()) as $fasilitas)
                                <div class="custom-select-option {{ (string) request('id_fasilitas') === (string) $fasilitas->id_fasilitas ? 'selected' : '' }}" data-value="{{ $fasilitas->id_fasilitas }}" onclick="selectCustomOption(this, 'cs-fasilitas')">{{ $fasilitas->kategori?->nama_kategori ?? 'Tanpa Kategori' }} ({{ $fasilitas->no_fasilitas ?? '-' }})</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1"><i class="fa-solid fa-filter mr-2"></i>Filter</button>
                    <a href="{{ $routeSafe('rekapsulasi.index') }}" class="btn-secondary"><i class="fa-solid fa-rotate-left mr-2"></i>Reset</a>
                </div>
            </form>
            <div class="mt-4 flex flex-col lg:flex-row justify-between gap-3">
                @if($canExportRekap)
                    <div class="flex gap-2 flex-wrap">
                        <a href="{{ route('rekapsulasi.export.pdf', request()->query()) }}" class="btn-mini btn-export-pdf">
                            <i class="fa-solid fa-print"></i>Cetak
                        </a>
                        <a href="{{ route('rekapsulasi.export.excel', request()->query()) }}" class="btn-mini btn-export-excel">
                            <i class="fa-solid fa-file-excel"></i>Excel
                        </a>
                        <a href="{{ route('rekapsulasi.import-template') }}" class="btn-mini btn-template">
                            <i class="fa-solid fa-download"></i>Template
                        </a>
                    </div>
                @endif
                @if(auth()->user()?->role === 'laboran')
                    <form action="{{ route('rekapsulasi.import') }}" method="POST" enctype="multipart/form-data" class="import-form">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt,.xls,.xlsx" class="form-control import-file">
                        <button type="submit" class="btn-mini btn-import">
                            <i class="fa-solid fa-file-import"></i>Import
                        </button>
                    </form>
                @endif
            </div>
        </section>

        <section class="table-card">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-extrabold text-[#2C3E50]">Daftar Hasil Rekapitulasi</h2>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pelapor</th>
                            <th>Lokasi Masalah</th>
                            <th>Fasilitas</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $item)
                            @php
                                $status = $statusMeta($item->status_pengaduan ?? null);
                                $detailUrl = route('dashboard.pengaduan.detail', $item);
                            @endphp
                            <tr>
                                <td>{{ $item->tanggal_lapor ? \Carbon\Carbon::parse($item->tanggal_lapor)->format('d/m/Y') : '-' }}</td>
                                <td>{{ data_get($item, 'pelapor.nama', 'Guest') }}</td>
                                <td>{{ data_get($item, 'fasilitas.laboratorium.nama_laboratorium', '-') }}</td>
                                <td>{{ data_get($item, 'fasilitas.kategori.nama_kategori', '-') }} ({{ data_get($item, 'fasilitas.no_fasilitas', '-') }})</td>
                                <td><span class="laporan-status {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                <td class="text-center"><button type="button" class="detail-btn" data-detail-url="{{ $detailUrl }}">Detail</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 text-center text-gray-400">Belum ada data rekapitulasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($rows, 'links'))
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $rows->withQueryString()->links() }}
                </div>
            @endif
        </section>
        @include('partials.detail-modal')
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

    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#filter-tanggal", {
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });

    /* ── Custom Select Dropdown Functions ── */

    function toggleCustomSelect(wrapperId) {
        // Close all other custom selects first
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            if (wrapper.id !== wrapperId) {
                const container = wrapper.querySelector('.custom-select-options-container');
                const trigger = wrapper.querySelector('.custom-select-trigger');
                if (container) container.classList.remove('show');
                if (trigger) trigger.classList.remove('active');
            }
        });

        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const container = wrapper.querySelector('.custom-select-options-container');
        const trigger = wrapper.querySelector('.custom-select-trigger');

        if (container) {
            container.classList.toggle('show');
            if (container.classList.contains('show')) {
                const searchInput = container.querySelector('.custom-select-search-box input');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                    filterCustomSelectOptions(searchInput, wrapperId);
                }
            }
        }
        if (trigger) {
            trigger.classList.toggle('active');
        }
    }

    function filterCustomSelectOptions(input, wrapperId) {
        const query = input.value.trim().toLowerCase();
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        wrapper.querySelectorAll('.custom-select-option').forEach(opt => {
            if (opt.classList.contains('placeholder-opt')) return;
            const text = opt.textContent.trim().toLowerCase();
            opt.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function selectCustomOption(optionEl, wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const value = optionEl.dataset.value;
        const text = optionEl.textContent.trim();
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const selectedTextSpan = wrapper.querySelector('.selected-text');

        if (hiddenInput) hiddenInput.value = value;

        if (selectedTextSpan) {
            selectedTextSpan.textContent = text;
            if (value === '') {
                selectedTextSpan.classList.add('placeholder');
            } else {
                selectedTextSpan.classList.remove('placeholder');
            }
        }

        // Highlight selected option
        wrapper.querySelectorAll('.custom-select-option').forEach(opt => opt.classList.remove('selected'));
        optionEl.classList.add('selected');

        // Close dropdown
        const container = wrapper.querySelector('.custom-select-options-container');
        const trigger = wrapper.querySelector('.custom-select-trigger');
        if (container) container.classList.remove('show');
        if (trigger) trigger.classList.remove('active');
    }

    // Close select dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.custom-select-wrapper')) {
            document.querySelectorAll('.custom-select-options-container').forEach(c => c.classList.remove('show'));
            document.querySelectorAll('.custom-select-trigger').forEach(t => t.classList.remove('active'));
        }
    });
</script>
@endsection
