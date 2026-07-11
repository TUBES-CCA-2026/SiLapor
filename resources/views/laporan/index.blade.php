@extends('layouts.app')

@section('title', 'Laporan | SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.page-styles')

@php
    $user = auth()->user();
    $activeMenu = 'laporan';
    $rows = isset($pengaduanList) ? collect($pengaduanList) : collect();
    $canManageLaporan = in_array($user?->role, ['koordinator_lab', 'laboran'], true);

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
    .detail-btn, .edit-btn, .danger-btn { border-radius:.55rem; padding:.45rem .85rem; font-size:.78rem; font-weight:800; cursor:pointer; transition:.18s ease; }
    .detail-btn { border:1px solid #0090F5; color:#0090F5; background:#EEF8FF; }
    .detail-btn:hover { background:#0090F5; color:#fff; }
    .edit-btn { border:1px solid #CBD5E1; color:#334155; background:#fff; }
    .edit-btn:hover { background:#F1F5F9; border-color:#94A3B8; }
    .danger-btn { border:1px solid #DC2626; color:#DC2626; background:#FEE2E2; }
    .danger-btn:hover { background:#DC2626; color:#fff; }
    .empty-state { padding:2rem!important; text-align:center!important; color:#94A3B8!important; }
    .modal-backdrop { position:fixed; inset:0; z-index:60; padding:20px; background:rgba(15,23,42,.34); backdrop-filter:blur(4px); display:grid; place-items:center; }
    .modal-backdrop[hidden] { display:none!important; }
    .modal-card { width:min(780px,96vw); max-height:92vh; overflow:hidden; border:1px solid #DCE6F1; border-radius:28px; background:#fff; box-shadow:0 28px 70px rgba(30,64,175,.18); }
    .modal-header { min-height:68px; padding:0 24px; border-bottom:0; background:linear-gradient(135deg,#0090F5,#2563EB); display:flex; align-items:center; justify-content:space-between; }
    .modal-header h2 { margin:0; color:#fff; font-size:18px; font-weight:800; }
    .modal-close { border:0; background:transparent; color:#fff; font-size:32px; font-weight:800; line-height:1; cursor:pointer; padding:0; }
    .modal-body { padding:24px; overflow-y:auto; max-height:calc(92vh - 68px); background:#F8FAFC; }
    .detail-grid { display:grid; grid-template-columns:minmax(220px,280px) minmax(0,1fr); gap:20px; align-items:start; }
    .detail-photo-wrap { display:grid; gap:10px; width:100%; max-height:360px; margin:0; border:1px solid #DCE6F1; border-radius:18px; overflow-y:auto; background:#fff; padding:8px; }
    .modal-photo { width:100%; height:160px; display:block; object-fit:cover; border-radius:12px; }
    .modal-photo-placeholder { width:100%; min-height:160px; display:grid; place-items:center; color:#777; font-size:13px; font-weight:700; }
    .detail-panel { width:100%; margin:0; border:1px solid #DCE6F1; border-radius:20px; overflow:hidden; background:#fff; }
    .modal-row { min-height:38px; display:grid; grid-template-columns:96px 12px 1fr; align-items:center; padding:0 16px; background:#fff; color:#475569; font-size:14px; border-bottom:1px solid #EEF2F7; }
    .modal-row:nth-child(even){ background:#F8FAFC; }
    .modal-label { font-weight:700; }
    .modal-row-description { min-height:116px; align-items:start; padding-top:14px; padding-bottom:14px; }
    .description-box { min-height:76px; padding:14px; border:1px solid #DCE6F1; border-radius:18px; background:#fff; color:#475569; line-height:1.45; white-space:pre-wrap; }
    .status-badge { display:inline-block; padding:2px 8px; border-radius:5px; font-size:11px; line-height:1.25; }
    .status-badge.new { color:#0b5b9c; background:#d8ecff; }
    .status-badge.done { color:#0f7433; background:#d9f7e3; }
    .status-badge.progress { color:#6b5700; background:#ffe03d; }
    .status-badge.cancel { color:#B91C1C; background:#FEE2E2; }
    .status-badge.no-sparepart { color:#374151; background:#E5E7EB; }
    .loading-line { height:13px; margin:13px 0; border-radius:30px; background:linear-gradient(90deg,#edf2f7,#f8fbff,#edf2f7); }
    .loading-line.short { width:60%; }
    @media (max-width:820px){ .detail-grid{ grid-template-columns:1fr; } }
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
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Laporan</h1>
            </div>

            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        <section class="dashboard-card">
            <div class="laporan-toolbar">
                <h2 class="section-title">Daftar Laporan</h2>
                <label class="laporan-search" aria-label="Cari laporan">
                    <input type="search" placeholder="Cari laporan..." data-laporan-search>
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
                            <th>Deskripsi Kerusakan</th>
                            <th>Status</th>
                            @if($user?->role !== 'laboran')
                            <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $laporan)
                            @php
                                $tanggal = $laporan->tanggal_lapor ? \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d/m/Y') : '-';
                                $pelapor = data_get($laporan, 'pelapor.nama', data_get($laporan, 'user.nama', 'Guest'));
                                $fasilitas = data_get($laporan, 'fasilitas.kategori.nama_kategori', '-') . ' (' . data_get($laporan, 'fasilitas.no_fasilitas', '-') . ')';
                                $lokasi = data_get($laporan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                                $deskripsi = $laporan->deskripsi_kerusakan ?? '-';
                                $status = $statusMeta($laporan->status_pengaduan ?? null);
                                $detailUrl = route('dashboard.pengaduan.detail', $laporan);
                                $colCount = $user?->role === 'laboran' ? 6 : 7;
                            @endphp
                            <tr data-laporan-row>
                                <td>{{ $tanggal }}</td>
                                <td>{{ $pelapor }}</td>
                                <td>{{ $fasilitas }}</td>
                                <td>{{ $lokasi }}</td>
                                <td class="laporan-description" title="{{ $deskripsi }}">{{ \Illuminate\Support\Str::limit($deskripsi, 45) }}</td>
                                <td><span class="laporan-status {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                @if($user?->role !== 'laboran')
                                <td class="text-center">
                                    <div class="inline-flex items-center gap-2 justify-center">
                                        <button type="button" class="detail-btn" data-detail-url="{{ $detailUrl }}">Detail</button>
                                        @if($canManageLaporan && \Illuminate\Support\Facades\Route::has('laporan.update'))
                                            <button type="button" class="edit-btn" data-edit-target="edit-laporan-{{ $laporan->id_pengaduan }}">Edit</button>
                                        @endif
                                        @if($canManageLaporan && \Illuminate\Support\Facades\Route::has('laporan.destroy'))
                                            <form method="POST" action="{{ route('laporan.destroy', $laporan) }}" class="inline" data-confirm-delete data-confirm-title="Hapus laporan ini?" data-confirm-text="Laporan yang dihapus tidak akan tampil lagi di laporan, riwayat, atau rekapitulasi.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="danger-btn">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @if($canManageLaporan && $user?->role !== 'laboran')
                            <tr id="edit-laporan-{{ $laporan->id_pengaduan }}" hidden>
                                <td colspan="{{ $colCount }}" style="background:#F8FAFC; padding:1rem 1.25rem;">
                                    <form method="POST" action="{{ route('laporan.update', $laporan) }}" style="display:grid; grid-template-columns: 1fr 180px 120px; gap:.75rem; align-items:end;">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label style="display:block;font-size:.75rem;font-weight:800;color:#64748B;margin-bottom:.35rem;">Deskripsi Kerusakan</label>
                                            <textarea name="deskripsi_kerusakan" rows="2" class="form-control" style="width:100%;border:1px solid #D1D5DB;border-radius:.75rem;padding:.7rem;">{{ $deskripsi }}</textarea>
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:.75rem;font-weight:800;color:#64748B;margin-bottom:.35rem;">Status</label>
                                            <select name="status_pengaduan" class="form-control" style="width:100%;border:1px solid #D1D5DB;border-radius:.75rem;padding:.7rem;">
                                                <option value="NEW" @selected(($laporan->status_pengaduan ?? null) === 'NEW')>New</option>
                                                <option value="HANDLED" @selected(($laporan->status_pengaduan ?? null) === 'HANDLED')>On Progress</option>
                                                <option value="DONE" @selected(($laporan->status_pengaduan ?? null) === 'DONE')>Done</option>
                                                <option value="CANCEL" @selected(($laporan->status_pengaduan ?? null) === 'CANCEL')>Cancel</option>
                                                <option value="NO_SPAREPART" @selected(($laporan->status_pengaduan ?? null) === 'NO_SPAREPART')>No Sparepart</option>
                                            </select>
                                        </div>
                                        <button class="detail-btn" type="submit">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr data-empty-row><td colspan="{{ $user?->role === 'laboran' ? 6 : 7 }}" class="empty-state">Belum ada laporan pengaduan.</td></tr>
                        @endforelse

                        @if($rows->isNotEmpty())
                            <tr data-empty-row hidden><td colspan="{{ $user?->role === 'laboran' ? 6 : 7 }}" class="empty-state">Data laporan tidak ditemukan.</td></tr>
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
            <div class="detail-grid">
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
            </div>
        `;
    }

    document.addEventListener('click', async function (event) {
        const modal = document.getElementById('detailModal');
        const modalContent = document.getElementById('modalContent');
        const detailButton = event.target.closest('[data-detail-url]');
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

    document.addEventListener('click', function (event) {
        const editButton = event.target.closest('[data-edit-target]');
        if (!editButton) return;
        const row = document.getElementById(editButton.dataset.editTarget);
        if (row) row.hidden = !row.hidden;
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
