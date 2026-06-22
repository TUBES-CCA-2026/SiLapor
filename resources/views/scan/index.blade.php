@extends('layouts.app')

@section('title', 'Scan QR Fasilitas - SiLapor')

@push('head')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
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
            Arahkan kamera ke QR Code yang tertempel di alat/fasilitas lab.
        </p>

        <div id="reader" class="rounded-2xl overflow-hidden bg-black"></div>

        <p id="scan-status" class="text-gray-400 text-xs mt-4">Meminta izin kamera…</p>

        <p class="text-gray-500 text-xs mt-8">
            Tidak punya kamera? Masukkan kode fasilitas secara manual:
        </p>
        <form id="manual-form" class="mt-2 flex gap-2">
            <input id="manual-code" type="text" placeholder="Kode fasilitas"
                   class="flex-1 rounded-xl border border-gray-700 bg-gray-800 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-silapor-500">
            <button type="submit" class="bg-silapor-500 hover:bg-silapor-600 text-white text-sm font-semibold rounded-xl px-4">Buka</button>
        </form>
    </div>
</div>

<script>
    const statusEl = document.getElementById('scan-status');

    function goToReport(decodedText) {
        statusEl.textContent = 'QR terdeteksi, membuka halaman lapor…';
        // decodedText DIHARAPKAN berisi URL lengkap hasil FasilitasLab::scanUrl(),
        // contoh: https://silapor.test/lapor/<qr_code>
        try {
            const url = new URL(decodedText);
            window.location.href = url.toString();
        } catch (e) {
            // fallback kalau QR cuma berisi token, bukan URL penuh
            window.location.href = '{{ url('/lapor') }}/' + decodedText;
        }
    }

    const html5QrCode = new Html5Qrcode('reader');
    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: 230 },
        (decodedText) => {
            html5QrCode.stop().catch(() => {});
            goToReport(decodedText);
        },
        (errorMessage) => { /* frame tanpa QR, diamkan */ }
    ).then(() => {
        statusEl.textContent = 'Arahkan kamera ke QR Code…';
    }).catch((err) => {
        statusEl.textContent = 'Kamera tidak dapat diakses. Gunakan input manual di bawah.';
        console.error(err);
    });

    document.getElementById('manual-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const code = document.getElementById('manual-code').value.trim();
        if (code) window.location.href = '{{ url('/lapor') }}/' + encodeURIComponent(code);
    });
</script>
@endsection
