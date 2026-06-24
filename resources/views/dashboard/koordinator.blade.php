@extends('layouts.silapor-dashboard', [
    'title' => 'Dashboard Koordinator | SiLapor',
    'pageTitle' => 'DASBOARD',
    'activeMenu' => 'dashboard'
])

@section('content')
@php
    $totalLaporan = $totalLaporan ?? 0;
    $proses = $proses ?? 0;
    $selesai = $selesai ?? 0;
    $pengaduanRows = isset($pengaduanList) ? $pengaduanList : collect();
@endphp

<section class="dashboard-card">
    <div class="section-title">Ringkasan Laporan</div>

    <div class="summary-grid">
        <article class="summary-box">
            <div class="summary-icon danger">!</div>
            <div class="summary-info">
                <span>Total<br>Pengaduan</span>
                <strong>{{ number_format($totalLaporan, 0, ',', '.') }}</strong>
            </div>
        </article>

        <article class="summary-box">
            <div class="summary-icon process">🛠</div>
            <div class="summary-info">
                <span>Sedang<br>Diperbaiki</span>
                <strong>{{ number_format($proses, 0, ',', '.') }}</strong>
            </div>
        </article>

        <article class="summary-box">
            <div class="summary-icon success">✓</div>
            <div class="summary-info">
                <span>Selesai</span>
                <strong>{{ number_format($selesai, 0, ',', '.') }}</strong>
            </div>
        </article>
    </div>

    <div class="table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Lokasi Masalah</th>
                    <th>Tanggal Lapor</th>
                    <th>Fasilitas</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengaduanRows as $laporan)
                    @php
                        $id = $laporan->id_pengaduan ?? $laporan->id ?? null;
                        $lokasi = data_get($laporan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                        $fasilitas = data_get($laporan, 'fasilitas.nama_fasilitas', '-');
                        $tanggalRaw = $laporan->tanggal_lapor ?? null;
                        $tanggal = $tanggalRaw ? \Carbon\Carbon::parse($tanggalRaw)->format('d/m/Y') : '-';
                        $detailUrl = $id ? route('dashboard.pengaduan.detail', $laporan) : '#';
                    @endphp
                    <tr>
                        <td>{{ $lokasi }}</td>
                        <td>{{ $tanggal }}</td>
                        <td>{{ $fasilitas }}</td>
                        <td class="text-center">
                            <button type="button" class="detail-btn" data-detail-url="{{ $detailUrl }}">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">Belum ada laporan pengaduan.</td>
                    </tr>
                @endforelse
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

