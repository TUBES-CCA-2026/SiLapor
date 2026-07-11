@extends('layouts.app')

@section('title', 'Fasilitas | SiLapor')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'fasilitas';
    $pageTitle = $pageTitle ?? strtoupper(str_replace('-', ' ', $activeMenu));

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $roleLabel = match($role) {
        'laboran' => 'Laboran',
        'koordinator_lab' => 'Koordinator Lab',
        'asisten' => 'Asisten Lab',
        default => 'User',
    };

    if ($role === 'laboran') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
            ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
            ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
            ['laboratorium', 'Laboratorium', 'fa-regular fa-building', $routeSafe('laboratorium.index')],
            ['fasilitas', 'Fasilitas & QR', 'fa-solid fa-qrcode', $routeSafe('fasilitas.index')],
            ['users', 'Kelola User', 'fa-solid fa-users-gear', $routeSafe('admin.users.index')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } elseif ($role === 'koordinator_lab') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
            ['penugasan', 'Penugasan', 'fa-solid fa-user-check', $routeSafe('penugasan.index')],
            ['detail-laporan', 'Detail Laporan', 'fa-regular fa-rectangle-list', $routeSafe('detail-laporan.index')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } elseif ($role === 'asisten') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['pengaduan', 'Pengaduan', 'fa-regular fa-file-lines', $routeSafe('pengaduan.index')],
            ['tindak-lanjut', 'Tindak Lanjut', 'fa-solid fa-screwdriver-wrench', $routeSafe('tindak-lanjut.index')],
            ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } else {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    }

    $groupedFasilitas = $fasilitas->groupBy('id_laboratorium');
@endphp

