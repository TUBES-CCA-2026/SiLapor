@extends('layouts.app')

@section('title', 'Scan QR Fasilitas - SiLapor')

@push('head')
<script defer src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
    .scan-shell { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:1.25rem; background:#0F172A; }
    .scan-card { width:min(440px, 100%); border:1px solid rgba(148,163,184,.22); border-radius:28px; background:#111827; color:#fff; box-shadow:0 30px 80px rgba(0,0,0,.28); overflow:hidden; }
    .scan-card-body { padding:1.4rem; }
    .scan-brand { display:flex; align-items:center; justify-content:center; gap:.65rem; margin-bottom:1.25rem; }
    .scan-logo { width:44px; height:44px; border-radius:14px; display:grid; place-items:center; background:linear-gradient(135deg,#0090F5,#3B82F6); box-shadow:0 14px 32px rgba(0,144,245,.24); color:#fff; }
    .scan-brand-text { font-size:1.35rem; font-weight:800; letter-spacing:-.02em; background:linear-gradient(90deg,#38BDF8,#93C5FD); -webkit-background-clip:text; background-clip:text; color:transparent; }
    .scan-title { margin:0; font-size:1.1rem; font-weight:800; text-align:center; }
    .scan-subtitle { margin:.4rem auto 1.15rem; max-width:340px; color:#CBD5E1; font-size:.86rem; line-height:1.55; text-align:center; }
    #reader { width:100%; min-height:292px; border-radius:22px; overflow:hidden; background:#000; border:1px solid rgba(148,163,184,.22); display:grid; place-items:center; }
    #reader video,
    #reader__scan_region video { width:100% !important; height:100% !important; min-height:292px !important; object-fit:cover !important; transform:none !important; }
    .scan-placeholder { width:100%; min-height:292px; display:grid; place-items:center; color:#94A3B8; text-align:center; padding:1.25rem; }
    .scan-placeholder i { display:block; font-size:2.4rem; margin-bottom:.75rem; color:#CBD5E1; }
    .scan-placeholder p { margin:0; font-size:.84rem; line-height:1.55; }
    .scan-status { margin:1rem 0 0; color:#CBD5E1; font-size:.8rem; line-height:1.5; text-align:center; }
    .scan-actions { margin-top:1.2rem; display:grid; gap:.75rem; }
    .scan-btn { width:100%; min-height:46px; border:1px solid rgba(148,163,184,.28); border-radius:14px; background:#1F2937; color:#fff; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; font-size:.88rem; font-weight:800; cursor:pointer; text-decoration:none; transition:.18s ease; }
    .scan-btn:hover { background:#273449; border-color:#60A5FA; }
    .scan-btn.secondary { border-color:#38BDF8; color:#E0F2FE; }
    .scan-help { margin-top:1rem; padding:.85rem; border:1px solid rgba(251,191,36,.32); border-radius:16px; background:rgba(251,191,36,.08); color:#FDE68A; font-size:.76rem; line-height:1.55; }
    .hidden { display:none !important; }
    .scan-file-preview { position:absolute; width:1px; height:1px; overflow:hidden; opacity:0; pointer-events:none; }
</style>
@endpush

@section('content')
@php
    $isGuestScan = ! auth()->check();
    $manualReportUrl = auth()->check() && auth()->user()?->isAsisten()
        ? route('pengaduan.index')
        : route('pengaduan.manual.create');
@endphp

<div class="scan-shell">
    <div class="scan-card">
        <div class="scan-card-body">
            <div class="scan-brand">
                <div class="scan-logo"><i class="fa-solid fa-square-poll-vertical text-xl"></i></div>
                <span class="font-display scan-brand-text">SiLapor</span>
            </div>

            <h1 class="scan-title">Scan QR Fasilitas</h1>
            <p class="scan-subtitle">Kamera akan dibuka otomatis. Arahkan kamera belakang HP ke QR Code fasilitas. Jika kamera live ditolak browser, gunakan Foto / Upload QR.</p>

            <div id="reader">
                <div class="scan-placeholder">
                    <div>
                        <i class="fa-solid fa-camera"></i>
                        <p>Membuka kamera scanner secara otomatis…</p>
                    </div>
                </div>
            </div>
            <p id="scan-status" class="scan-status">Memuat scanner kamera…</p>

            <div id="reader-file-preview" class="scan-file-preview" aria-hidden="true"></div>

            <div class="scan-actions">
                <label for="qr-file-input" class="scan-btn secondary">
                    <i class="fa-solid fa-camera-retro"></i>
                    <span>Foto / Upload QR</span>
                </label>
                <input type="file" id="qr-file-input" accept="image/*" capture="environment" class="hidden">

                <a href="{{ $manualReportUrl }}" class="scan-btn">
                    <i class="fa-regular fa-pen-to-square"></i>
                    <span>Buat Pengaduan Manual</span>
                </a>

                @if ($isGuestScan)
                    <a href="{{ route('login') }}" class="scan-btn">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali ke Login</span>
                    </a>
                @endif
            </div>

            <div class="scan-help" id="scan-help" hidden>
                Kamera live di HP hanya stabil jika halaman dibuka melalui HTTPS atau localhost. Untuk pengujian dari HP, gunakan HTTPS tunnel seperti ngrok/Cloudflare Tunnel, atau gunakan tombol Foto / Upload QR.
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const readerEl = document.getElementById('reader');
    const statusEl = document.getElementById('scan-status');
    const helpEl = document.getElementById('scan-help');
    const fileInput = document.getElementById('qr-file-input');
    const qrReportRouteTemplate = @json(route('pengaduan.qr.create', ['qr_code' => '__QR_CODE__'], false));

    let html5Scanner = null;
    let nativeStream = null;
    let nativeTimer = null;
    let isRedirecting = false;

    function setStatus(message) {
        if (statusEl) statusEl.textContent = message;
    }

    function showHelp() {
        if (helpEl) helpEl.hidden = false;
    }

    function clearReader() {
        if (readerEl) readerEl.innerHTML = '';
    }

    function stopNativeCamera() {
        if (nativeTimer) {
            window.clearTimeout(nativeTimer);
            nativeTimer = null;
        }
        if (nativeStream) {
            nativeStream.getTracks().forEach((track) => track.stop());
            nativeStream = null;
        }
    }

    function stopHtml5Scanner() {
        if (!html5Scanner) return Promise.resolve();
        const scanner = html5Scanner;
        html5Scanner = null;
        return scanner.stop().catch(() => {}).then(() => scanner.clear?.()).catch(() => {});
    }

    function isSecureEnoughForLiveCamera() {
        const localhostNames = ['localhost', '127.0.0.1', '::1'];
        return window.isSecureContext || localhostNames.includes(window.location.hostname);
    }

    function waitForHtml5Qrcode(timeoutMs = 3500) {
        if (window.Html5Qrcode) return Promise.resolve(true);

        return new Promise((resolve) => {
            const startedAt = Date.now();
            const timer = window.setInterval(() => {
                if (window.Html5Qrcode) {
                    window.clearInterval(timer);
                    resolve(true);
                    return;
                }

                if (Date.now() - startedAt >= timeoutMs) {
                    window.clearInterval(timer);
                    resolve(false);
                }
            }, 100);
        });
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

            if (segments.length >= 2 && segments[0].toLowerCase() === 'lapor') {
                return decodeURIComponent(segments[segments.length - 1]);
            }

            return decodeURIComponent(segments[segments.length - 1] || value);
        } catch (error) {
            return value;
        }
    }

    function goToQrReport(decodedText) {
        if (isRedirecting) return;

        const qrCode = extractQrCode(decodedText);
        if (!qrCode) {
            setStatus('QR Code tidak memuat kode fasilitas yang valid.');
            return;
        }

        isRedirecting = true;
        stopNativeCamera();
        stopHtml5Scanner();
        setStatus('QR terdeteksi, membuka formulir pengaduan…');
        window.location.href = qrReportRouteTemplate.replace('__QR_CODE__', encodeURIComponent(qrCode));
    }

    function getQrBoxSize() {
        const width = Math.max(240, Math.min(readerEl?.clientWidth || 280, 360));
        const size = Math.round(width * 0.72);
        return { width: size, height: size };
    }

    async function startWithHtml5Qrcode() {
        clearReader();
        html5Scanner = new Html5Qrcode('reader', { verbose: false });

        const config = {
            fps: 12,
            qrbox: getQrBoxSize(),
            aspectRatio: 1.0,
            disableFlip: true,
            rememberLastUsedCamera: true,
        };

        let cameraConfig = { facingMode: 'environment' };

        try {
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length) {
                const backCamera = devices.find((device) => /back|rear|environment|belakang/i.test(device.label || ''));
                cameraConfig = (backCamera || devices[devices.length - 1]).id;
            }
        } catch (error) {
            cameraConfig = { facingMode: 'environment' };
        }

        await html5Scanner.start(
            cameraConfig,
            config,
            (decodedText) => goToQrReport(decodedText),
            () => {}
        );
    }

    async function startWithNativeBarcodeDetector() {
        if (!('BarcodeDetector' in window)) {
            throw new Error('BarcodeDetector tidak tersedia di browser ini.');
        }

        clearReader();
        const video = document.createElement('video');
        video.setAttribute('playsinline', 'true');
        video.setAttribute('muted', 'true');
        video.autoplay = true;
        video.style.width = '100%';
        video.style.minHeight = '292px';
        video.style.objectFit = 'cover';
        readerEl.appendChild(video);

        nativeStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: 'environment' },
                width: { ideal: 1280 },
                height: { ideal: 720 },
            },
            audio: false,
        });

        video.srcObject = nativeStream;
        await video.play();

        const detector = new BarcodeDetector({ formats: ['qr_code'] });

        const scanLoop = async () => {
            if (isRedirecting || !nativeStream) return;
            try {
                const barcodes = await detector.detect(video);
                if (barcodes && barcodes.length && barcodes[0].rawValue) {
                    goToQrReport(barcodes[0].rawValue);
                    return;
                }
            } catch (error) {
                console.error(error);
            }
            nativeTimer = window.setTimeout(scanLoop, 250);
        };

        scanLoop();
    }

    async function startCameraScanner() {
        if (!navigator.mediaDevices?.getUserMedia || !isSecureEnoughForLiveCamera()) {
            setStatus('Kamera live diblokir browser karena halaman belum HTTPS. Gunakan Foto / Upload QR atau buka aplikasi melalui HTTPS.');
            showHelp();
            return;
        }

        setStatus('Meminta izin kamera…');

        try {
            stopNativeCamera();
            await stopHtml5Scanner();
            await waitForHtml5Qrcode();

            if (window.Html5Qrcode) {
                await startWithHtml5Qrcode();
            } else {
                await startWithNativeBarcodeDetector();
            }

            setStatus('Kamera aktif. Arahkan ke QR Code fasilitas…');
        } catch (error) {
            console.error(error);
            try {
                await stopHtml5Scanner();
                stopNativeCamera();
                await startWithNativeBarcodeDetector();
                setStatus('Kamera aktif. Arahkan ke QR Code fasilitas…');
                return;
            } catch (nativeError) {
                console.error(nativeError);
            }

            setStatus('Kamera tidak dapat diakses. Pastikan izin kamera diberikan, gunakan Chrome/Safari terbaru, atau gunakan Foto / Upload QR.');
            showHelp();
        }
    }

    async function scanFileWithNativeDetector(file) {
        if (!('BarcodeDetector' in window)) {
            throw new Error('BarcodeDetector tidak tersedia.');
        }
        const detector = new BarcodeDetector({ formats: ['qr_code'] });
        const bitmap = await createImageBitmap(file);
        const barcodes = await detector.detect(bitmap);
        if (barcodes && barcodes.length && barcodes[0].rawValue) {
            return barcodes[0].rawValue;
        }
        throw new Error('QR tidak ditemukan pada foto.');
    }

    function bindFileScanner() {
        if (!fileInput) return;

        fileInput.addEventListener('change', async function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            setStatus('Membaca QR dari foto…');

            try {
                await waitForHtml5Qrcode(1500);
                if (window.Html5Qrcode) {
                    const fileScanner = new Html5Qrcode('reader-file-preview', { verbose: false });
                    const decodedText = await fileScanner.scanFile(file, false);
                    goToQrReport(decodedText);
                    return;
                }

                const decodedText = await scanFileWithNativeDetector(file);
                goToQrReport(decodedText);
            } catch (firstError) {
                console.error(firstError);
                try {
                    const decodedText = await scanFileWithNativeDetector(file);
                    goToQrReport(decodedText);
                } catch (secondError) {
                    console.error(secondError);
                    setStatus('QR tidak terbaca dari foto ini. Coba foto ulang lebih dekat, cahaya cukup, QR tidak miring, dan seluruh QR masuk frame.');
                    showHelp();
                }
            } finally {
                event.target.value = '';
            }
        });
    }

    bindFileScanner();

    window.addEventListener('load', function () {
        startCameraScanner();
    });
})();
</script>
@endsection
