@extends('layouts.silapor-dashboard', [
    'title' => 'Detail Laporan | SiLapor',
    'pageTitle' => 'DETAIL LAPORAN',
    'activeMenu' => 'detail'
])

@section('content')
@php
    $rows = isset($pengaduanList) ? $pengaduanList : collect();

    $statusMeta = function ($status) {
        return match ($status) {
            'NEW' => ['label' => 'Baru', 'class' => 'new'],
            'HANDLED' => ['label' => 'On Progress', 'class' => 'progress'],
            'DONE' => ['label' => 'Done', 'class' => 'done'],
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
        padding-top: 4px;
    }

    .detail-laporan-table-wrap {
        border-radius: 24px;
        overflow-x: auto;
        overflow-y: visible;
    }

    .detail-laporan-table {
        min-width: 1180px;
        table-layout: auto;
    }

    .detail-laporan-table th,
    .detail-laporan-table td {
        padding-left: 18px;
        padding-right: 18px;
        white-space: nowrap;
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
        max-width: 210px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .detail-status {
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

    .detail-status .status-arrow {
        margin-left: auto;
        opacity: .8;
        font-size: 11px;
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
        width: min(520px, 96vw);
        max-height: 92vh;
        overflow: hidden;
        border: 1.5px solid #2f2f2f;
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 18px 35px rgba(0, 0, 0, .18);
    }

    .detail-modal-header {
        height: 58px;
        padding: 0 20px;
        border-bottom: 1.5px solid #2f2f2f;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .detail-modal-header h2 {
        margin: 0;
        color: #404040;
        font-size: 16px;
        font-weight: 800;
    }

    .detail-modal-close {
        border: 0;
        background: transparent;
        color: #4a4a4a;
        font-size: 38px;
        font-weight: 800;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }

    .detail-modal-body {
        padding: 34px 32px 36px;
        overflow-y: auto;
        max-height: calc(92vh - 58px);
    }

    .detail-modal-photo-wrap {
        display: grid;
        gap: 10px;
        width: min(100%, 280px);
        max-height: 360px;
        margin: 0 auto 20px;
        border: 1.5px solid #2f2f2f;
        border-radius: 18px;
        overflow-y: auto;
        background: #f1f1f1;
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
        width: min(100%, 420px);
        margin: 0 auto;
        border: 1.5px solid #2f2f2f;
        border-radius: 20px;
        overflow: hidden;
        background: #f7f7f7;
    }

    .detail-modal-row {
        min-height: 38px;
        display: grid;
        grid-template-columns: 96px 12px 1fr;
        align-items: center;
        padding: 0 16px;
        background: #f0f0f0;
        color: #555;
        font-size: 14px;
    }

    .detail-modal-row:nth-child(even) {
        background: #e8e8e8;
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

    .detail-modal-description-row {
        min-height: 116px;
        align-items: start;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .detail-modal-description {
        min-height: 76px;
        padding: 14px;
        border: 1.5px solid #555;
        border-radius: 18px;
        background: #fff;
        color: #555;
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

    @media (max-width: 820px) {
        .detail-laporan-table {
            min-width: 1080px;
        }
    }
</style>

<section class="dashboard-card detail-laporan-page">
    <div class="table-wrap detail-laporan-table-wrap">
        <table class="report-table detail-laporan-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID PGD</th>
                    <th>Pelapor</th>
                    <th>PJ</th>
                    <th>Lokasi Masalah</th>
                    <th>Fasilitas</th>
                    <th>Status</th>
                    <th>Tanggal<br>Lapor</th>
                    <th>Tanggal<br>Selesai</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $laporan)
                    @php
                        $tindak = $laporan->tindakLanjut;
                        $status = $statusMeta($laporan->status_pengaduan ?? null);
                        $tanggalLapor = $laporan->tanggal_lapor
                            ? \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d/m/Y')
                            : '-';
                        $tanggalSelesai = ($laporan->status_pengaduan === 'DONE' && data_get($tindak, 'tanggal_penanganan'))
                            ? \Carbon\Carbon::parse(data_get($tindak, 'tanggal_penanganan'))->format('d-m-Y')
                            : 'mm/dd/yyyy';
                        $idTindak = $formatTdl(data_get($tindak, 'id_tindak_lanjut'));
                        $idPgd = $formatPgd($laporan->id_pengaduan);
                        $pelapor = data_get($laporan, 'pelapor.nama', 'Guest');
                        $pj = data_get($tindak, 'asisten.nama')
                            ?: data_get($tindak, 'penugas.nama')
                            ?: '-';
                        $lokasi = data_get($laporan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                        $fasilitas = data_get($laporan, 'fasilitas.nama_fasilitas', '-');
                        $detailUrl = route('dashboard.pengaduan.detail', $laporan);
                    @endphp
                    <tr>
                        <td>{{ $idTindak }}</td>
                        <td>{{ $idPgd }}</td>
                        <td>{{ $pelapor }}</td>
                        <td>{{ $pj }}</td>
                        <td class="cell-left lokasi-cell" title="{{ $lokasi }}">{{ $lokasi }}</td>
                        <td>{{ $fasilitas }}</td>
                        <td>
                            <span class="detail-status {{ $status['class'] }}">
                                {{ $status['label'] }}
                                <span class="status-arrow">▾</span>
                            </span>
                        </td>
                        <td>{{ $tanggalLapor }}</td>
                        <td>{{ $tanggalSelesai }}</td>
                        <td class="text-center">
                            <button type="button" class="detail-laporan-btn" data-detail-laporan-url="{{ $detailUrl }}">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty-state">Belum ada detail laporan pengaduan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="detail-laporan-note">Menu ini menampilkan rekap detail pengaduan, penanggung jawab/teknisi, status penanganan, tanggal lapor, dan tanggal selesai.</p>
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
