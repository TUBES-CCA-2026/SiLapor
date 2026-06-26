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
    .report-table { width:100%; border-collapse:collapse; min-width:1040px; }
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
    .action-row { display:inline-flex; align-items:center; justify-content:center; gap:.45rem; flex-wrap:wrap; }
    .detail-btn, .edit-btn, .danger-btn { border-radius:.55rem; padding:.45rem .85rem; font-size:.78rem; font-weight:800; cursor:pointer; transition:.18s ease; }
    .detail-btn { border:1px solid #0090F5; color:#0090F5; background:#EEF8FF; }
    .detail-btn:hover { background:#0090F5; color:#fff; }
    .edit-btn { border:1px solid #CBD5E1; color:#334155; background:#fff; }
    .edit-btn:hover { background:#F1F5F9; border-color:#94A3B8; }
    .danger-btn { border:1px solid #DC2626; color:#DC2626; background:#FEE2E2; }
    .danger-btn:hover { background:#DC2626; color:#fff; }
    .empty-state { padding:2rem!important; text-align:center!important; color:#94A3B8!important; }
    .edit-panel { background:#F8FAFC!important; padding:1rem 1.25rem!important; }
    .edit-form { display:grid; grid-template-columns:1fr 190px 120px; gap:.75rem; align-items:end; }
    .edit-form label { display:block; font-size:.75rem; font-weight:800; color:#64748B; margin-bottom:.35rem; }
    .edit-form textarea,.edit-form select { width:100%; border:1px solid #D1D5DB; border-radius:.75rem; padding:.7rem; background:#fff; }
    @media (max-width: 768px) { .edit-form { grid-template-columns:1fr; } }
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
                            <th>Status</th>
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
                                $deskripsi = $laporan->deskripsi_kerusakan ?? '-';
                                $status = $statusMeta($laporan->status_pengaduan ?? null);
                                $detailUrl = route('dashboard.pengaduan.detail', $laporan);
                                $editRowId = 'edit-row-' . $laporan->id_pengaduan;
                            @endphp
                            <tr data-laporan-row>
                                <td>{{ $tanggal }}</td>
                                <td>{{ $pelapor }}</td>
                                <td>{{ $fasilitas }}</td>
                                <td>{{ $lokasi }}</td>
                                <td>{{ $tanggalSelesai }}</td>
                                <td><span class="laporan-status {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                <td class="text-center">
                                    <div class="action-row">
                                        <button type="button" class="detail-btn" data-detail-url="{{ $detailUrl }}">Detail</button>
                                        <button type="button" class="edit-btn" onclick="toggleEditRow(@json($editRowId))">Edit</button>
                                        <form action="{{ route('riwayat.destroy', $laporan) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-btn">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr id="{{ $editRowId }}" hidden>
                                <td colspan="7" class="edit-panel">
                                    <form method="POST" action="{{ route('riwayat.update', $laporan) }}" class="edit-form">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label>Deskripsi Kerusakan</label>
                                            <textarea name="deskripsi_kerusakan" rows="2">{{ $deskripsi }}</textarea>
                                        </div>
                                        <div>
                                            <label>Status</label>
                                            <select name="status_pengaduan">
                                                <option value="NEW" @selected(($laporan->status_pengaduan ?? null) === 'NEW')>Baru</option>
                                                <option value="HANDLED" @selected(($laporan->status_pengaduan ?? null) === 'HANDLED')>On Progress</option>
                                                <option value="DONE" @selected(($laporan->status_pengaduan ?? null) === 'DONE')>Selesai</option>
                                                <option value="CANCEL" @selected(($laporan->status_pengaduan ?? null) === 'CANCEL')>Cancel</option>
                                                <option value="NO_SPAREPART" @selected(($laporan->status_pengaduan ?? null) === 'NO_SPAREPART')>No Sparepart</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="detail-btn">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty-row><td colspan="7" class="empty-state">Belum ada riwayat pengaduan.</td></tr>
                        @endforelse

                        @if($rows->isNotEmpty())
                            <tr data-empty-row hidden><td colspan="7" class="empty-state">Data riwayat tidak ditemukan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </section>

        @include('partials.detail-modal')
    </main>
</div>

<script>
    function toggleEditRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) row.hidden = !row.hidden;
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

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
                const editRow = row.nextElementSibling;
                if (editRow && editRow.id && editRow.id.startsWith('edit-row-')) {
                    editRow.hidden = true;
                }
                if (isMatch) visibleCount++;
            });
            if (emptyRow) emptyRow.hidden = visibleCount > 0;
        });
    }
</script>
@endsection
