@extends('layouts.app')

@section('title', 'Scan QR Fasilitas - SiLapor')

@push('head')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
    /* Beberapa browser/device menampilkan preview kamera depan secara
       ter-mirror (efek "cermin"). Kita paksa selalu normal (tidak mirror),
       supaya orientasi QR yang terbaca tetap sesuai aslinya. */
    #reader video { transform: none !important; }
</style>
@endpush

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center p-6 bg-gray-900">
    <div class="w-full max-w-sm text-center">
        <div class="flex items-center justify-center gap-2 mb-6">
            <img src="{{ asset('images/logo-silapor.png') }}" alt="SiLapor" class="w-10 h-10 rounded-lg object-contain">
            <span class="font-display font-bold text-xl text-white">SiLapor</span>
        </div>

        <h1 class="text-white font-display font-semibold text-lg mb-1">Scan QR Fasilitas</h1>
        <p class="text-gray-400 text-sm mb-5">
            Arahkan kamera ke QR Code yang tertempel pada alat atau fasilitas laboratorium.
        </p>

        <div id="reader" class="rounded-2xl overflow-hidden bg-black"></div>

        <p id="scan-status" class="text-gray-400 text-xs mt-4">Meminta izin kamera…</p>

        <div class="mt-8 pt-6 border-t border-gray-800">
            <p class="text-gray-400 text-sm mb-3">Tidak dapat memindai QR?</p>
            <a href="{{ route('pengaduan.manual.create') }}"
               class="inline-flex items-center justify-center w-full rounded-xl border border-gray-700 bg-gray-800 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-3 transition">
                Buat Pengaduan Manual
            </a>
        </div>
    </div>
</div>

<script>
    const statusEl = document.getElementById('scan-status');
    const qrReportBaseUrl = @json(url('/lapor/qr'));

    function extractQrCode(decodedText) {
        const value = decodedText.trim();

        try {
            const parsedUrl = new URL(value, window.location.origin);
            const segments = parsedUrl.pathname.split('/').filter(Boolean);
            return decodeURIComponent(segments[segments.length - 1] || '');
        } catch (error) {
            return value;
        }
    }

    function goToQrReport(decodedText) {
        const qrCode = extractQrCode(decodedText);

        if (!qrCode) {
            statusEl.textContent = 'QR Code tidak memuat kode fasilitas yang valid.';
            return;
        }

        statusEl.textContent = 'QR terdeteksi, membuka formulir pengaduan…';
        window.location.href = `${qrReportBaseUrl}/${encodeURIComponent(qrCode)}`;
    }

    const html5QrCode = new Html5Qrcode('reader');
    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: 230 },
        (decodedText) => {
            html5QrCode.stop().catch(() => {});
            goToQrReport(decodedText);
        },
        () => {}
    ).then(() => {
        statusEl.textContent = 'Arahkan kamera ke QR Code…';
    }).catch((error) => {
        statusEl.textContent = 'Kamera tidak dapat diakses. Silakan upload foto QR di bawah.';
        console.error(error);
    });

    // Fallback: kalau kamera tidak bisa baca QR (blur, pantulan cahaya, dll),
    // user bisa foto/upload gambar QR-nya, lalu di-decode dari gambar itu.
    // Pakai instance Html5Qrcode TERPISAH supaya tidak konflik dengan kamera yang aktif.
    const html5QrCodeFile = new Html5Qrcode('reader-file-preview');

    document.getElementById('qr-file-input').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        statusEl.textContent = 'Membaca QR dari foto…';

        html5QrCodeFile.scanFile(file, false)
            .then((decodedText) => {
                goToQrReport(decodedText);
            })
            .catch((error) => {
                statusEl.textContent = 'QR tidak terbaca dari foto ini. Coba foto lain, atau gunakan pengaduan manual.';
                console.error(error);
            });
        statusEl.textContent = 'Kamera tidak dapat diakses. Gunakan pengaduan manual.';
        console.error(error);
    });
</script>
@endsection
