@extends('layouts.app')

@section('title', 'Riwayat | SiLapor')

@section('content')
@php
    $user = auth()->user();
    $role = $user?->role;
    $sidebarUser = $user;
    $sidebarRole = $role;
    $activeMenu = 'riwayat';
    $pageTitle = 'RIWAYAT';

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $roleLabel = match($role) {
        'laboran' => 'Laboran',
        'koordinator_lab' => 'Koordinator Lab',
        'kepala_lab' => 'Kepala Lab',
        'asisten' => 'Asisten Lab',
        'admin' => 'Admin',
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
    } elseif ($role === 'kepala_lab') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-solid fa-table-columns', $routeSafe('dashboard')],
            ['laporan', 'Laporan', 'fa-regular fa-file-lines', $routeSafe('laporan.index')],
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
    .page-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 2rem;
        box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05);
        overflow: visible;
    }

    .page-card-body { padding: 1.5rem; }

    .section-title {
        margin: 0 0 1rem;
        font-size: 1.25rem;
        font-weight: 800;
        color: #2C3E50;
    }

    .table-wrap {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        background: #fff;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .report-table thead {
        background: #F8FAFC;
        color: #64748B;
        text-transform: uppercase;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .04em;
    }

    .report-table th,
    .report-table td {
        padding: 1rem 1.25rem;
        text-align: left;
        border-bottom: 1px solid #F1F5F9;
        white-space: nowrap;
    }

    .report-table td {
        font-size: .875rem;
        color: #374151;
    }

    .report-table tr:hover td {
        background: #F8FAFC;
    }

    .text-center {
        text-align: center !important;
    }

    .empty-state {
        padding: 2rem !important;
        text-align: center !important;
        color: #94A3B8 !important;
    }

    .detail-btn,
    .btn-outline-blue {
        border: 1px solid #0090F5;
        color: #0090F5;
        background: #EEF8FF;
        border-radius: .5rem;
        padding: .4rem .9rem;
        font-size: .8rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .detail-btn:hover,
    .btn-outline-blue:hover {
        background: #0090F5;
        color: #fff;
    }

    .btn-primary {
        background: #0090F5;
        color: #fff;
        border: 0;
        border-radius: .875rem;
        padding: .75rem 1rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary:hover {
        background: #007CD5;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .7rem;
        border-radius: .5rem;
        font-size: .75rem;
        font-weight: 800;
    }

    .status-chip.progress { background: #FFD400; color: #7A6200; }
    .status-chip.done { background: #4DFF41; color: #128A2B; }
    .status-chip.new { background: #DCEEFF; color: #0D5D9C; }
    .status-chip.cancel { background: #FEE2E2; color: #B91C1C; }
    .status-chip.no-sparepart { background: #E5E7EB; color: #374151; }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 60;
        padding: 20px;
        background: rgba(15, 23, 42, .35);
        display: grid;
        place-items: center;
    }

    .modal-backdrop[hidden] {
        display: none !important;
    }

    .modal-card {
        width: min(520px, 96vw);
        max-height: 92vh;
        overflow: hidden;
        border-radius: 1.5rem;
        background: #fff;
        box-shadow: 0 18px 35px rgba(0,0,0,.18);
    }

    .modal-header {
        height: 58px;
        padding: 0 20px;
        border-bottom: 1px solid #E5E7EB;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-header h2 {
        margin: 0;
        color: #404040;
        font-size: 16px;
        font-weight: 800;
    }

    .modal-close {
        border: 0;
        background: transparent;
        color: #4a4a4a;
        font-size: 38px;
        font-weight: 800;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }

    .modal-body {
        padding: 34px 32px 36px;
        overflow-y: auto;
        max-height: calc(92vh - 58px);
    }

    .detail-photo-wrap {
        display: grid;
        gap: 10px;
        width: min(100%, 280px);
        max-height: 360px;
        margin: 0 auto 20px;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        overflow-y: auto;
        background: #f1f1f1;
        padding: 8px;
    }

    .modal-photo {
        width: 100%;
        height: 160px;
        display: block;
        object-fit: cover;
        border-radius: 12px;
    }

    .modal-photo-placeholder {
        width: 100%;
        min-height: 160px;
        display: grid;
        place-items: center;
        color: #777;
        font-size: 13px;
        font-weight: 700;
    }

    .detail-panel {
        width: min(100%, 420px);
        margin: 0 auto;
        border: 1px solid #E5E7EB;
        border-radius: 20px;
        overflow: hidden;
        background: #f7f7f7;
    }

    .modal-row {
        min-height: 38px;
        display: grid;
        grid-template-columns: 96px 12px 1fr;
        align-items: center;
        padding: 0 16px;
        background: #f0f0f0;
        color: #555;
        font-size: 14px;
    }

    .modal-row:nth-child(even) {
        background: #e8e8e8;
    }

    .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 5px;
        font-size: 11px;
        line-height: 1.25;
        color: #6b5700;
        background: #ffe03d;
    }

    .status-badge.new { color: #0b5b9c; background: #d8ecff; }
    .status-badge.done { color: #0f7433; background: #d9f7e3; }
    .status-badge.progress { color: #6b5700; background: #ffe03d; }
    .status-badge.cancel { color: #B91C1C; background: #FEE2E2; }
    .status-badge.no-sparepart { color: #374151; background: #E5E7EB; }

    .modal-row-description {
        min-height: 116px;
        align-items: start;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .description-box {
        min-height: 76px;
        padding: 14px;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        background: #fff;
        color: #555;
        line-height: 1.45;
        white-space: pre-wrap;
    }

    .loading-line {
        height: 13px;
        margin: 13px 0;
        border-radius: 30px;
        background: linear-gradient(90deg, #edf2f7, #f8fbff, #edf2f7);
    }

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
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">
                    SiLapor
                </span>
            </a>

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
            <form id="logout-form" action="{{ $routeSafe('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
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
                <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight uppercase">
                    RIWAYAT
                </h1>
            </div>

            <div class="bg-[#1E90FF] text-white px-5 py-3 rounded-[22px] flex items-center gap-4 shadow-md w-full sm:w-auto">
                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-[#1E90FF] shrink-0">
                    <i class="fa-solid fa-user text-lg"></i>
                </div>
                <div class="text-left overflow-hidden">
                    <span class="text-[11px] opacity-80 block uppercase tracking-wider">Selamat datang</span>
                    <span class="text-xl font-extrabold block truncate">
                        {{ $user->name ?? $user->nama ?? 'User' }}
                    </span>
                </div>
            </div>
        </header>

        <section class="page-card">
            <div class="page-card-body">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <div>
                        <h2 class="section-title" style="margin-bottom: .35rem;">
                            Riwayat Laporan Selesai
                        </h2>
                        <p style="margin: 0; color: #64748B; font-size: .9rem;">
                            Daftar laporan yang sudah selesai ditangani.
                        </p>
                    </div>
                </div>

                <div class="table-wrap custom-scrollbar">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Lokasi Masalah</th>
                                <th>Fasilitas</th>
                                <th>Tanggal Lapor</th>
                                <th>Tanggal Selesai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $item)
                                @php
                                    $detailUrl = route('dashboard.pengaduan.detail', $item);
                                @endphp
                                <tr>
                                    <td style="font-weight: 800; color: #64748B;">
                                        PGD-{{ str_pad((string) $item->id_pengaduan, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td>
                                        {{ $item->fasilitas?->laboratorium?->nama_laboratorium ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $item->fasilitas?->nama_fasilitas ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $item->tanggal_lapor ? \Carbon\Carbon::parse($item->tanggal_lapor)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>
                                        {{ $item->updated_at ? $item->updated_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="detail-btn" data-detail-url="{{ $detailUrl }}">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        Belum ada riwayat pengaduan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div class="modal-backdrop" id="detailModal" hidden>
            <div class="modal-card" role="dialog" aria-modal="true">
                <div class="modal-header">
                    <h2>Detail Pengaduan</h2>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">×</button>
                </div>
                <div class="modal-body" id="modalContent"></div>
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
                    <div class="modal-row">
                        <span class="modal-label">ID</span>
                        <span>:</span>
                        <span>${esc(data.id)}</span>
                    </div>
                    <div class="modal-row">
                        <span class="modal-label">Status</span>
                        <span>:</span>
                        <span><mark class="status-badge ${statusClass}">${statusLabel}</mark></span>
                    </div>
                    <div class="modal-row">
                        <span class="modal-label">Pelapor</span>
                        <span>:</span>
                        <span>${esc(data.pelapor)}</span>
                    </div>
                    <div class="modal-row">
                        <span class="modal-label">Lokasi</span>
                        <span>:</span>
                        <span>${esc(data.lokasi)}</span>
                    </div>
                    <div class="modal-row">
                        <span class="modal-label">Fasilitas</span>
                        <span>:</span>
                        <span>${esc(data.fasilitas)}</span>
                    </div>
                    <div class="modal-row">
                        <span class="modal-label">Tgl Lapor</span>
                        <span>:</span>
                        <span>${esc(data.tanggal)}</span>
                    </div>
                    <div class="modal-row modal-row-description">
                        <span class="modal-label">Deskripsi</span>
                        <span>:</span>
                        <div class="description-box">${esc(data.deskripsi)}</div>
                    </div>
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
            modalContent.innerHTML = `
                <div class="loading-line"></div>
                <div class="loading-line short"></div>
                <div class="loading-line"></div>
            `;

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

                if (!response.ok) {
                    throw new Error('Gagal mengambil detail laporan.');
                }

                const data = await response.json();
                modalContent.innerHTML = renderDetail(data);
            } catch (error) {
                modalContent.innerHTML = '<p>Detail laporan belum bisa ditampilkan. Pastikan route detail pengaduan sudah benar.</p>';
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    })();
</script>
@endsection