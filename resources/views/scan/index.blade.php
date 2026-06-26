@extends('layouts.app')

@section('title', 'Scan QR Fasilitas - SiLapor')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
    #reader video { transform: none !important; }
    .scan-shell { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:1.25rem; background:#0F172A; }
    .scan-card { width:min(430px, 100%); border:1px solid rgba(148,163,184,.22); border-radius:28px; background:#111827; color:#fff; box-shadow:0 30px 80px rgba(0,0,0,.28); overflow:hidden; }
    .scan-card-body { padding:1.4rem; }
    .scan-brand { display:flex; align-items:center; justify-content:center; gap:.65rem; margin-bottom:1.25rem; }
    .scan-logo { width:44px; height:44px; border-radius:14px; display:grid; place-items:center; background:linear-gradient(135deg,#0090F5,#2563EB); box-shadow:0 14px 32px rgba(0,144,245,.24); }
    .scan-title { margin:0; font-size:1.1rem; font-weight:800; text-align:center; }
    .scan-subtitle { margin:.4rem auto 1.15rem; max-width:330px; color:#CBD5E1; font-size:.86rem; line-height:1.55; text-align:center; }
    #reader { width:100%; min-height:260px; border-radius:22px; overflow:hidden; background:#000; border:1px solid rgba(148,163,184,.22); }
    #reader__scan_region video { width:100% !important; }
    .scan-status { margin:1rem 0 0; color:#CBD5E1; font-size:.8rem; line-height:1.5; text-align:center; }
    .scan-actions { margin-top:1.2rem; display:grid; gap:.75rem; }
    .scan-btn { width:100%; min-height:46px; border:1px solid rgba(148,163,184,.28); border-radius:14px; background:#1F2937; color:#fff; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; font-size:.88rem; font-weight:800; cursor:pointer; text-decoration:none; transition:.18s ease; }
    .scan-btn:hover { background:#273449; border-color:#60A5FA; }
    .scan-btn.primary { border-color:#0EA5E9; background:#0284C7; }
    .scan-btn.primary:hover { background:#0369A1; }
    .scan-help { margin-top:1rem; padding:.85rem; border:1px solid rgba(251,191,36,.32); border-radius:16px; background:rgba(251,191,36,.08); color:#FDE68A; font-size:.76rem; line-height:1.55; }
    .hidden { display:none !important; }
    .scan-file-preview { position:absolute; width:1px; height:1px; overflow:hidden; opacity:0; pointer-events:none; }
</style>
@endpush

@section('content')
<div class="scan-shell">
    <div class="scan-card">
        <div class="scan-card-body">
            <div class="scan-brand">
                <div class="scan-logo"><i class="fa-solid fa-qrcode text-xl"></i></div>
                <span class="font-display font-bold text-xl">SiLapor</span>
            </div>

            <h1 class="scan-title">Scan QR Fasilitas</h1>
            <p class="scan-subtitle">Arahkan kamera ke QR Code fasilitas. Jika kamera HP tidak aktif karena browser memblokir akses kamera, gunakan tombol foto QR.</p>

            <div id="reader"></div>
            <p id="scan-status" class="scan-status">Menyiapkan scanner…</p>

            <div id="reader-file-preview" class="scan-file-preview" aria-hidden="true"></div>

            <div class="scan-actions">
                <label for="qr-file-input" class="scan-btn primary">
                    <i class="fa-solid fa-camera"></i>
                    <span>Foto / Upload QR</span>
                </label>
                <input type="file" id="qr-file-input" accept="image/*" capture="environment" class="hidden">

                <a href="{{ auth()->check() ? route('pengaduan.index') : route('pengaduan.manual.create') }}" class="scan-btn">
                    <i class="fa-regular fa-pen-to-square"></i>
                    <span>Buat Pengaduan Manual</span>
                </a>
            </div>

            <div class="scan-help" id="scan-help" hidden>
                Catatan: kamera browser di HP biasanya hanya berjalan pada HTTPS atau localhost. Untuk server lokal, buka aplikasi dari HP memakai alamat IP komputer dalam jaringan yang sama, lalu gunakan tombol “Foto / Upload QR” bila kamera live tetap diblokir.
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const statusEl = document.getElementById('scan-status');
    const helpEl = document.getElementById('scan-help');
    const fileInput = document.getElementById('qr-file-input');
    const qrReportBasePath = @json('/lapor/qr');

    function setStatus(message) {
        if (statusEl) statusEl.textContent = message;
    }

    function extractQrCode(decodedText) {
        const value = String(decodedText || '').trim();
        if (!value) return '';

        try {
            const parsedUrl = new URL(value, window.location.origin);
            const segments = parsedUrl.pathname.split('/').filter(Boolean);
            const qrIndex = segments.findIndex((segment) => segment.toLowerCase() === 'qr');

            if (qrIndex !== -1 && segments[qrIndex + 1]) {
                return decodeURIComponent(segments[qrIndex + 1]);
            }

            return decodeURIComponent(segments[segments.length - 1] || value);
        } catch (error) {
            return value;
        }
    }

    function goToQrReport(decodedText) {
        const qrCode = extractQrCode(decodedText);

        if (!qrCode) {
            setStatus('QR Code tidak memuat kode fasilitas yang valid.');
            return;
        }

        setStatus('QR terdeteksi, membuka formulir pengaduan…');
        window.location.href = `${qrReportBasePath}/${encodeURIComponent(qrCode)}`;
    }

    function startCameraScanner() {
        if (!window.Html5Qrcode) {
            setStatus('Library QR belum termuat. Gunakan tombol Foto / Upload QR.');
            if (helpEl) helpEl.hidden = false;
            return;
        }

        const isLocalhost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);
        const canUseLiveCamera = window.isSecureContext || isLocalhost;

        if (!canUseLiveCamera || !navigator.mediaDevices?.getUserMedia) {
            setStatus('Kamera live diblokir browser. Gunakan tombol Foto / Upload QR.');
            if (helpEl) helpEl.hidden = false;
            return;
        }

        const scanner = new Html5Qrcode('reader');
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 230, height: 230 } },
            (decodedText) => {
                scanner.stop().catch(() => {});
                goToQrReport(decodedText);
            },
            () => {}
        ).then(() => {
            setStatus('Arahkan kamera ke QR Code…');
        }).catch((error) => {
            console.error(error);
            setStatus('Kamera tidak dapat diakses. Gunakan tombol Foto / Upload QR.');
            if (helpEl) helpEl.hidden = false;
        });
    }

    function bindFileScanner() {
        if (!fileInput) return;

        fileInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            if (!window.Html5Qrcode) {
                setStatus('Library QR belum termuat, sehingga foto QR belum bisa dibaca. Muat ulang halaman atau gunakan pengaduan manual.');
                return;
            }

            setStatus('Membaca QR dari foto…');
            const fileScanner = new Html5Qrcode('reader-file-preview');

            fileScanner.scanFile(file, false)
                .then((decodedText) => goToQrReport(decodedText))
                .catch((error) => {
                    console.error(error);
                    setStatus('QR tidak terbaca dari foto ini. Coba foto ulang dengan cahaya lebih terang atau gunakan pengaduan manual.');
                });
        });
    }

    bindFileScanner();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startCameraScanner);
    } else {
        startCameraScanner();
    }
})();
</script>
@endsection
