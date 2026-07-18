@extends('layouts.app')

@section('title', 'Penugasan | SiLapor')

@section('content')
@php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'penugasan';
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
    .detail-btn, .btn-outline-blue { border: 1px solid #29ABE2; color: #29ABE2; background: #E8F7FC; border-radius: .5rem; padding: .4rem .9rem; font-size: .8rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .detail-btn:hover, .btn-outline-blue:hover { background: #29ABE2; color: #fff; }
    .btn-primary { background: #29ABE2; color: #fff; border: 0; border-radius: .875rem; padding: .75rem 1rem; font-weight: 800; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
    .btn-primary:hover { background: #007CD5; }
    .btn-danger-soft { background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: .875rem; padding: .65rem 1rem; font-weight: 700; text-decoration: none; cursor: pointer; }
    .danger-btn { border: 1px solid #FCA5A5; color: #DC2626; background: #FEF2F2; border-radius: .5rem; padding: .4rem .9rem; font-size: .8rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: .35rem; transition: .18s ease; }
    .danger-btn:hover { background: #DC2626; color: #fff; border-color: #DC2626; }
    .form-control { width: 100%; border: 1px solid #D1D5DB; border-radius: .875rem; padding: .75rem 1rem; background: #fff; outline: none; }
    .form-control:focus { border-color: #29ABE2; box-shadow: 0 0 0 3px rgba(41, 171, 226, .14); }
    .field-label { display: block; margin-bottom: .45rem; font-size: .75rem; color: #6B7280; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .info-box { padding: 1rem; background: #F8FAFC; border: 1px solid #E5E7EB; border-radius: 1rem; font-weight: 700; color: #374151; }
    .status-chip { display: inline-flex; align-items: center; gap: .35rem; padding: .35rem .7rem; border-radius: .5rem; font-size: .75rem; font-weight: 800; }
    .status-chip.progress { background: #FFD400; color: #7A6200; }
    .status-chip.done { background: #4DFF41; color: #128A2B; }
    .status-chip.new { background: #DCEEFF; color: #0D5D9C; }

    .laporan-toolbar { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
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

    @media (min-width: 768px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }

    .penugasan-page { overflow: visible !important; }
    .penugasan-table-wrap { max-width: 100%; overflow-x: auto; overflow-y: visible; }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['user' => auth()->user(), 'activeMenu' => 'penugasan'])

<main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop md:hidden">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight uppercase">PENUGASAN</h1>
        </div>

        @include('partials.user-welcome-box', ['user' => $user ?? auth()->user()])
    </header>

@php
    $rows = isset($pengaduanList) ? $pengaduanList : collect();
    $teknisiList = isset($asisten) ? $asisten : collect();

    $statusMeta = function ($status) {
        return match ($status) {
            'NEW' => ['label' => 'New', 'class' => 'new'],
            'HANDLED' => ['label' => 'On Progress', 'class' => 'progress'],
            'DONE' => ['label' => 'Done', 'class' => 'done'],
            default => ['label' => $status ?: '-', 'class' => 'new'],
        };
    };
@endphp

<style>
    .penugasan-page {
        padding-top: 4px;
    }

    .penugasan-title {
        margin-bottom: 18px;
        color: #2f3d4d;
        font-size: 20px;
        font-weight: 800;
    }

    .penugasan-alert {
        width: 100%;
        margin: 0 0 16px;
        padding: 14px 18px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
    }

    .penugasan-alert.success {
        color: #137232;
        border: 1px solid #bcebc8;
        background: #e6faec;
    }

    .penugasan-alert.error {
        color: #b42318;
        border: 1px solid #ffc9c4;
        background: #fff0ef;
    }

    .penugasan-table-wrap {
        border-radius: 24px;
        overflow-x: auto;
    }

    .penugasan-table {
        min-width: 1120px;
        table-layout: auto;
    }

    .penugasan-table th,
    .penugasan-table td {
        padding-left: 18px;
        padding-right: 18px;
        white-space: nowrap;
    }

    .penugasan-table th {
        color: #39495a;
        font-size: 14px;
        font-weight: 800;
    }

    .penugasan-table td {
        color: #3f4c5a;
        font-size: 13px;
    }

    .penugasan-description {
        max-width: 240px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .assign-form {
        margin: 0;
    }

    .teknisi-select-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
        min-width: 136px;
    }

    .teknisi-select {
        width: 100%;
        height: 30px;
        padding: 0 30px 0 12px;
        border: 1px solid #b7c6d7;
        border-radius: 7px;
        background: #fff;
        color: #344b63;
        font-size: 12px;
        font-weight: 600;
        outline: none;
        appearance: auto;
        cursor: pointer;
    }

    .teknisi-select:focus {
        border-color: #0d8ff2;
        box-shadow: 0 0 0 3px rgba(13, 143, 242, .12);
    }

    .teknisi-select:disabled {
        cursor: not-allowed;
        color: #8a98a8;
        background: #eef2f7;
    }

    .penugasan-status {
        min-width: 100px;
        height: 30px;
        padding: 0 10px;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 0;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
    }

    .penugasan-status.progress {
        color: #806600;
        background: #ffd400;
    }

    .penugasan-status.done {
        color: #128a2b;
        background: #4dff41;
    }

    .penugasan-status.new {
        color: #0d5d9c;
        background: #dceeff;
    }


    .penugasan-note {
        margin: 14px 0 0;
        color: #70849b;
        font-size: 12px;
        line-height: 1.5;
    }

    .penugasan-table {
        min-width: 980px;
        table-layout: fixed;
    }

    .penugasan-table th,
    .penugasan-table td {
        vertical-align: top;
        white-space: normal;
        line-height: 1.45;
    }

    /* Default: 7 columns, no checkbox (cb-col is hidden, but still child 1 in DOM) */
    .penugasan-table th:nth-child(2),
    .penugasan-table td:nth-child(2) { width: 120px; }
    .penugasan-table th:nth-child(3),
    .penugasan-table td:nth-child(3) { width: 150px; }
    .penugasan-table th:nth-child(4),
    .penugasan-table td:nth-child(4) { width: 160px; }
    .penugasan-table th:nth-child(5),
    .penugasan-table td:nth-child(5) { width: 170px; }
    .penugasan-table th:nth-child(6),
    .penugasan-table td:nth-child(6) { width: 230px; }
    .penugasan-table th:nth-child(7),
    .penugasan-table td:nth-child(7) { width: 170px; }
    .penugasan-table th:nth-child(8),
    .penugasan-table td:nth-child(8) { width: 140px; }

    /* Checkbox column hidden by default */
    .cb-col { display: none; }
    .delete-mode .cb-col { display: table-cell; text-align: center; vertical-align: middle; }

    /* When in delete-mode, shift widths for 8 columns */
    .delete-mode .penugasan-table { min-width: 1060px; }
    .delete-mode .penugasan-table th:nth-child(1),
    .delete-mode .penugasan-table td:nth-child(1) { width: 44px; }
    .delete-mode .penugasan-table th:nth-child(2),
    .delete-mode .penugasan-table td:nth-child(2) { width: 110px; }
    .delete-mode .penugasan-table th:nth-child(3),
    .delete-mode .penugasan-table td:nth-child(3) { width: 140px; }
    .delete-mode .penugasan-table th:nth-child(4),
    .delete-mode .penugasan-table td:nth-child(4) { width: 150px; }
    .delete-mode .penugasan-table th:nth-child(5),
    .delete-mode .penugasan-table td:nth-child(5) { width: 150px; }
    .delete-mode .penugasan-table th:nth-child(6),
    .delete-mode .penugasan-table td:nth-child(6) { width: 210px; }
    .delete-mode .penugasan-table th:nth-child(7),
    .delete-mode .penugasan-table td:nth-child(7) { width: 160px; }
    .delete-mode .penugasan-table th:nth-child(8),
    .delete-mode .penugasan-table td:nth-child(8) { width: 110px; }

    .penugasan-checkbox { width: 17px; height: 17px; accent-color: #29ABE2; cursor: pointer; }

    /* Toolbar: hidden by default */
    .bulk-toolbar { display: none; align-items: center; justify-content: space-between; padding: .85rem 1.25rem; border-bottom: 1px solid #F1F5F9; background: #FFFBFB; border-top-left-radius: 2rem; border-top-right-radius: 2rem; }
    .delete-mode .bulk-toolbar { display: flex; }
    .bulk-selected-count { font-size: .85rem; font-weight: 700; color: #64748B; }
    .bulk-actions { display: flex; align-items: center; gap: .6rem; }
    .bulk-delete-btn { border: 1px solid #FCA5A5; color: #DC2626; background: #FEF2F2; border-radius: .75rem; padding: .55rem 1.1rem; font-size: .82rem; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: .4rem; transition: .18s ease; }
    .bulk-delete-btn:hover { background: #DC2626; color: #fff; border-color: #DC2626; }
    .bulk-delete-btn:disabled { opacity: .4; cursor: not-allowed; background: #FEF2F2; color: #DC2626; border-color: #FCA5A5; }
    .bulk-cancel-btn { border: 1px solid #D1D5DB; color: #64748B; background: #fff; border-radius: .75rem; padding: .55rem 1.1rem; font-size: .82rem; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: .4rem; transition: .18s ease; }
    .bulk-cancel-btn:hover { background: #F1F5F9; color: #374151; border-color: #9CA3AF; }

    /* Toggle enter button */
    .delete-mode-toggle { display: flex; align-items: center; justify-content: space-between; padding: .85rem 1.25rem; border-bottom: 1px solid #F1F5F9; border-top-left-radius: 2rem; border-top-right-radius: 2rem; }
    .delete-mode .delete-mode-toggle { display: none; }
    .enter-delete-btn { border: 1px solid #E5E7EB; color: #DC2626; background: #fff; border-radius: .75rem; padding: .55rem 1.1rem; font-size: .82rem; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: .4rem; transition: .18s ease; }
    .enter-delete-btn:hover { background: #FEF2F2; border-color: #FCA5A5; }

    .penugasan-description {
        max-width: none;
        overflow: visible;
        text-overflow: unset;
        word-break: break-word;
    }

    .teknisi-select-wrap {
        width: 100%;
        min-width: 0;
    }

    .teknisi-select {
        min-height: 34px;
        height: auto;
    }

    .penugasan-status {
        justify-content: center;
        text-align: center;
    }

    @media (max-width: 820px) {
        .penugasan-table {
            min-width: 960px;
        }
        .delete-mode .penugasan-table {
            min-width: 1060px;
        }
    }
</style>

<section class="dashboard-card penugasan-page" id="penugasan-section">

    {{-- Enter delete-mode button (visible by default) --}}
    <div class="delete-mode-toggle">
        <span></span>
        <button type="button" class="enter-delete-btn" id="enter-delete-btn">
            <i class="fa-solid fa-trash-can" style="font-size:.75rem;"></i> Hapus Laporan
        </button>
    </div>

    {{-- Bulk delete toolbar (hidden until delete-mode) --}}
    <div class="bulk-toolbar" id="bulk-toolbar">
        <span class="bulk-selected-count" id="bulk-selected-count">0 laporan dipilih</span>
        <div class="bulk-actions">
            <form method="POST" action="{{ route('penugasan.bulk-delete') }}" id="bulk-delete-form" data-confirm-delete data-confirm-title="Hapus laporan terpilih?" data-confirm-text="Semua laporan yang dipilih akan dihapus secara permanen.">
                @csrf
                @method('DELETE')
                <div id="bulk-delete-inputs"></div>
                <button type="submit" class="bulk-delete-btn" id="bulk-delete-btn" disabled>
                    <i class="fa-solid fa-trash-can" style="font-size:.75rem;"></i> Hapus Terpilih
                </button>
            </form>
            <button type="button" class="bulk-cancel-btn" id="cancel-delete-btn">
                <i class="fa-solid fa-xmark" style="font-size:.8rem;"></i> Batal
            </button>
        </div>
    </div>

    <div class="table-wrap penugasan-table-wrap">
        <table class="report-table penugasan-table">
            <thead>
                <tr>
                    <th class="cb-col"><input type="checkbox" class="penugasan-checkbox" id="select-all-checkbox"></th>
                    <th>Tanggal Lapor</th>
                    <th>Pelapor</th>
                    <th>Fasilitas</th>
                    <th>Lokasi Masalah</th>
                    <th>Deskripsi Kerusakan</th>
                    <th>Teknisi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $laporan)
                    @php
                        $tanggal = $laporan->tanggal_lapor
                            ? \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d/m/Y')
                            : '-';
                        $pelapor = data_get($laporan, 'pelapor.nama', 'Guest');
                        $fasilitas = data_get($laporan, 'fasilitas.kategori.nama_kategori', '-') . ' (' . data_get($laporan, 'fasilitas.no_fasilitas', '-') . ')';
                        $lokasi = data_get($laporan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                        $deskripsi = $laporan->deskripsi_kerusakan ?? '-';
                        $selectedAsisten = data_get($laporan, 'tindakLanjut.id_teknisi');
                        $allowedTeknisi = $teknisiList;
                        $status = $statusMeta($laporan->status_pengaduan ?? null);
                    @endphp
                    <tr>
                        <td class="cb-col"><input type="checkbox" class="penugasan-checkbox row-checkbox" value="{{ $laporan->id_pengaduan }}"></td>
                        <td>{{ $tanggal }}</td>
                        <td>{{ $pelapor }}</td>
                        <td>{{ $fasilitas }}</td>
                        <td>{{ $lokasi }}</td>
                        <td class="penugasan-description" title="{{ $deskripsi }}">
                            {{ \Illuminate\Support\Str::limit($deskripsi, 42) }}
                        </td>
                        <td>
                            <form method="POST" action="{{ route('tindak-lanjut.assign', $laporan) }}" class="assign-form" data-assign-form>
                                @csrf
                                <span class="teknisi-select-wrap w-full block">
                                    <div class="relative custom-searchable-select w-full">
                                        <input type="text" readonly placeholder="{{ $allowedTeknisi->isEmpty() ? 'Belum ada asisten' : 'Pilih Teknisi' }}" 
                                               class="form-control searchable-select-trigger cursor-pointer bg-white text-xs font-semibold px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:border-silapor-500" 
                                               style="padding-right: 2rem; height: 34px;"
                                               {{ $allowedTeknisi->isEmpty() ? 'disabled' : '' }}>
                                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                        </div>
                                        @if(!$allowedTeknisi->isEmpty())
                                        <div class="absolute left-0 right-0 z-50 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl hidden searchable-select-dropdown p-2" style="min-width: 180px;">
                                            <input type="text" placeholder="Cari..." 
                                                   class="w-full p-2 mb-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-silapor-500 searchable-select-search">
                                            <ul class="max-h-40 overflow-y-auto searchable-select-options custom-scrollbar space-y-0.5 text-left">
                                                <li data-value="" class="px-2.5 py-1.5 hover:bg-[#E8F7FC] hover:text-silapor-500 rounded-md cursor-pointer text-xs transition-colors">
                                                    Pilih Teknisi
                                                </li>
                                                @foreach($allowedTeknisi as $teknisi)
                                                    <li data-value="{{ $teknisi->id_user }}" class="px-2.5 py-1.5 hover:bg-[#E8F7FC] hover:text-silapor-500 rounded-md cursor-pointer text-xs transition-colors">
                                                        {{ $teknisi->nama }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        <input type="hidden" name="id_asisten" value="{{ $selectedAsisten }}" data-assign-select data-original="{{ $selectedAsisten }}">
                                    </div>
                                </span>
                            </form>
                        </td>
                        <td>
                            <span class="penugasan-status {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state" id="empty-state-cell">Belum ada laporan pengaduan untuk ditugaskan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    </section>



<div id="assign-confirm-modal" class="modal-backdrop" hidden>
    <div class="modal-card" role="dialog" aria-modal="true" style="width:min(440px,96vw);">
        <div class="modal-header">
            <h2>Konfirmasi Penugasan</h2>
            <button type="button" class="modal-close" onclick="document.getElementById('assign-confirm-no')?.click()">&times;</button>
        </div>
        <div class="modal-body" style="text-align:center;">
            <div style="width:64px;height:64px;border-radius:999px;margin:0 auto 1rem;display:grid;place-items:center;background:#DBEAFE;color:#29ABE2;font-size:1.75rem;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <p id="assign-confirm-message" style="margin:0;color:#374151;font-weight:800;line-height:1.6;">
                Tugaskan laporan ini?
            </p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1.35rem;">
                <button type="button" id="assign-confirm-no" style="border:1px solid #E5E7EB;background:#fff;color:#64748B;border-radius:.9rem;padding:.8rem 1rem;font-weight:800;cursor:pointer;">Tidak</button>
                <button type="button" id="assign-confirm-yes" class="btn-primary" style="width:100%;border-radius:.9rem;">Ya</button>
            </div>
        </div>
    </div>
</div>

</main>
</div>

<script>
    function handleResponsiveSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');

        if (!sidebar || !overlay) return;

        if (window.innerWidth < 768) {
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
</script>

@endsection

@push('scripts')
<script>
(function () {
    document.addEventListener('change', function (event) {
        const select = event.target.closest('[data-assign-select]');
        if (!select) return;

        const selectedValue = select.value;
        const originalValue = select.dataset.original || '';

        if (!selectedValue) {
            select.value = originalValue;
            return;
        }

        const wrapper = select.closest('.custom-searchable-select');
        const trigger = wrapper ? wrapper.querySelector('.searchable-select-trigger') : null;
        const selectedText = trigger ? trigger.value.trim() : 'teknisi';
        const form = select.closest('[data-assign-form]');
        if (!form) return;

        const modal = document.getElementById('assign-confirm-modal');
        const message = document.getElementById('assign-confirm-message');
        const yesButton = document.getElementById('assign-confirm-yes');
        const noButton = document.getElementById('assign-confirm-no');

        if (!modal || !message || !yesButton || !noButton) {
            select.value = originalValue;
            return;
        }

        message.textContent = 'Tugaskan laporan ini ke ' + selectedText + '?';
        modal.hidden = false;

        yesButton.onclick = function () {
            modal.hidden = true;
            form.submit();
        };

        noButton.onclick = function () {
            modal.hidden = true;
            select.value = originalValue;
        };
    });
})();

// ── Delete-mode toggle & bulk select logic ──
(function () {
    const section = document.getElementById('penugasan-section');
    const enterBtn = document.getElementById('enter-delete-btn');
    const cancelBtn = document.getElementById('cancel-delete-btn');
    const selectAll = document.getElementById('select-all-checkbox');
    const countLabel = document.getElementById('bulk-selected-count');
    const deleteBtn = document.getElementById('bulk-delete-btn');
    const inputsContainer = document.getElementById('bulk-delete-inputs');

    if (!section || !enterBtn || !selectAll) return;

    function enterDeleteMode() {
        section.classList.add('delete-mode');
        syncUI();
    }

    function exitDeleteMode() {
        section.classList.remove('delete-mode');
        selectAll.checked = false;
        selectAll.indeterminate = false;
        document.querySelectorAll('.row-checkbox').forEach(function (cb) { cb.checked = false; });
        syncUI();
    }

    enterBtn.addEventListener('click', enterDeleteMode);
    cancelBtn.addEventListener('click', exitDeleteMode);

    function getRowCheckboxes() {
        return document.querySelectorAll('.row-checkbox');
    }

    function syncUI() {
        const boxes = getRowCheckboxes();
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;

        countLabel.textContent = count + ' laporan dipilih';
        deleteBtn.disabled = count === 0;

        if (boxes.length > 0 && count === boxes.length) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else if (count > 0) {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }

        inputsContainer.innerHTML = '';
        checked.forEach(function (cb) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            inputsContainer.appendChild(input);
        });

        // Sync colspan for empty state cell if it exists
        const emptyCell = document.getElementById('empty-state-cell');
        if (emptyCell) {
            emptyCell.colSpan = section.classList.contains('delete-mode') ? 8 : 7;
        }
    }

    selectAll.addEventListener('change', function () {
        const boxes = getRowCheckboxes();
        boxes.forEach(function (cb) { cb.checked = selectAll.checked; });
        syncUI();
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('row-checkbox')) {
            syncUI();
        }
    });
})();
</script>
@endpush
