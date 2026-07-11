@extends('layouts.app')

@section('title', 'Pengaduan Terkirim - SiLapor')

@section('content')
@php
    $user = auth()->user();

    if ($user?->isAsisten() && \Illuminate\Support\Facades\Route::has('pengaduan.index')) {
        $primaryUrl = route('pengaduan.index');
        $primaryLabel = 'Kembali ke Pengaduan';
    } elseif (auth()->check() && \Illuminate\Support\Facades\Route::has('dashboard')) {
        $primaryUrl = route('dashboard');
        $primaryLabel = 'Kembali ke Dashboard';
    } else {
        $primaryUrl = route('scan.index');
        $primaryLabel = 'Scan QR Lagi';
    }

    $manualUrl = auth()->check() && $user?->isAsisten() && \Illuminate\Support\Facades\Route::has('pengaduan.index')
        ? route('pengaduan.index')
        : route('pengaduan.manual.create');

    $loginOrDashboardUrl = auth()->check() && \Illuminate\Support\Facades\Route::has('dashboard')
        ? route('dashboard')
        : route('login');

    $loginOrDashboardLabel = auth()->check()
        ? 'Kembali ke Dashboard'
        : 'Kembali ke Login';
@endphp

<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="w-full max-w-md text-center">
        <div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
            <div class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4 text-2xl">✓</div>

            <h1 class="font-display font-bold text-xl text-gray-900 mb-3">
                Pengaduan Berhasil Dikirim
            </h1>

            <p class="text-gray-500 text-sm mb-2">
                Laporan kerusakan
                <strong>{{ $pengaduan->fasilitas?->kategori?->nama_kategori ?? '-' }} ({{ $pengaduan->fasilitas?->no_fasilitas ?? '-' }})</strong>
                di
                <strong>{{ $pengaduan->fasilitas?->laboratorium?->nama_laboratorium ?? '-' }}</strong>
                sudah masuk ke sistem dan akan ditindaklanjuti oleh tim laboratorium.
            </p>

            <p class="text-gray-400 text-xs">
                Dilaporkan oleh: {{ $pengaduan->pelapor?->nama ?? 'Anonim (tanpa nama)' }}
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-6">
            <a href="{{ $primaryUrl }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-gray-600 text-sm font-semibold hover:bg-white">
                {{ $primaryLabel }}
            </a>

            <a href="{{ $manualUrl }}" class="rounded-xl bg-silapor-500 px-4 py-2.5 text-white text-sm font-semibold hover:bg-silapor-600">
                Lapor Manual
            </a>
        </div>

        <a href="{{ $loginOrDashboardUrl }}" class="inline-flex mt-4 text-sm font-semibold text-silapor-600 hover:underline">
            {{ $loginOrDashboardLabel }}
        </a>
    </div>
</div>
@endsection
