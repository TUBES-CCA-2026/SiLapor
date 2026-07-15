@extends('layouts.app')

@section('title', 'Edit User | SiLapor')

@section('content')
@php
    $editingUser = $user;
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'users';
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
    .form-control { width: 100%; border: 1px solid #D1D5DB; border-radius: .875rem; padding: .75rem 1rem; background: #fff; outline: none; font-size: .875rem; color: #374151; }
    .form-control:focus { border-color: #0090F5; box-shadow: 0 0 0 3px rgba(0, 144, 245, .14); }
    select.form-control { appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7280' d='M6 8.825a.7.7 0 0 1-.5-.206L1.205 4.324a.71.71 0 0 1 0-1.001.71.71 0 0 1 1.001 0L6 7.118l3.794-3.795a.71.71 0 0 1 1.001 0 .71.71 0 0 1 0 1.001L6.5 8.619a.7.7 0 0 1-.5.206Z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem; cursor: pointer; }
    select.form-control:focus { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230090F5' d='M6 8.825a.7.7 0 0 1-.5-.206L1.205 4.324a.71.71 0 0 1 0-1.001.71.71 0 0 1 1.001 0L6 7.118l3.794-3.795a.71.71 0 0 1 1.001 0 .71.71 0 0 1 0 1.001L6.5 8.619a.7.7 0 0 1-.5.206Z'/%3E%3C/svg%3E"); }
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
        .sidebar-desktop {
            transform: translateX(0) !important;
            position: sticky !important;
            top: 0;
            height: 100vh;
        }
        .hide-on-desktop { display: none !important; }
    }

    /* Custom Searchable Dropdown styles */
    .custom-select-wrapper {
        position: relative;
        width: 100%;
    }
    .custom-select-trigger {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        background: #fff;
        height: 42px;
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        user-select: none;
        border: 1px solid #D1D5DB;
        border-radius: 0.875rem;
        transition: all 0.2s;
    }
    .custom-select-trigger:hover {
        border-color: #CBD5E1;
    }
    .custom-select-trigger.active {
        border-color: #0090F5;
        box-shadow: 0 0 0 3px rgba(0, 144, 245, 0.14);
    }
    .custom-select-options-container {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        z-index: 99;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        opacity: 0;
        transform: translateY(-10px);
        pointer-events: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-select-options-container.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
    .custom-select-search-box {
        padding: 10px;
        border-bottom: 1px solid #F1F5F9;
        background: #F8FAFC;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .custom-select-search-box input {
        width: 100%;
        border: 1px solid #E2E8F0;
        border-radius: 0.6rem;
        padding: 6px 10px;
        font-size: 0.8rem;
        outline: none;
        transition: border-color 0.2s;
    }
    .custom-select-search-box input:focus {
        border-color: #0090F5;
    }
    .custom-select-options {
        max-height: 180px;
        overflow-y: auto;
    }
    .custom-select-option {
        padding: 10px 14px;
        font-size: 0.82rem;
        color: #374151;
        cursor: pointer;
        transition: all 0.15s;
        text-align: left;
    }
    .custom-select-option:hover {
        background: #F0F9FF;
        color: #0090F5;
        padding-left: 18px;
    }
    .custom-select-option.selected {
        background: #EEF8FF;
        color: #0090F5;
        font-weight: 700;
    }
    .custom-select-option.placeholder-opt {
        color: #9CA3AF;
        font-style: italic;
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['activeMenu' => 'users'])

<main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight uppercase">EDIT USER</h1>
        </div>

        @include('partials.user-welcome-box', ['user' => $user ?? auth()->user()])
    </header>

<section class="page-card" style="max-width: 720px; margin: 0 auto; width: 100%;">
    <div class="page-card-body">
        <h2 class="section-title" style="margin-bottom: .35rem;">{{ $editingUser->nama }}</h2>
        <p style="margin: 0 0 1.5rem; color: #64748B; font-size: .9rem;">{{ $editingUser->email }}</p>
<div style="display: grid; gap: 1.5rem;">
            <form method="POST" action="{{ route('admin.users.update', $editingUser->id_user) }}" style="display: grid; gap: 1rem;">
                @csrf
                @method('PUT')
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">Edit Profil</h3>
                <div><label class="field-label">Nama</label><input name="nama" value="{{ old('nama', $editingUser->nama) }}" required class="form-control"></div>
                <div><label class="field-label">Email</label><input type="email" name="email" value="{{ old('email', $editingUser->email) }}" required class="form-control"></div>
                <div>
                    <label class="field-label">Role</label>
                    <div class="custom-select-wrapper" id="custom-select-role">
                        <input type="hidden" name="role" required id="role-select" value="{{ old('role', $editingUser->role) }}">
                        <div class="custom-select-trigger" onclick="toggleCustomSelect('custom-select-role')">
                            <span class="selected-text text-gray-400">Pilih role</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                        <div class="custom-select-options-container">
                            <div class="custom-select-search-box">
                                <i class="fa-solid fa-magnifying-glass text-xs text-gray-400"></i>
                                <input type="text" placeholder="Cari..." oninput="filterCustomSelectOptions(this, 'custom-select-role')">
                            </div>
                            <div class="custom-select-options custom-scrollbar">
                                <div class="custom-select-option placeholder-opt" data-value="" onclick="selectCustomOption(this, 'custom-select-role')">Pilih role</div>
                                @foreach ($roles as $r)
                                    <div class="custom-select-option" data-value="{{ $r }}" onclick="selectCustomOption(this, 'custom-select-role')">
                                        {{ str_replace('_', ' ', ucwords($r, '_')) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div id="koordinator-lab-only" style="display: none;">
                    <label class="field-label">Laboratorium yang Dikoordinasi</label>
                    <div class="custom-select-wrapper" id="custom-select-lab">
                        <input type="hidden" name="id_laboratorium" id="lab-select" value="{{ old('id_laboratorium', $editingUser->laboratoriumDikoordinatori->first()?->id_laboratorium ?? '') }}">
                        <div class="custom-select-trigger" onclick="toggleCustomSelect('custom-select-lab')">
                            <span class="selected-text text-gray-400">Pilih Laboratorium</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                        <div class="custom-select-options-container">
                            <div class="custom-select-search-box">
                                <i class="fa-solid fa-magnifying-glass text-xs text-gray-400"></i>
                                <input type="text" placeholder="Cari..." oninput="filterCustomSelectOptions(this, 'custom-select-lab')">
                            </div>
                            <div class="custom-select-options custom-scrollbar">
                                <div class="custom-select-option placeholder-opt" data-value="" onclick="selectCustomOption(this, 'custom-select-lab')">Pilih Laboratorium</div>
                                @foreach (($laboratoriums ?? collect()) as $lab)
                                    <div class="custom-select-option" data-value="{{ $lab->id_laboratorium }}" onclick="selectCustomOption(this, 'custom-select-lab')">
                                        {{ $lab->nama_laboratorium }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                    <div><label class="field-label">No. HP</label><input type="text" inputmode="numeric" pattern="[0-9]*" name="phone" value="{{ old('phone', $editingUser->phone) }}" class="form-control" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                    <div data-asisten-only><label class="field-label">NIM</label><input type="text" inputmode="numeric" pattern="[0-9]*" name="nim" value="{{ old('nim', $editingUser->nim) }}" class="form-control" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                    <div data-asisten-only>
                        <label class="field-label">Jurusan</label>
                        <div class="custom-select-wrapper" id="custom-select-jurusan">
                            <input type="hidden" name="jurusan" id="jurusan-select" value="{{ old('jurusan', $editingUser->jurusan) }}">
                            <div class="custom-select-trigger" onclick="toggleCustomSelect('custom-select-jurusan')">
                                <span class="selected-text text-gray-400">Pilih Jurusan</span>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                            <div class="custom-select-options-container">
                                <div class="custom-select-search-box">
                                    <i class="fa-solid fa-magnifying-glass text-xs text-gray-400"></i>
                                    <input type="text" placeholder="Cari..." oninput="filterCustomSelectOptions(this, 'custom-select-jurusan')">
                                </div>
                                <div class="custom-select-options custom-scrollbar">
                                    <div class="custom-select-option placeholder-opt" data-value="" onclick="selectCustomOption(this, 'custom-select-jurusan')">Pilih Jurusan</div>
                                    <div class="custom-select-option" data-value="Sistem Informasi" onclick="selectCustomOption(this, 'custom-select-jurusan')">Sistem Informasi</div>
                                    <div class="custom-select-option" data-value="Teknik Informatika" onclick="selectCustomOption(this, 'custom-select-jurusan')">Teknik Informatika</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">Simpan Profil</button>
            </form>

            <form method="POST" action="{{ route('admin.users.reset-password', $editingUser->id_user) }}" style="display: grid; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid #E5E7EB;">
                @csrf
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">Buat Password Baru</h3>
                <p style="margin: 0; color: #64748B; font-size: .9rem;">Password baru langsung aktif setelah disimpan.</p>
                <div><label class="field-label">Password Baru</label><input type="password" name="password" minlength="8" required class="form-control"></div>
                <div><label class="field-label">Konfirmasi Password</label><input type="password" name="password_confirmation" minlength="8" required class="form-control"></div>
                <button type="submit" class="btn-primary" style="width: 100%; background: #F59E0B;">Ganti Password</button>
            </form>

            @if ($editingUser->id_user !== auth()->id())
                <form
                    method="POST"
                    action="{{ route('admin.users.destroy', $editingUser->id_user) }}"
                    data-confirm-delete
                    data-confirm-title="Hapus user {{ $editingUser->nama }}?"
                    data-confirm-text="Pilih Ya untuk menghapus user ini atau Tidak untuk membatalkan."
                    data-confirm-yes="Ya"
                    data-confirm-no="Tidak"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger-soft" style="width: 100%;">Hapus user ini</button>
                </form>
            @endif

            <a href="{{ route('admin.users.index') }}" class="btn-outline-blue" style="justify-self: center;">← Kembali ke daftar user</a>
        </div>
    </div>
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



<div id="role-limit-modal" class="modal-backdrop" hidden>
    <div class="modal-card" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2>Role Tidak Tersedia</h2>
            <button type="button" class="modal-close" onclick="closeRoleLimitModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="role-limit-message" style="margin:0 0 1rem;color:#374151;font-weight:700;line-height:1.6;"></p>
            <button type="button" class="btn-primary" style="width:100%;" onclick="closeRoleLimitModal()">Mengerti</button>
        </div>
    </div>
</div>

<script>
(function () {
    const roleSelect = document.getElementById('role-select');
    const asistenOnlyFields = Array.from(document.querySelectorAll('[data-asisten-only]'));
    const roleCounts = @json($roleCounts ?? []);
    const roleLimits = @json($roleLimits ?? []);
    const currentRole = @json(old('role', $editingUser->role ?? ''));
    const message = document.getElementById('role-limit-message');
    const modal = document.getElementById('role-limit-modal');

    window.closeRoleLimitModal = function () {
        if (modal) modal.hidden = true;
    };

    function humanRole(role) {
        return String(role || '').replaceAll('_', ' ');
    }

    function applyRoleFields(role) {
        const showProfileFields = role === 'asisten';
        asistenOnlyFields.forEach((field) => {
            field.style.display = showProfileFields ? '' : 'none';
            field.querySelectorAll('input').forEach((input) => {
                if (!showProfileFields) {
                    input.value = '';
                    const textSpan = field.querySelector('.selected-text');
                    if (textSpan) {
                        textSpan.textContent = 'Pilih Jurusan';
                        textSpan.classList.add('text-gray-400');
                        textSpan.classList.remove('text-gray-700');
                    }
                    field.querySelectorAll('.custom-select-option').forEach(opt => opt.classList.remove('selected'));
                }
            });
        });

        const showLabFields = role === 'koordinator_lab';
        const labField = document.getElementById('koordinator-lab-only');
        if (labField) {
            labField.style.display = showLabFields ? '' : 'none';
            if (!showLabFields) {
                const hiddenInput = labField.querySelector('input[type="hidden"]');
                if (hiddenInput) hiddenInput.value = '';
                const textSpan = labField.querySelector('.selected-text');
                if (textSpan) {
                    textSpan.textContent = 'Pilih Laboratorium';
                    textSpan.classList.add('text-gray-400');
                    textSpan.classList.remove('text-gray-700');
                }
                labField.querySelectorAll('.custom-select-option').forEach(opt => opt.classList.remove('selected'));
            }
        }
    }

    function showRoleLimit(role) {
        if (!modal || !message) return;
        message.textContent = `Role ${humanRole(role)} sudah mencapai batas maksimal akun. Pilih role lain.`;
        modal.hidden = false;
    }

    if (roleSelect) {
        applyRoleFields(roleSelect.value || currentRole);

        roleSelect.addEventListener('change', function () {
            const role = this.value;
            applyRoleFields(role);

            if (roleLimits[role] && Number(roleCounts[role] || 0) >= Number(roleLimits[role] || 0)) {
                showRoleLimit(role);
                this.value = currentRole && currentRole !== role ? currentRole : '';
                applyRoleFields(this.value);
            }
        });
    }

    // Custom Select Dropdown logic
    window.toggleCustomSelect = function(wrapperId) {
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

    window.filterCustomSelectOptions = function(input, wrapperId) {
        const query = input.value.trim().toLowerCase();
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const options = wrapper.querySelectorAll('.custom-select-option');
        options.forEach(opt => {
            const isPlaceholder = opt.classList.contains('placeholder-opt');
            if (isPlaceholder) return;

            const text = opt.textContent.trim().toLowerCase();
            if (text.includes(query)) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    window.selectCustomOption = function(optionEl, wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const value = optionEl.dataset.value;
        const text = optionEl.textContent.trim();
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const selectedTextSpan = wrapper.querySelector('.selected-text');

        if (hiddenInput) {
            hiddenInput.value = value;
            hiddenInput.dispatchEvent(new Event('change'));
        }

        if (selectedTextSpan) {
            selectedTextSpan.textContent = text;
            if (value === '') {
                selectedTextSpan.classList.add('text-gray-400');
                selectedTextSpan.classList.remove('text-gray-700');
            } else {
                selectedTextSpan.classList.remove('text-gray-400');
                selectedTextSpan.classList.add('text-gray-700');
            }
        }

        wrapper.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        optionEl.classList.add('selected');

        const container = wrapper.querySelector('.custom-select-options-container');
        const trigger = wrapper.querySelector('.custom-select-trigger');
        if (container) container.classList.remove('show');
        if (trigger) trigger.classList.remove('active');
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.custom-select-wrapper')) {
            document.querySelectorAll('.custom-select-options-container').forEach(container => {
                container.classList.remove('show');
            });
            document.querySelectorAll('.custom-select-trigger').forEach(trigger => {
                trigger.classList.remove('active');
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            if (hiddenInput && hiddenInput.value) {
                const val = hiddenInput.value;
                const optionEl = wrapper.querySelector(`.custom-select-option[data-value="${val}"]`);
                if (optionEl) {
                    selectCustomOption(optionEl, wrapper.id);
                }
            }
        });
    });
})();
</script>

@endsection