@once
<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-card { box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.03); }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #F1F5F9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
.dashboard-card,
    .page-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 2rem; box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); overflow: visible; }
    .page-card-body { padding: 1.5rem; }
    .section-title { margin: 0 0 1rem; font-size: 1.25rem; font-weight: 800; color: #2C3E50; }
    .table-wrap { width: 100%; max-width: 100%; overflow-x: auto; overflow-y: visible; background: #fff; }
    .report-table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .report-table thead { background: #F8FAFC; color: #64748B; text-transform: uppercase; font-size: .75rem; font-weight: 800; letter-spacing: .04em; }
    .report-table th, .report-table td { padding: 1rem 1.25rem; text-align: left; border-bottom: 1px solid #F1F5F9; white-space: nowrap; }
    .report-table td { font-size: .875rem; color: #374151; }
    .report-table tr:hover td { background: #F8FAFC; }
    .text-center { text-align: center !important; }
    .empty-state { padding: 2rem !important; text-align: center !important; color: #94A3B8 !important; }
    .detail-btn, .btn-outline-blue { border: 1px solid #0090F5; color: #0090F5; background: #EEF8FF; border-radius: .5rem; padding: .4rem .9rem; font-size: .8rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .detail-btn:hover, .btn-outline-blue:hover { background: #0090F5; color: #fff; }
    .btn-primary { background: #0090F5; color: #fff; border: 0; border-radius: .875rem; padding: .75rem 1rem; font-weight: 800; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
    .btn-primary:hover { background: #007CD5; }
    .btn-danger-soft { background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: .875rem; padding: .65rem 1rem; font-weight: 700; text-decoration: none; cursor: pointer; }
    .btn-cetak-qr {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 0.75rem;
        padding: 0.6rem 1.25rem;
        font-size: 0.82rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        min-height: 36px;
    }
    .btn-cetak-qr:hover {
        background: linear-gradient(135deg, #F87171 0%, #DC2626 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.35);
    }
    .btn-cetak-qr:active {
        transform: translateY(0);
        box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);
    }
    .qr-actions { display:flex; gap:.4rem; justify-content:center; flex-wrap:wrap; }
    .qr-action-btn { min-width:0; border-radius:.55rem; padding:.42rem .62rem; font-size:.72rem; font-weight:800; line-height:1; display:inline-flex; align-items:center; gap:.3rem; border:1px solid #CBD5E1; background:#fff; color:#334155; cursor:pointer; text-decoration:none; }
    .qr-action-btn:hover { border-color:#0090F5; color:#0090F5; background:#F0F9FF; }
    .qr-action-btn.danger { border-color:#FCA5A5; color:#DC2626; background:#FEF2F2; }
    .qr-action-btn.danger:hover { background:#DC2626; color:#fff; border-color:#DC2626; }
    .form-control { width: 100%; border: 1px solid #D1D5DB; border-radius: .875rem; padding: .75rem 1rem; background: #fff; outline: none; }
    .form-control:focus { border-color: #0090F5; box-shadow: 0 0 0 3px rgba(0, 144, 245, .14); }
    .field-label { display: block; margin-bottom: .45rem; font-size: .75rem; color: #6B7280; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .inline-form-grid { display: grid; grid-template-columns: 2fr 1.5fr 1.5fr 1.5fr auto; gap: 0.75rem; align-items: end; padding: 1.25rem; border: 1px solid #E5E7EB; border-radius: 1.25rem; background: #F8FAFC; }
    @media (max-width: 1024px) {
        .inline-form-grid { grid-template-columns: 1fr; align-items: stretch; }
    }
    .category-form-grid { display: grid; grid-template-columns: 1fr auto; gap: 0.75rem; align-items: end; padding: 1.25rem; border: 1px solid #E5E7EB; border-radius: 1.25rem; background: #F8FAFC; max-width: 500px; }
    @media (max-width: 640px) {
        .category-form-grid { grid-template-columns: 1fr; align-items: stretch; max-width: 100%; }
    }
    .info-box { padding: 1rem; background: #F8FAFC; border: 1px solid #E5E7EB; border-radius: 1rem; font-weight: 700; color: #374151; }
    .status-chip { display: inline-flex; align-items: center; gap: .35rem; padding: .35rem .7rem; border-radius: .5rem; font-size: .75rem; font-weight: 800; }
    .status-chip.progress { background: #FFD400; color: #7A6200; }
    .status-chip.done { background: #4DFF41; color: #128A2B; }
    .status-chip.new { background: #DCEEFF; color: #0D5D9C; }

    .laporan-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
    .laporan-title { margin-bottom: 0; }
    .laporan-search { width: min(280px, 100%); height: 42px; padding: 0 .9rem 0 1rem; display: flex; align-items: center; gap: .65rem; border: 1px solid #D8E1EC; border-radius: 999px; background: #fff; }
    .laporan-search input { width: 100%; min-width: 0; border: 0; outline: 0; background: transparent; color: #2C3E50; font-size: .875rem; }
    .laporan-search input::placeholder { color: #9AA9BA; }
    .laporan-search svg { width: 22px; height: 22px; fill: none; stroke: #52657A; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; flex: 0 0 auto; }
    .laporan-table-wrap { border-radius: 1.5rem; }
    .laporan-table { min-width: 1180px; }
    .laporan-description { max-width: 230px; overflow: hidden; text-overflow: ellipsis; }
    .laporan-status { min-width: 116px; height: 28px; padding: 0 9px 0 12px; display: inline-flex; align-items: center; justify-content: space-between; gap: 8px; border-radius: 7px; font-size: 13px; font-weight: 700; line-height: 1; white-space: nowrap; }
    .laporan-status.progress { color: #756000; background: #FFD400; }
    .laporan-status.done { color: #187C28; background: #59FF45; }
    .laporan-status.new { color: #095E9C; background: #D8ECFF; }
    .status-arrow { font-size: 12px; opacity: .75; }

    .modal-backdrop { position: fixed; inset: 0; z-index: 60; padding: 20px; background: rgba(15, 23, 42, .35); display: grid; place-items: center; }
    .modal-backdrop[hidden] { display: none !important; }
    .modal-card { width: min(520px, 96vw); max-height: 92vh; overflow: hidden; border-radius: 1.5rem; background: #fff; box-shadow: 0 18px 35px rgba(0,0,0,.18); }
    .modal-header { height: 58px; padding: 0 20px; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; }
    .modal-header h2 { margin: 0; color: #404040; font-size: 16px; font-weight: 800; }
    .modal-close { border: 0; background: transparent; color: #4a4a4a; font-size: 38px; font-weight: 800; line-height: 1; cursor: pointer; padding: 0; }
    .modal-body { padding: 34px 32px 36px; overflow-y: auto; max-height: calc(92vh - 58px); }
    .detail-photo-wrap { display: grid; gap: 10px; width: min(100%, 280px); max-height: 360px; margin: 0 auto 20px; border: 1px solid #E5E7EB; border-radius: 18px; overflow-y: auto; background: #f1f1f1; padding: 8px; }
    .modal-photo { width: 100%; height: 160px; display: block; object-fit: cover; border-radius: 12px; }
    .modal-photo-placeholder { width: 100%; min-height: 160px; display: grid; place-items: center; color: #777; font-size: 13px; font-weight: 700; }
    .detail-panel { width: min(100%, 420px); margin: 0 auto; border: 1px solid #E5E7EB; border-radius: 20px; overflow: hidden; background: #f7f7f7; }
    .modal-row { min-height: 38px; display: grid; grid-template-columns: 96px 12px 1fr; align-items: center; padding: 0 16px; background: #f0f0f0; color: #555; font-size: 14px; }
    .modal-row:nth-child(even) { background: #e8e8e8; }
    .status-badge { display: inline-block; padding: 2px 8px; border-radius: 5px; font-size: 11px; line-height: 1.25; color: #6b5700; background: #ffe03d; }
    .status-badge.new { color: #0b5b9c; background: #d8ecff; }
    .status-badge.done { color: #0f7433; background: #d9f7e3; }
    .status-badge.progress { color: #6b5700; background: #ffe03d; }
    .modal-row-description { min-height: 116px; align-items: start; padding-top: 14px; padding-bottom: 14px; }
    .description-box { min-height: 76px; padding: 14px; border: 1px solid #E5E7EB; border-radius: 18px; background: #fff; color: #555; line-height: 1.45; white-space: pre-wrap; }
    .loading-line { height: 13px; margin: 13px 0; border-radius: 30px; background: linear-gradient(90deg, #edf2f7, #f8fbff, #edf2f7); }
    .loading-line.short { width: 60%; }

    .btn-secondary-light { border: 1px solid #D8E1EC; color: #334155; background: #F8FAFC; border-radius: .875rem; padding: .75rem 1rem; font-weight: 800; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: .45rem; }
    .btn-secondary-light:hover { border-color: #0090F5; color: #0090F5; background: #EEF8FF; }
    .import-fasilitas-card { margin-bottom: 1.5rem; padding: 1.25rem; border: 1px solid #E5E7EB; border-radius: 1.5rem; background: #F8FAFC; display: grid; grid-template-columns: 1.2fr 1fr auto; gap: 1rem; align-items: end; }
    .import-fasilitas-title { margin: 0; color: #1F2937; font-size: .95rem; font-weight: 800; }
    .import-fasilitas-help { margin: .25rem 0 0; color: #64748B; font-size: .78rem; line-height: 1.5; }
    .import-fasilitas-card .form-control { height: 44px; padding: .62rem .85rem; border-radius: .75rem; font-size: .84rem; background: #fff; }
    .import-fasilitas-card .btn-primary { height: 44px; padding: 0 1.25rem; border-radius: .75rem; font-size: .84rem; white-space: nowrap; }
    .import-result-box { margin-bottom: 1.25rem; padding: .9rem 1rem; border: 1px solid #FBBF24; border-radius: 1rem; background: #FFFBEB; color: #92400E; font-size: .84rem; line-height: 1.5; }
    .import-result-box strong { display: block; margin-bottom: .35rem; font-weight: 800; }
    .import-result-box ul { margin: .35rem 0 0 1rem; padding: 0; list-style-type: disc; }
    .import-result-box li + li { margin-top: .25rem; }

    @media (max-width: 900px) {
        .import-fasilitas-card { grid-template-columns: 1fr; }
        .import-fasilitas-card .btn-primary { width: 100%; }
    }

    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['user' => auth()->user(), 'activeMenu' => 'fasilitas'])

<main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight uppercase">FASILITAS</h1>
        </div>

        @include('partials.user-welcome-box', ['user' => $user ?? auth()->user()])
    </header>

<section class="page-card">
    <div class="page-card-body">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div>
                <h2 class="section-title" style="margin-bottom: .35rem;">Fasilitas Lab & QR Code</h2>
                <p style="margin: 0; color: #64748B; font-size: .9rem;">Setiap fasilitas punya QR unik untuk pelaporan kerusakan.</p>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <a href="{{ route('fasilitas.import-template') }}" class="btn-secondary-light">
                    <i class="fa-solid fa-file-arrow-down"></i>Template CSV
                </a>
                <button type="button" onclick="printAllQrs()" class="btn-cetak-qr">
                    <i class="fa-solid fa-print"></i>Cetak Semua QR
                </button>
            </div>
        </div>

        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="import-result-box">
                <strong>Catatan import</strong>
                <ul>
                    @foreach(session('import_errors') as $importError)
                        <li>{{ $importError }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($errors->any())
            <div class="import-result-box">
                <strong>Data belum bisa diproses</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('fasilitas.import') }}" enctype="multipart/form-data" class="import-fasilitas-card">
            @csrf
            <div>
                <p class="import-fasilitas-title">Import Spreadsheet Fasilitas</p>
                 <p class="import-fasilitas-help">Gunakan file .xlsx atau .csv. Header yang didukung: kode_laboratorium, no_fasilitas, nama_fasilitas (opsional).</p>
            </div>
            <input type="file" name="spreadsheet" accept=".xlsx,.csv,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required class="form-control">
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-file-import" style="margin-right: .45rem;"></i>Import
            </button>
        </form>
        <div style="display: grid; gap: 1rem; margin-bottom: 2rem;">
            <!-- Form Tambah Fasilitas -->
            <form method="POST" action="{{ route('fasilitas.store') }}" class="inline-form-grid">
                @csrf

                <div>
                    <label class="field-label">Laboratorium</label>
                    <select name="id_laboratorium" required class="form-control" style="height: 42px; padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                        <option value="" disabled selected>Pilih Laboratorium</option>
                        @foreach ($laboratoriums as $lab)
                            <option value="{{ $lab->id_laboratorium }}">{{ $lab->nama_laboratorium }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Kategori</label>
                    <select name="id_kategori" class="form-control" style="height: 42px; padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                        <option value="" selected>Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id_kategori }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Kode Aset</label>
                    <input name="no_fasilitas" placeholder="Kode aset" class="form-control" style="height: 42px; padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                </div>
                <div>
                    <button class="btn-primary" style="height: 42px; padding: 0 1.25rem; font-size: 0.85rem; font-weight: 800; white-space: nowrap;">
                        ADD
                    </button>
                </div>
            </form>

            <!-- Form Tambah Kategori -->
            <form method="POST" action="{{ route('fasilitas.kategori.store') }}" class="category-form-grid">
                @csrf
                <div>
                    <label class="field-label">Nama Kategori Baru</label>
                    <input name="nama_kategori" placeholder="Nama kategori (misal: Komputer, Kursi)" required class="form-control" style="height: 42px; padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                </div>
                <div>
                    <button class="btn-primary" style="height: 42px; padding: 0 1.25rem; font-size: 0.85rem; font-weight: 800; background: #F59E0B; border: none; white-space: nowrap;">
                        ADD
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            @foreach ($laboratoriums as $lab)
                @php
                    $labFasilitas = $groupedFasilitas->get($lab->id_laboratorium) ?? collect();
                    $activeFasilitas = $labFasilitas->filter(fn($f) => !$f->qr_deleted_at && !blank($f->qr_code));
                @endphp
                <div class="border border-gray-200 rounded-[24px] bg-white overflow-hidden shadow-sm">
                    <!-- Lab Header -->
                    <div onclick="toggleLabFacilities('{{ $lab->id_laboratorium }}')" class="flex items-center justify-between px-6 py-4 bg-gray-50/50 hover:bg-gray-50 cursor-pointer transition-all">
                        <div>
                            <h3 class="font-extrabold text-[#2C3E50] text-base flex items-center gap-2">
                                <i class="fa-regular fa-building text-gray-400"></i>
                                {{ $lab->nama_laboratorium }}
                                @if($lab->kode_laboratorium)
                                    <span class="text-xs font-semibold text-gray-400">({{ $lab->kode_laboratorium }})</span>
                                @endif
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 font-semibold">
                                {{ $activeFasilitas->count() }} Fasilitas Aktif
                            </p>
                        </div>
                        <span id="chevron-{{ $lab->id_laboratorium }}" class="text-gray-400 transition-transform duration-200">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>
                    </div>

                    <!-- Lab Facilities (Collapsible) -->
                    <div id="lab-facilities-{{ $lab->id_laboratorium }}" class="hidden p-6 border-t border-gray-100 bg-white">
                        @if($activeFasilitas->isEmpty())
                            <p class="text-sm text-gray-400 text-center py-4">Belum ada fasilitas di laboratorium ini.</p>
                        @else
                            @php
                                $facilitiesByCategory = $activeFasilitas->groupBy(function($item) {
                                    return $item->kategori?->nama_kategori ?? 'Tanpa Kategori';
                                });
                            @endphp
                            <div class="space-y-6">
                                @foreach($facilitiesByCategory as $categoryName => $catItems)
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 border-b pb-1">
                                            {{ $categoryName }} ({{ $catItems->count() }})
                                        </h4>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;">
                                            @foreach($catItems as $f)
                                                <div id="fasilitas-card-{{ $f->id_fasilitas }}" style="background: #fff; border: {{ session('new_fasilitas_id') == $f->id_fasilitas ? '2px solid #0090F5' : '1px solid #E5E7EB' }}; border-radius: 1.25rem; padding: 1.25rem; text-align: center;">
                                                     <p style="margin: 0; font-weight: 800; color: #2C3E50;">{{ $f->no_fasilitas ?: 'Tanpa Kode Aset' }}</p>
                                                     
                                                     <div class="qr-print-area" data-fasilitas-category="{{ $f->kategori->nama_kategori ?? 'Tanpa Kategori' }}" data-fasilitas-code="{{ $f->no_fasilitas ?: '-' }}" data-fasilitas-lab="{{ $f->laboratorium->nama_laboratorium ?? '-' }}" data-fasilitas-url="{{ $f->scanUrl() }}" style="display: flex; justify-content: center; margin-bottom: 1rem; min-height: 140px; align-items: center;" id="qr-{{ $f->id_fasilitas }}"></div>
                                                    
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function () {
                                                            const target = document.getElementById('qr-{{ $f->id_fasilitas }}');
                                                            if (!target) return;
                                                            target.innerHTML = '';
                                                            if (window.QRCode) {
                                                                new QRCode(target, {
                                                                    text: @json($f->scanUrl()),
                                                                    width: 168,
                                                                    height: 168,
                                                                    correctLevel: QRCode.CorrectLevel.H
                                                                });
                                                            } else {
                                                                target.innerHTML = '<p style="font-size:.75rem;color:#64748B;word-break:break-all;">' + @json($f->scanUrl()) + '</p>';
                                                            }
                                                        });
                                                    </script>
                                                    <div class="qr-actions">
                                                        <form method="POST" action="{{ route('fasilitas.regenerate-qr', $f->id_fasilitas) }}">
                                                            @csrf
                                                            <button class="qr-action-btn" type="submit" title="Regenerasi QR"><i class="fa-solid fa-rotate"></i><span>QR Baru</span></button>
                                                        </form>
                                                        <button type="button" class="qr-action-btn" onclick="printQr('qr-{{ $f->id_fasilitas }}')" title="Cetak QR"><i class="fa-solid fa-print"></i><span>Cetak</span></button>
                                                        <form method="POST" action="{{ route('fasilitas.delete-qr', $f->id_fasilitas) }}" data-confirm-delete data-confirm-title="Hapus/nonaktifkan QR code fasilitas ini?" data-confirm-text="QR fasilitas ini akan dinonaktifkan dan tidak bisa digunakan untuk pelaporan.">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="qr-action-btn danger" type="submit" title="Hapus QR"><i class="fa-solid fa-trash"></i><span>Hapus</span></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

</main>
</div>

<script>

    function printAllQrs() {
        const qrAreas = document.querySelectorAll('.qr-print-area');
        if (!qrAreas.length) {
            alert('Belum ada QR Code untuk dicetak.');
            return;
        }

        const printWindow = window.open('', '_blank', 'width=800,height=600');
        if (!printWindow) return;

        let cardsHtml = '';
        qrAreas.forEach(area => {
            const category = area.dataset.fasilitasCategory || 'Tanpa Kategori';
            const code = area.dataset.fasilitasCode || '-';
            const lab = area.dataset.fasilitasLab || '-';
            const url = area.dataset.fasilitasUrl || '';
            const qrHtml = area.innerHTML;
            
            cardsHtml += `
                <div class="card">
                    <div class="brand">SiLapor</div>
                    <div class="qr">${qrHtml}</div>
                    <div class="code" style="font-weight:800; color:#111827; margin: 8px 0 2px; font-size: 14px;">${code}</div>
                    <div class="category" style="color:#4B5563; font-weight:600; font-size: 12px; margin-bottom: 4px;">${category}</div>
                    <div class="lab" style="color:#64748B; font-size: 11px; margin-bottom: 8px;">${lab}</div>
                    <div class="url">${url}</div>
                </div>
            `;
        });

        printWindow.document.write(`
            <html><head><title>Cetak Semua QR</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
                .card { width: 220px; border: 1px solid #E5E7EB; border-radius: 14px; padding: 16px; text-align: center; page-break-inside: avoid; }
                .brand { color:#0090F5; font-weight:800; font-size:18px; margin-bottom: 8px; }
                .name { font-weight:800; color:#111827; margin: 8px 0 4px; font-size: 14px; }
                .lab { color:#64748B; font-size: 11px; margin-bottom: 8px; }
                .qr { display:flex; justify-content:center; margin: 8px 0; }
                .qr img { width: 120px !important; height: 120px !important; }
                .url { word-break: break-all; color:#64748B; font-size: 9px; }
                @media print {
                    body { margin: 0; }
                    .card { page-break-inside: avoid; }
                }
            </style></head><body>
                ${cardsHtml}
            </body></html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => printWindow.print(), 350);
    }

    function printQr(id) {
        const target = document.getElementById(id);
        if (!target) return;

        const category = target.dataset.fasilitasCategory || 'Tanpa Kategori';
        const code = target.dataset.fasilitasCode || '-';
        const lab = target.dataset.fasilitasLab || '-';
        const url = target.dataset.fasilitasUrl || '';
        const qrHtml = target.innerHTML;
        const printWindow = window.open('', '_blank', 'width=420,height=560');
        if (!printWindow) return;

        printWindow.document.write(`
            <html><head><title>Cetak QR ${code}</title>
            <style>
                body { font-family: Arial, sans-serif; display: grid; place-items: center; min-height: 100vh; margin: 0; }
                .card { width: 320px; border: 1px solid #E5E7EB; border-radius: 18px; padding: 24px; text-align: center; }
                .brand { color:#0090F5; font-weight:800; font-size:22px; margin-bottom: 12px; }
                .code { font-weight:800; color:#111827; margin: 12px 0 2px; font-size: 18px; }
                .category { color:#4B5563; font-weight:600; font-size: 14px; margin-bottom: 6px; }
                .lab { color:#64748B; font-size: 13px; margin-bottom: 12px; }
                .qr { display:flex; justify-content:center; margin: 16px 0; }
                .url { word-break: break-all; color:#64748B; font-size: 11px; }
            </style></head><body>
                <div class="card">
                    <div class="brand">SiLapor</div>
                    <div class="qr">${qrHtml}</div>
                    <div class="code">${code}</div>
                    <div class="category">${category}</div>
                    <div class="lab">${lab}</div>
                    <div class="url">${url}</div>
                </div>
            </body></html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => printWindow.print(), 350);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const newId = @json(session('new_fasilitas_id'));
        if (newId) {
            const card = document.getElementById('fasilitas-card-' + newId);
            if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    function handleResponsiveSidebar() {
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

    window.addEventListener('resize', handleResponsiveSidebar);
    window.addEventListener('load', handleResponsiveSidebar);

    (function () {
        const modal = document.getElementById('detailModal');
        const modalContent = document.getElementById('modalContent');

        if (!modal || !modalContent) return;

        function closeModal() {
            modal.hidden = true;
            modalContent.innerHTML = '';
        }

        function esc(value) {
            return String(value ?? '-')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderDetail(data) {
            const fotoItems = Array.isArray(data.fotos) && data.fotos.length
                ? data.fotos
                : (data.foto ? [{ url: data.foto }] : []);

            const foto = fotoItems.length
                ? fotoItems.map((item, index) => {
                    const url = typeof item === 'string' ? item : item.url;
                    return url
                        ? `<img src="${esc(url)}" alt="Foto kerusakan ${index + 1}" class="modal-photo" loading="lazy">`
                        : '';
                }).join('')
                : `<div class="modal-photo-placeholder">Tidak ada foto</div>`;

            const statusClass = esc(data.statusClass || 'new');
            const statusLabel = esc(data.statusLabel || data.status);

            return `
                <div class="detail-photo-wrap">${foto}</div>
                <div class="detail-panel">
                    <div class="modal-row"><span class="modal-label">ID</span><span>:</span><span>${esc(data.id)}</span></div>
                    <div class="modal-row"><span class="modal-label">Status</span><span>:</span><span><mark class="status-badge ${statusClass}">${statusLabel}</mark></span></div>
                    <div class="modal-row"><span class="modal-label">Pelapor</span><span>:</span><span>${esc(data.pelapor)}</span></div>
                    <div class="modal-row"><span class="modal-label">Lokasi</span><span>:</span><span>${esc(data.lokasi)}</span></div>
                    <div class="modal-row"><span class="modal-label">Fasilitas</span><span>:</span><span>${esc(data.fasilitas)}</span></div>
                    <div class="modal-row"><span class="modal-label">Tgl Lapor</span><span>:</span><span>${esc(data.tanggal)}</span></div>
                    <div class="modal-row modal-row-description"><span class="modal-label">Deskripsi</span><span>:</span><div class="description-box">${esc(data.deskripsi)}</div></div>
                </div>
            `;
        }

        document.addEventListener('click', async function (event) {
            const detailButton = event.target.closest('.detail-btn');
            const closeButton = event.target.closest('[data-close-modal]');

            if (closeButton || event.target === modal) {
                closeModal();
                return;
            }

            if (!detailButton) return;

            const url = detailButton.dataset.detailUrl;
            modal.hidden = false;
            modalContent.innerHTML = '<div class="loading-line"></div><div class="loading-line short"></div><div class="loading-line"></div>';

            if (!url || url === '#') {
                modalContent.innerHTML = '<p>URL detail belum tersedia.</p>';
                return;
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Gagal mengambil detail laporan.');
                const data = await response.json();
                modalContent.innerHTML = renderDetail(data);
            } catch (error) {
                modalContent.innerHTML = '<p>Detail laporan belum bisa ditampilkan. Pastikan route detail pengaduan sudah benar.</p>';
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeModal();
        });
    })();

    (function () {
        const searchInput = document.querySelector('[data-laporan-search]');
        const table = document.querySelector('[data-laporan-table]');

        if (!searchInput || !table) return;

        const rows = Array.from(table.querySelectorAll('[data-laporan-row]'));
        const emptyRow = table.querySelector('[data-empty-row]');

        searchInput.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                const isMatch = text.includes(keyword);
                row.hidden = !isMatch;
                if (isMatch) visibleCount += 1;
            });

            if (emptyRow && rows.length > 0) {
                emptyRow.hidden = visibleCount !== 0;
            }
        });
    })();

    function toggleLabFacilities(labId) {
        const el = document.getElementById('lab-facilities-' + labId);
        const chevron = document.getElementById('chevron-' + labId);
        if (el) {
            const isHidden = el.classList.contains('hidden');
            if (isHidden) {
                el.classList.remove('hidden');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            } else {
                el.classList.add('hidden');
                if (chevron) chevron.style.transform = '';
            }
        }
    }
</script>

@endsection
