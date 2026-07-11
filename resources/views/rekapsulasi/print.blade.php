<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Cetak Rekapitulasi Laporan</title><style>body{font-family:Arial,sans-serif;color:#111827}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #d1d5db;padding:8px;font-size:12px;text-align:left}th{background:#f3f4f6}.no-print{margin-bottom:20px}@media print{.no-print{display:none}}</style></head><body>
<div class="no-print"><button onclick="window.print()">Cetak / Simpan PDF</button></div>
<h2>Rekapitulasi Laporan SiLapor</h2><p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
<table><thead><tr><th>Tanggal</th><th>Pelapor</th><th>Fasilitas</th><th>Lokasi</th><th>Status</th><th>Deskripsi</th></tr></thead><tbody>
@foreach($rows as $row)<tr><td>{{ $row->tanggal_lapor ? $row->tanggal_lapor->format('d/m/Y') : '-' }}</td><td>{{ $row->pelapor?->nama ?? '-' }}</td><td>{{ $row->fasilitas?->kategori?->nama_kategori ?? '-' }} ({{ $row->fasilitas?->no_fasilitas ?? '-' }})</td><td>{{ $row->fasilitas?->laboratorium?->nama_laboratorium ?? '-' }}</td><td>{{ $row->status_pengaduan }}</td><td>{{ $row->deskripsi_kerusakan }}</td></tr>@endforeach
</tbody></table><script>window.addEventListener('load',()=>window.print())</script></body></html>
