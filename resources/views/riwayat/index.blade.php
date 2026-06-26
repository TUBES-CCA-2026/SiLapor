@extends('layouts.app')

@section('title', 'Riwayat | SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.page-styles')

@php
    $user = auth()->user();
    $activeMenu = 'riwayat';
    $rows = isset($riwayat) ? collect($riwayat) : collect();

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $statusMeta = function ($status) {
        return match ($status) {
            'NEW' => ['label' => 'Baru', 'class' => 'new'],
            'HANDLED' => ['label' => 'On Progress', 'class' => 'progress'],
            'DONE' => ['label' => 'Selesai', 'class' => 'done'],
            'CANCEL' => ['label' => 'Cancel', 'class' => 'cancel'],
            'NO_SPAREPART' => ['label' => 'No Sparepart', 'class' => 'no-sparepart'],
            default => ['label' => $status ?: '-', 'class' => 'new'],
        };
    };
@endphp

@once
<style>
    .dashboard-card { background:#fff; border:1px solid #E5E7EB; border-radius:2rem; box-shadow:0 15px 50px rgba(0,0,0,.05); overflow:hidden; }
    .section-title { margin:0; font-size:1.25rem; font-weight:800; color:#2C3E50; }
    .laporan-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1.25rem 1.5rem; flex-wrap:wrap; }
    .laporan-search { width:min(320px,100%); height:42px; padding:0 .9rem 0 1rem; display:flex; align-items:center; gap:.65rem; border:1px solid #D8E1EC; border-radius:999px; background:#fff; }
    .laporan-search input { width:100%; min-width:0; border:0; outline:0; background:transparent; color:#2C3E50; font-size:.875rem; }
    .report-table { width:100%; border-collapse:collapse; min-width:980px; }
    .report-table thead { background:#F8FAFC; color:#64748B; text-transform:uppercase; font-size:.75rem; font-weight:800; letter-spacing:.04em; }
    .report-table th,.report-table td { padding:1rem 1.25rem; text-align:left; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
    .report-table td { font-size:.875rem; color:#374151; }
    .report-table tr:hover td { background:#F8FAFC; }
    .laporan-description { max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .laporan-status { min-width:112px; height:28px; padding:0 10px; display:inline-flex; align-items:center; justify-content:center; border-radius:7px; font-size:12px; font-weight:800; white-space:nowrap; }
    .laporan-status.progress { color:#756000; background:#FFD400; }
    .laporan-status.done { color:#187C28; background:#59FF45; }
    .laporan-status.new { color:#095E9C; background:#D8ECFF; }
    .laporan-status.cancel { color:#B91C1C; background:#FEE2E2; }
    .laporan-status.no-sparepart { color:#374151; background:#E5E7EB; }
    .detail-btn { border:1px solid #0090F5; color:#0090F5; background:#EEF8FF; border-radius:.5rem; padding:.45rem 1rem; font-size:.8rem; font-weight:800; cursor:pointer; }
    .detail-btn:hover { background:#0090F5; color:#fff; }
    .empty-state { padding:2rem!important; text-align:center!important; color:#94A3B8!important; }
    .modal-backdrop { position:fixed; inset:0; z-index:60; padding:20px; background:rgba(15,23,42,.35); display:grid; place-items:center; }
    .modal-backdrop[hidden] { display:none!important; }
    .modal-card { width:min(520px,96vw); max-height:92vh; overflow:hidden; border-radius:1.5rem; background:#fff; box-shadow:0 18px 35px rgba(0,0,0,.18); }
    .modal-header { height:58px; padding:0 20px; border-bottom:1px solid #E5E7EB; display:flex; align-items:center; justify-content:space-between; }
    .modal-header h2 { margin:0; color:#404040; font-size:16px; font-weight:800; }
    .modal-close { border:0; background:transparent; color:#4a4a4a; font-size:38px; font-weight:800; line-height:1; cursor:pointer; padding:0; }
    .modal-body { padding:34px 32px 36px; overflow-y:auto; max-height:calc(92vh - 58px); }
    .detail-photo-wrap { display:grid; gap:10px; width:min(100%,280px); max-height:360px; margin:0 auto 20px; border:1px solid #E5E7EB; border-radius:18px; overflow-y:auto; background:#f1f1f1; padding:8px; }
    .modal-photo { width:100%; height:160px; display:block; object-fit:cover; border-radius:12px; }
    .modal-photo-placeholder { width:100%; min-height:160px; display:grid; place-items:center; color:#777; font-size:13px; font-weight:700; }
    .detail-panel { width:min(100%,420px); margin:0 auto; border:1px solid #E5E7EB; border-radius:20px; overflow:hidden; background:#f7f7f7; }
    .modal-row { min-height:38px; display:grid; grid-template-columns:96px 12px 1fr; align-items:center; padding:0 16px; background:#f0f0f0; color:#555; font-size:14px; }
    .modal-row:nth-child(even){ background:#e8e8e8; }
    .modal-row-description { min-height:116px; align-items:start; padding-top:14px; padding-bottom:14px; }
    .description-box { min-height:76px; padding:14px; border:1px solid #E5E7EB; border-radius:18px; background:#fff; color:#555; line-height:1.45; white-space:pre-wrap; }
    .status-badge { display:inline-block; padding:2px 8px; border-radius:5px; font-size:11px; line-height:1.25; }
    .status-badge.new { color:#0b5b9c; background:#d8ecff; }
    .status-badge.done { color:#0f7433; background:#d9f7e3; }
    .status-badge.progress { color:#6b5700; background:#ffe03d; }
    .status-badge.cancel { color:#B91C1C; background:#FEE2E2; }
    .status-badge.no-sparepart { color:#374151; background:#E5E7EB; }
    .loading-line { height:13px; margin:13px 0; border-radius:30px; background:linear-gradient(90deg,#edf2f7,#f8fbff,#edf2f7); }
    .loading-line.short { width:60%; }
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
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Riwayat</h1>
            </div>

            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        <section class="dashboard-card">
            <div class="laporan-toolbar">
                <h2 class="section-title">Riwayat Laporan Selesai</h2>
                <label class="laporan-search" aria-label="Cari laporan">
                    <input type="search" placeholder="Cari riwayat..." data-laporan-search>
                    <i class="fa-solid fa-magnifying-glass text-gray-500"></i>
                </label>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="report-table" data-laporan-table>
                    <thead>
                        <tr>
                            <th>Tanggal Lapor</th>
                            <th>Pelapor</th>
                            <th>Fasilitas</th>
                            <th>Lokasi Masalah</th>
                            <th>Tanggal Selesai</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $laporan)
                            @php
                                $tanggal = $laporan->tanggal_lapor ? \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d/m/Y') : '-';
                                $tanggalSelesai = $laporan->updated_at ? $laporan->updated_at->format('d/m/Y') : '-';
                                $pelapor = data_get($laporan, 'pelapor.nama', data_get($laporan, 'user.nama', 'Guest'));
                                $fasilitas = data_get($laporan, 'fasilitas.nama_fasilitas', '-');
                                $lokasi = data_get($laporan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                                $detailUrl = route('dashboard.pengaduan.detail', $laporan);
                            @endphp
                            <tr data-laporan-row>
                                <td>{{ $tanggal }}</td>
                                <td>{{ $pelapor }}</td>
                                <td>{{ $fasilitas }}</td>
                                <td>{{ $lokasi }}</td>
                                <td>{{ $tanggalSelesai }}</td>
                                <td class="text-center"><button type="button" class="detail-btn" data-detail-url="{{ $detailUrl }}">Detail</button></td>
                            </tr>
                        @empty
                            <tr data-empty-row><td colspan="6" class="empty-state">Belum ada riwayat pengaduan.</td></tr>
                        @endforelse

                        @if($rows->isNotEmpty())
                            <tr data-empty-row hidden><td colspan="6" class="empty-state">Data riwayat tidak ditemukan.</td></tr>
                        @endif
                    </tbody>
                </table>
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
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
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
        const fotoItems = Array.isArray(data.fotos) && data.fotos.length ? data.fotos : (data.foto ? [{ url: data.foto }] : []);
        const foto = fotoItems.length
            ? fotoItems.map((item, index) => {
                const url = typeof item === 'string' ? item : item.url;
                return url ? `<img src="${esc(url)}" alt="Foto kerusakan ${index + 1}" class="modal-photo" loading="lazy">` : '';
            }).join('')
            : `<div class="modal-photo-placeholder">Tidak ada foto</div>`;

        const statusClass = esc(data.statusClass || 'new');
        const statusLabel = esc(data.statusLabel || data.status);

        return `
            <div class="detail-photo-wrap">${foto}</div>
            <div class="detail-panel">
                <div class="modal-row"><span>ID</span><span>:</span><span>${esc(data.id)}</span></div>
                <div class="modal-row"><span>Status</span><span>:</span><span><mark class="status-badge ${statusClass}">${statusLabel}</mark></span></div>
                <div class="modal-row"><span>Pelapor</span><span>:</span><span>${esc(data.pelapor)}</span></div>
                <div class="modal-row"><span>Lokasi</span><span>:</span><span>${esc(data.lokasi)}</span></div>
                <div class="modal-row"><span>Fasilitas</span><span>:</span><span>${esc(data.fasilitas)}</span></div>
                <div class="modal-row"><span>Tgl Lapor</span><span>:</span><span>${esc(data.tanggal)}</span></div>
                <div class="modal-row modal-row-description"><span>Deskripsi</span><span>:</span><div class="description-box">${esc(data.deskripsi)}</div></div>
            </div>
        `;
    }

    document.addEventListener('click', async function (event) {
        const modal = document.getElementById('detailModal');
        const modalContent = document.getElementById('modalContent');
        const detailButton = event.target.closest('.detail-btn');
        const closeButton = event.target.closest('[data-close-modal]');

        if (!modal || !modalContent) return;
        if (closeButton || event.target === modal) {
            modal.hidden = true;
            modalContent.innerHTML = '';
            return;
        }
        if (!detailButton) return;

        modal.hidden = false;
        modalContent.innerHTML = '<div class="loading-line"></div><div class="loading-line short"></div><div class="loading-line"></div>';

        try {
            const response = await fetch(detailButton.dataset.detailUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('Gagal mengambil detail.');
            modalContent.innerHTML = renderDetail(await response.json());
        } catch (error) {
            modalContent.innerHTML = '<p>Detail laporan belum bisa ditampilkan.</p>';
        }
    });

    const searchInput = document.querySelector('[data-laporan-search]');
    const table = document.querySelector('[data-laporan-table]');
    if (searchInput && table) {
        const rows = Array.from(table.querySelectorAll('[data-laporan-row]'));
        const emptyRow = table.querySelector('[data-empty-row]');
        searchInput.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            let visibleCount = 0;
            rows.forEach(function (row) {
                const isMatch = row.textContent.toLowerCase().includes(keyword);
                row.hidden = !isMatch;
                if (isMatch) visibleCount++;
            });
            if (emptyRow && rows.length > 0) emptyRow.hidden = visibleCount !== 0;
        });
    }
</script>
@endsection
