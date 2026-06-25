@extends('layouts.app')

@section('title', 'Penugasan | SiLapor')

@section('content')
@php
    $activeMenu = 'penugasan';
    $pageTitle = 'PENUGASAN';
    $user = auth()->user();
    $role = $user?->role;

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $roleLabel = match($role) {
        'laboran' => 'Laboran',
        'koordinator_lab' => 'Koordinator Lab',
        'asisten' => 'Asisten Lab',
        'admin' => 'Admin',
        default => 'User',
    };

    if ($role === 'laboran' || $role === 'admin') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
            ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
            ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
            ['laboratorium', 'Laboratorium', 'fa-regular fa-building', $routeSafe('laboratorium.index')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } elseif ($role === 'koordinator_lab') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
            ['penugasan', 'Penugasan', 'fa-solid fa-user-check', $routeSafe('penugasan.index')],
            ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
            ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
        ];
    } elseif ($role === 'asisten') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['pengaduan', 'Pengaduan', 'fa-regular fa-file-lines', $routeSafe('pengaduan.index')],
            ['tindak-lanjut', 'Tindak Lanjut', 'fa-solid fa-screwdriver-wrench', $routeSafe('tindak-lanjut.index')],
            ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
            ['teknisi', 'Teknisi', 'fa-solid fa-triangle-exclamation', $routeSafe('teknisi.index')],
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
    .page-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 2rem; box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); overflow: hidden; }
    .page-card-body { padding: 1.5rem; }
    .section-title { margin: 0 0 1rem; font-size: 1.25rem; font-weight: 800; color: #2C3E50; }
    .table-wrap { width: 100%; overflow-x: auto; background: #fff; }
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

    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto custom-scrollbar">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 text-decoration-none">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </a>

            @php
                $activeMenu = 'penugasan';
                $routeSafe = function (string $name, string $fallback = '#') {
                    return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
                };
                $sidebarUser = auth()->user();
                $sidebarRole = $sidebarUser?->role;

                if ($sidebarRole === 'laboran' || $sidebarRole === 'admin') {
                    $menuItems = [
                        ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
                        ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
                        ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
                        ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
                        ['laboratorium', 'Laboratorium', 'fa-regular fa-building', $routeSafe('laboratorium.index')],
                        ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
                    ];
                } elseif ($sidebarRole === 'koordinator_lab') {
                    $menuItems = [
                        ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
                        ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
                        ['penugasan', 'Penugasan', 'fa-solid fa-user-check', $routeSafe('penugasan.index')],
                        ['rekapsulasi', 'Rekapsulasi', 'fa-regular fa-rectangle-list', $routeSafe('rekapsulasi.index')],
                        ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
                    ];
                } elseif ($sidebarRole === 'asisten') {
                    $menuItems = [
                        ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
                        ['pengaduan', 'Pengaduan', 'fa-regular fa-file-lines', $routeSafe('pengaduan.index')],
                        ['tindak-lanjut', 'Tindak Lanjut', 'fa-solid fa-screwdriver-wrench', $routeSafe('tindak-lanjut.index')],
                        ['riwayat', 'Riwayat', 'fa-solid fa-clock-rotate-left', $routeSafe('riwayat.index')],
                        ['teknisi', 'Teknisi', 'fa-solid fa-triangle-exclamation', $routeSafe('teknisi.index')],
                        ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
                    ];
                } else {
                    $menuItems = [
                        ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
                        ['profil', 'Profil', 'fa-regular fa-user', $routeSafe('profile.index')],
                    ];
                }
            @endphp

            <nav class="mt-10 space-y-7">
                @foreach($menuItems as [$key, $label, $icon, $url])
                    @if($activeMenu === $key)
                        <a href="{{ $url }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                            <div class="flex items-center gap-3.5">
                                <i class="{{ $icon }} text-lg text-[#0090F5]"></i>
                                <span>{{ $label }}</span>
                            </div>
                            <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                        </a>
                    @else
                        <a href="{{ $url }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                            <i class="{{ $icon }} text-lg"></i>
                            <span>{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <div class="mt-auto p-8 border-t border-gray-100 bg-white rounded-br-[36px] md:rounded-br-none">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ $routeSafe('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden" onclick="toggleSidebar()"></div>

<main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight uppercase">PENUGASAN</h1>
        </div>

        <div class="bg-[#1E90FF] text-white px-5 py-3 rounded-[22px] flex items-center gap-4 shadow-md w-full sm:w-auto">
            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-[#1E90FF] shrink-0">
                <i class="fa-solid fa-user text-lg"></i>
            </div>
            <div class="text-left overflow-hidden">
                <span class="text-[11px] opacity-80 block uppercase tracking-wider">Selamat datang</span>
                <span class="text-xl font-extrabold block truncate">{{ $user->name ?? $user->nama ?? 'User' }}</span>
            </div>
        </div>
    </header>

@php
    $rows = isset($pengaduanList) ? $pengaduanList : collect();
    $teknisiList = isset($asisten) ? $asisten : collect();

    $statusMeta = function ($status) {
        return match ($status) {
            'NEW' => ['label' => 'Baru', 'class' => 'new'],
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
        min-width: 108px;
        height: 30px;
        padding: 0 10px;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
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

    .penugasan-status .status-arrow {
        margin-left: auto;
        opacity: .8;
        font-size: 11px;
    }

    .penugasan-note {
        margin: 14px 0 0;
        color: #70849b;
        font-size: 12px;
        line-height: 1.5;
    }

    @media (max-width: 820px) {
        .penugasan-table {
            min-width: 980px;
        }
    }
</style>

<section class="dashboard-card penugasan-page">
    <h2 class="section-title penugasan-title">Tugaskan Teknisi</h2>

    @if(session('success'))
        <div class="penugasan-alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="penugasan-alert error">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="table-wrap penugasan-table-wrap">
        <table class="report-table penugasan-table">
            <thead>
                <tr>
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
                        $fasilitas = data_get($laporan, 'fasilitas.nama_fasilitas', '-');
                        $lokasi = data_get($laporan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                        $deskripsi = $laporan->deskripsi_kerusakan ?? '-';
                        $selectedAsisten = data_get($laporan, 'tindakLanjut.id_asisten');
                        $status = $statusMeta($laporan->status_pengaduan ?? null);
                    @endphp
                    <tr>
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
                                <span class="teknisi-select-wrap">
                                    <select name="id_asisten" class="teknisi-select" data-assign-select data-original="{{ $selectedAsisten }}" {{ $teknisiList->isEmpty() ? 'disabled' : '' }}>
                                        @if($teknisiList->isEmpty())
                                            <option value="">Belum ada teknisi</option>
                                        @else
                                            <option value="">Pilih Teknisi</option>
                                            @foreach($teknisiList as $teknisi)
                                                <option value="{{ $teknisi->id_user }}" @selected((string) $selectedAsisten === (string) $teknisi->id_user)>
                                                    {{ $teknisi->nama }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </span>
                            </form>
                        </td>
                        <td>
                            <span class="penugasan-status {{ $status['class'] }}">
                                {{ $status['label'] }}
                                <span class="status-arrow">▾</span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada laporan pengaduan untuk ditugaskan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="penugasan-note">Pilih nama teknisi/asisten pada kolom Teknisi untuk memberikan tugas perbaikan. Status akan berubah menjadi On Progress setelah penugasan berhasil disimpan.</p>
</section>

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

        const selectedText = select.options[select.selectedIndex]?.text?.trim() || 'teknisi';
        const form = select.closest('[data-assign-form]');
        if (!form) return;

        const confirmed = window.confirm('Tugaskan laporan ini ke ' + selectedText + '?');
        if (!confirmed) {
            select.value = originalValue;
            return;
        }

        form.submit();
    });
})();
</script>
@endpush
