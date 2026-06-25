@extends('layouts.silapor-dashboard', [
    'title' => 'Laporan | SiLapor',
    'pageTitle' => 'LAPORAN',
    'activeMenu' => 'laporan'
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
@endphp

<section class="dashboard-card laporan-page">
    <div class="laporan-toolbar">
        <h2 class="section-title laporan-title">Daftar Laporan</h2>

        <label class="laporan-search" aria-label="Cari laporan">
            <input type="search" placeholder="Cari laporan..." data-laporan-search>
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M16.5 16.5 21 21"></path>
            </svg>
        </label>
    </div>

    <div class="table-wrap laporan-table-wrap">
        <table class="report-table laporan-table" data-laporan-table>
            <thead>
                <tr>
                    <th>Tanggal Lapor</th>
                    <th>Pelapor</th>
                    <th>Fasilitas</th>
                    <th>Lokasi Masalah</th>
                    <th>Deskripsi Kerusakan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
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
                        $status = $statusMeta($laporan->status_pengaduan ?? null);
                        $detailUrl = route('dashboard.pengaduan.detail', $laporan);
                    @endphp
                    <tr data-laporan-row>
                        <td>{{ $tanggal }}</td>
                        <td>{{ $pelapor }}</td>
                        <td>{{ $fasilitas }}</td>
                        <td>{{ $lokasi }}</td>
                        <td class="laporan-description" title="{{ $deskripsi }}">{{ \Illuminate\Support\Str::limit($deskripsi, 45) }}</td>
                        <td>
                            <span class="laporan-status {{ $status['class'] }}">
                                {{ $status['label'] }}
                                <span class="status-arrow">▾</span>
                            </span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="detail-btn" data-detail-url="{{ $detailUrl }}">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr data-empty-row>
                        <td colspan="7" class="empty-state">Belum ada laporan pengaduan.</td>
                    </tr>
                @endforelse

                @if($rows->isNotEmpty())
                    <tr data-empty-row hidden>
                        <td colspan="7" class="empty-state">Data laporan tidak ditemukan.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</section>

<div class="modal-backdrop" id="detailModal" hidden>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-header">
            <h2 id="modalTitle">Detail Pengaduan</h2>
            <button type="button" class="modal-close" data-close-modal aria-label="Tutup">×</button>
        </div>
        <div class="modal-body" id="modalContent">
            <div class="loading-line"></div>
            <div class="loading-line short"></div>
            <div class="loading-line"></div>
        </div>
    </div>
</div>
@endsection

