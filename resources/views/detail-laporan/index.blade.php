@extends('layouts.app')

@section('title', auth()->user()?->role === 'koordinator_lab' ? 'Detail Laporan | SiLapor' : 'Rekapsulasi | SiLapor')

@section('content')
@php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = $sidebarRole === 'koordinator_lab' ? 'detail-laporan' : 'rekapsulasi';
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
    .detail-btn, .btn-outline-blue { border: 1px solid #0090F5; color: #0090F5; background: #EEF8FF; border-radius: .5rem; padding: .4rem .9rem; font-size: .8rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .detail-btn:hover, .btn-outline-blue:hover { background: #0090F5; color: #fff; }
    .btn-primary { background: #0090F5; color: #fff; border: 0; border-radius: .875rem; padding: .75rem 1rem; font-weight: 800; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
    .btn-primary:hover { background: #007CD5; }
    .btn-danger-soft { background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: .875rem; padding: .65rem 1rem; font-weight: 700; text-decoration: none; cursor: pointer; }
    .form-control { width: 100%; border: 1px solid #D1D5DB; border-radius: .875rem; padding: .75rem 1rem; background: #fff; outline: none; }
    .form-control:focus { border-color: #0090F5; box-shadow: 0 0 0 3px rgba(0, 144, 245, .14); }
    .field-label { display: block; margin-bottom: .45rem; font-size: .75rem; color: #6B7280; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
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

    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['user' => auth()->user(), 'activeMenu' => auth()->user()?->role === 'koordinator_lab' ? 'detail-laporan' : 'rekapsulasi'])

<main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight uppercase">{{ $sidebarRole === 'koordinator_lab' ? 'DETAIL LAPORAN' : 'REKAPSULASI' }}</h1>
        </div>

        @include('partials.user-welcome-box', ['user' => $user ?? auth()->user()])
    </header>

@php
    $rows = isset($pengaduanList) ? $pengaduanList : collect();

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

    $formatPgd = function ($id) {
        return 'PGD-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    };

    $formatTdl = function ($id) {
        return $id ? 'TDL-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT) : '-';
    };
@endphp

<style>
    .detail-laporan-page {
        overflow: visible !important;
        padding-top: 4px;
    }

    .detail-laporan-table-wrap {
        border-radius: 24px;
        overflow-x: auto;
        overflow-y: hidden;
        scrollbar-width: thin;
        scrollbar-color: #94A3B8 #E2E8F0;
    }

    .detail-laporan-table {
        width: 100%;
        min-width: 900px;
        table-layout: fixed;
    }

    .detail-laporan-table th,
    .detail-laporan-table td {
        padding-left: 18px;
        padding-right: 18px;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .detail-laporan-table th {
        color: #39495a;
        font-size: 14px;
        font-weight: 800;
        text-align: center;
        vertical-align: middle;
    }

    .detail-laporan-table td {
        color: #3f4c5a;
        font-size: 13px;
        text-align: center;
    }

    .detail-laporan-table .cell-left {
        text-align: left;
    }

    .detail-laporan-table .lokasi-cell {
        max-width: none;
    }

    .detail-status {
        min-width: 108px;
        height: 30px;
        padding: 0 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
    }

    .detail-status.progress {
        color: #806600;
        background: #ffd400;
    }

    .detail-status.done {
        color: #128a2b;
        background: #4dff41;
    }

    .detail-status.new {
        color: #0d5d9c;
        background: #dceeff;
    }

    .detail-status.cancel {
        color: #B91C1C;
        background: #FEE2E2;
    }

    .detail-status.no-sparepart {
        color: #374151;
        background: #E5E7EB;
    }


    .detail-laporan-btn {
        min-width: 66px;
        height: 29px;
        padding: 0 13px;
        border: 1px solid #16a4ff;
        border-radius: 5px;
        background: #eef8ff;
        color: #0d8ff2;
        font-size: 12px;
        font-weight: 600;
        line-height: 27px;
        cursor: pointer;
        transition: background .18s ease, color .18s ease, box-shadow .18s ease;
    }

    .detail-laporan-btn:hover {
        background: #0d8ff2;
        color: #fff;
        box-shadow: 0 8px 18px rgba(13, 143, 242, .22);
    }

    .detail-laporan-note {
        margin: 14px 0 0;
        color: #70849b;
        font-size: 12px;
        line-height: 1.5;
    }

    .detail-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 60;
        padding: 20px;
        background: rgba(16, 38, 61, .30);
        backdrop-filter: blur(4px);
        display: grid;
        place-items: center;
    }

    .detail-modal-backdrop[hidden] {
        display: none !important;
    }

    .detail-modal-card {
        width: min(760px, 96vw);
        max-height: 92vh;
        overflow: hidden;
        border: 1px solid #DCE6F1;
        border-radius: 28px;
        background: #fff;
        box-shadow: 0 28px 70px rgba(30, 64, 175, .18);
    }

    .detail-modal-header {
        min-height: 68px;
        padding: 0 24px;
        border-bottom: 0;
        background: linear-gradient(135deg, #0090F5, #2563EB);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .detail-modal-header h2 {
        margin: 0;
        color: #fff;
        font-size: 18px;
        font-weight: 800;
    }

    .detail-modal-close {
        border: 0;
        background: transparent;
        color: #fff;
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }

    .detail-modal-body {
        padding: 24px;
        overflow-y: auto;
        max-height: calc(92vh - 68px);
        background: #F8FAFC;
    }

    .detail-modal-grid {
        display: grid;
        grid-template-columns: minmax(220px, 280px) minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    .detail-modal-photo-wrap {
        display: grid;
        gap: 10px;
        width: 100%;
        max-height: 360px;
        margin: 0;
        border: 1px solid #DCE6F1;
        border-radius: 18px;
        overflow-y: auto;
        background: #fff;
        padding: 8px;
    }

    .detail-modal-photo {
        width: 100%;
        height: 160px;
        display: block;
        object-fit: cover;
        border-radius: 12px;
    }

    .detail-modal-photo-placeholder {
        width: 100%;
        min-height: 160px;
        display: grid;
        place-items: center;
        color: #777;
        font-size: 13px;
        font-weight: 700;
    }

    .detail-modal-panel {
        width: 100%;
        margin: 0;
        border: 1px solid #DCE6F1;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
    }

    .detail-modal-row {
        min-height: 38px;
        display: grid;
        grid-template-columns: 96px 12px 1fr;
        align-items: center;
        padding: 0 16px;
        background: #fff;
        color: #475569;
        font-size: 14px;
        border-bottom: 1px solid #EEF2F7;
    }

    .detail-modal-row:nth-child(even) {
        background: #F8FAFC;
    }

    .detail-modal-label {
        font-weight: 700;
    }

    .detail-modal-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 5px;
        font-size: 11px;
        font-style: normal;
        line-height: 1.25;
        color: #6b5700;
        background: #ffe03d;
    }

    .detail-modal-badge.new {
        color: #0b5b9c;
        background: #d8ecff;
    }

    .detail-modal-badge.done {
        color: #0f7433;
        background: #d9f7e3;
    }

    .detail-modal-badge.progress {
        color: #6b5700;
        background: #ffe03d;
    }

    .detail-modal-badge.cancel {
        color: #B91C1C;
        background: #FEE2E2;
    }

    .detail-modal-badge.no-sparepart {
        color: #374151;
        background: #E5E7EB;
    }


    .detail-modal-description-row {
        min-height: 116px;
        align-items: start;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .detail-modal-description {
        min-height: 76px;
        padding: 14px;
        border: 1px solid #DCE6F1;
        border-radius: 18px;
        background: #fff;
        color: #475569;
        line-height: 1.45;
        white-space: pre-wrap;
    }

    .detail-loading-line {
        height: 13px;
        margin: 13px 0;
        border-radius: 30px;
        background: linear-gradient(90deg, #edf2f7, #f8fbff, #edf2f7);
    }

    .detail-loading-line.short {
        width: 60%;
    }

    .btn-print-report:hover {
        border-color: #DC2626 !important;
        background: #DC2626 !important;
        color: #fff !important;
    }

    .btn-excel-report:hover {
        border-color: #16A34A !important;
        background: #16A34A !important;
        color: #fff !important;
    }

    @media (max-width: 820px) {
        .detail-laporan-table {
            min-width: 900px;
        }

        .detail-modal-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


@php
    $isKoordinator = auth()->user()?->role === 'koordinator_lab';
    $filterAction = $isKoordinator ? route('detail-laporan.index') : route('rekapsulasi.index');
    $groupedRows = $isKoordinator
        ? $rows->groupBy(fn ($item) => data_get($item, 'fasilitas.laboratorium.nama_laboratorium', 'Tanpa Lokasi Lab'))
        : collect(['Daftar Laporan' => $rows]);
@endphp

<section class="dashboard-card detail-laporan-page" style="padding: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
        <div>
            <h2 class="section-title" style="margin: 0; font-size: 1.5rem;">{{ $isKoordinator ? 'Detail Laporan' : 'Rekap Laporan' }}</h2>
            <p style="margin: .35rem 0 0; color: #64748B; font-size: .9rem;">{{ $isKoordinator ? 'Seluruh laporan aktif ditampilkan otomatis berdasarkan laboratorium. Laporan selesai tidak ditampilkan.' : 'Filter dan pantau seluruh riwayat laporan berdasarkan lokasi, fasilitas, dan status.' }}</p>
        </div>
        @unless($isKoordinator)
        <div style="display: flex; gap: .75rem; flex-wrap: wrap;">
            <button type="button" onclick="window.print()" class="btn-outline-blue btn-print-report"><i class="fa-solid fa-print" style="margin-right:.4rem"></i> Print Laporan</button>
            <button type="button" onclick="window.print()" class="btn-outline-blue btn-excel-report"><i class="fa-solid fa-download" style="margin-right:.4rem"></i> Unduh File Excel</button>
        </div>
        @endunless
    </div>

    @unless($isKoordinator)
    <form method="GET" action="{{ $filterAction }}" style="border: 1px solid #D1D5DB; border-radius: 1.25rem; padding: 1rem; background: #F8FAFC; margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1rem; font-size: 1.15rem; font-weight: 800; color: #374151;">Filter Laporan</h3>
        <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; align-items: end;">
            <div><label class="field-label">Penanggung Jawab</label><select name="id_penanggung_jawab" class="form-control"><option value="">Semua Penanggung Jawab</option>@foreach(($penanggungJawabs ?? collect()) as $pj)<option value="{{ $pj->id_user }}" {{ (string)($filters['id_penanggung_jawab'] ?? '') === (string)$pj->id_user ? 'selected' : '' }}>{{ $pj->nama }}</option>@endforeach</select></div>
            <div><label class="field-label">Lokasi Masalah</label><select name="id_laboratorium" class="form-control"><option value="">Semua Lokasi</option>@foreach(($laboratoriums ?? collect()) as $lab)<option value="{{ $lab->id_laboratorium }}" {{ (string)($filters['id_laboratorium'] ?? '') === (string)$lab->id_laboratorium ? 'selected' : '' }}>{{ $lab->nama_laboratorium }}</option>@endforeach</select></div>
            <div><label class="field-label">Fasilitas</label><select name="id_fasilitas" class="form-control"><option value="">Semua Fasilitas</option>@foreach(($fasilitasList ?? collect()) as $fasilitas)<option value="{{ $fasilitas->id_fasilitas }}" {{ (string)($filters['id_fasilitas'] ?? '') === (string)$fasilitas->id_fasilitas ? 'selected' : '' }}>{{ $fasilitas->nama_fasilitas }}</option>@endforeach</select></div>
            <div><label class="field-label">Cari Laporan</label><input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari laporan..." class="form-control"></div>
            <div><label class="field-label">Urutkan</label><select name="sort" class="form-control"><option value="terbaru" {{ ($filters['sort'] ?? 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option><option value="terlama" {{ ($filters['sort'] ?? '') === 'terlama' ? 'selected' : '' }}>Terlama</option></select></div>
            <div><label class="field-label">Status Laporan</label><select name="status" class="form-control"><option value="">Semua Status</option><option value="NEW" {{ ($filters['status'] ?? '') === 'NEW' ? 'selected' : '' }}>New</option><option value="HANDLED" {{ ($filters['status'] ?? '') === 'HANDLED' ? 'selected' : '' }}>On Progress</option><option value="DONE" {{ ($filters['status'] ?? '') === 'DONE' ? 'selected' : '' }}>Done</option><option value="CANCEL" {{ ($filters['status'] ?? '') === 'CANCEL' ? 'selected' : '' }}>Cancel</option><option value="NO_SPAREPART" {{ ($filters['status'] ?? '') === 'NO_SPAREPART' ? 'selected' : '' }}>No Sparepart</option></select></div>
            <div style="display: flex; gap: .75rem; align-items: center;"><button type="submit" class="btn-primary">Terapkan</button><a href="{{ $filterAction }}" class="btn-danger-soft">Reset</a></div>
        </div>
    </form>
    @endunless

    <div style="display: grid; gap: 1.5rem;">
        @forelse($groupedRows as $groupName => $items)
            @if($isKoordinator)<h3 style="margin: 0; color: #2C3E50; font-size: 1.1rem; font-weight: 800;">Lokasi Lab: {{ $groupName }}</h3>@endif
            <div class="table-wrap detail-laporan-table-wrap" style="border: 1px solid #D1D5DB; border-radius: 1.25rem; overflow-x: auto; overflow-y: visible;">
                <table class="report-table detail-laporan-table">
                    <thead><tr><th>Tanggal</th><th>Penanggung Jawab</th><th>Lokasi Masalah</th><th>Fasilitas</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($items as $laporan)
                            @php
                                $tindak = $laporan->tindakLanjut;
                                $status = $statusMeta($laporan->status_pengaduan ?? null);
                                $tanggal = $laporan->tanggal_lapor ? \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d-m-Y') : '-';
                                $pj = data_get($tindak, 'asisten.nama') ?: data_get($tindak, 'penugas.nama') ?: '-';
                                $lokasi = data_get($laporan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                                $fasilitas = data_get($laporan, 'fasilitas.nama_fasilitas', '-');
                                $detailUrl = route('dashboard.pengaduan.detail', $laporan);
                            @endphp
                            <tr><td>{{ $tanggal }}</td><td>{{ $pj }}</td><td>{{ $lokasi }}</td><td>{{ $fasilitas }}</td><td><span class="detail-status {{ $status['class'] }}">{{ $status['label'] }}</span></td><td class="text-center"><button type="button" class="detail-laporan-btn" data-detail-laporan-url="{{ $detailUrl }}">Detail</button></td></tr>
                        @empty
                            <tr><td colspan="6" class="empty-state">Belum ada laporan pada kelompok ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @empty
            <div class="empty-state page-card">Belum ada detail laporan pengaduan.</div>
        @endforelse
    </div>
</section>

<div class="detail-modal-backdrop" id="detailLaporanModal" hidden>
    <div class="detail-modal-card" role="dialog" aria-modal="true" aria-labelledby="detailLaporanModalTitle">
        <div class="detail-modal-header">
            <h2 id="detailLaporanModalTitle">Detail Pengaduan</h2>
            <button type="button" class="detail-modal-close" data-detail-laporan-close aria-label="Tutup">×</button>
        </div>
        <div class="detail-modal-body" id="detailLaporanModalContent">
            <div class="detail-loading-line"></div>
            <div class="detail-loading-line short"></div>
            <div class="detail-loading-line"></div>
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
    const modal = document.getElementById('detailLaporanModal');
    const modalContent = document.getElementById('detailLaporanModalContent');

    if (!modal || !modalContent) return;

    function esc(value) {
        return String(value ?? '-')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function loadingTemplate() {
        return '<div class="detail-loading-line"></div><div class="detail-loading-line short"></div><div class="detail-loading-line"></div>';
    }

    function closeModal() {
        modal.hidden = true;
        modalContent.innerHTML = loadingTemplate();
        document.body.style.overflow = '';
    }

    function renderDetail(data) {
        const fotoItems = Array.isArray(data.fotos) && data.fotos.length
            ? data.fotos
            : (data.foto ? [{ url: data.foto }] : []);

        const foto = fotoItems.length
            ? fotoItems.map((item, index) => {
                const url = typeof item === 'string' ? item : item.url;
                return url
                    ? `<img src="${esc(url)}" alt="Foto kerusakan ${index + 1}" class="detail-modal-photo" loading="lazy">`
                    : '';
            }).join('')
            : `<div class="detail-modal-photo-placeholder">Tidak ada foto</div>`;

        const statusClass = esc(data.statusClass || 'new');
        const statusLabel = esc(data.statusLabel || data.status || '-');

        return `
            <div class="detail-modal-grid">
                <div class="detail-modal-photo-wrap">${foto}</div>
                <div class="detail-modal-panel">
                <div class="detail-modal-row">
                    <span class="detail-modal-label">ID</span>
                    <span>:</span>
                    <span>${esc(data.id)}</span>
                </div>
                <div class="detail-modal-row">
                    <span class="detail-modal-label">Status</span>
                    <span>:</span>
                    <span><mark class="detail-modal-badge ${statusClass}">${statusLabel}</mark></span>
                </div>
                <div class="detail-modal-row">
                    <span class="detail-modal-label">Pelapor</span>
                    <span>:</span>
                    <span>${esc(data.pelapor)}</span>
                </div>
                <div class="detail-modal-row">
                    <span class="detail-modal-label">Lokasi</span>
                    <span>:</span>
                    <span>${esc(data.lokasi)}</span>
                </div>
                <div class="detail-modal-row">
                    <span class="detail-modal-label">Fasilitas</span>
                    <span>:</span>
                    <span>${esc(data.fasilitas)}</span>
                </div>
                <div class="detail-modal-row">
                    <span class="detail-modal-label">Tgl Lapor</span>
                    <span>:</span>
                    <span>${esc(data.tanggal)}</span>
                </div>
                <div class="detail-modal-row detail-modal-description-row">
                    <span class="detail-modal-label">Deskripsi</span>
                    <span>:</span>
                    <div class="detail-modal-description">${esc(data.deskripsi)}</div>
                </div>
                </div>
            </div>
        `;
    }

    document.addEventListener('click', async function (event) {
        const closeButton = event.target.closest('[data-detail-laporan-close]');
        if (closeButton || event.target === modal) {
            closeModal();
            return;
        }

        const detailButton = event.target.closest('[data-detail-laporan-url]');
        if (!detailButton) return;

        const url = detailButton.dataset.detailLaporanUrl;
        modal.hidden = false;
        modalContent.innerHTML = loadingTemplate();
        document.body.style.overflow = 'hidden';

        if (!url) {
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
        if (event.key === 'Escape' && !modal.hidden) closeModal();
    });
})();
</script>
@endpush
