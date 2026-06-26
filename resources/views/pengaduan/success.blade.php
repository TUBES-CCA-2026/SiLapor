@extends('layouts.app')

@section('title', 'Pengaduan Terkirim - SiLapor')

@section('content')
@php
    $returnUrl = route('login');
    $returnLabel = 'kembali ke halaman login';

    if (auth()->check()) {
        if (auth()->user()->isAsisten()) {
            $returnUrl = route('pengaduan.index');
            $returnLabel = 'kembali ke halaman pengaduan';
        } else {
            $returnUrl = route('dashboard');
            $returnLabel = 'kembali ke dashboard';
        }
    }
@endphp
<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="w-full max-w-md text-center">
        <button type="button" onclick="window.location='{{ $returnUrl }}'" class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-silapor-500">
            <div class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4 text-2xl">✓</div>
            <p class="text-gray-500 text-sm mb-2">
                Laporan kerusakan <strong>{{ $pengaduan->fasilitas->nama_fasilitas }}</strong>
                di <strong>{{ $pengaduan->fasilitas->laboratorium->nama_laboratorium }}</strong> sudah masuk ke sistem
                dan akan ditindaklanjuti oleh tim laboratorium.
            </p>
            <p class="text-gray-400 text-xs">
                Dilaporkan oleh: {{ $pengaduan->pelapor?->nama ?? 'Anonim (tanpa nama)' }}
            </p>
        </button>

        <div class="mt-4 rounded-2xl bg-green-50 border border-green-200 px-4 py-3 text-green-700 text-sm font-semibold">
            Notifikasi: pengaduan berhasil dikirim. Tekan pop-up di atas untuk {{ $returnLabel }}.
        </div>

        <div class="flex flex-col sm:flex-row gap-3 mt-6">
            @if (auth()->check() && auth()->user()->isAsisten())
                <a href="{{ route('pengaduan.index') }}" class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-gray-600 text-sm font-semibold hover:bg-white">
                    Pengaduan
                </a>
            @endif

            <a href="{{ route('scan.index') }}" class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-gray-600 text-sm font-semibold hover:bg-white">
                Scan QR
            </a>
            <a href="{{ route('pengaduan.manual.create') }}" class="flex-1 rounded-xl bg-silapor-500 px-4 py-2.5 text-white text-sm font-semibold hover:bg-silapor-600">
                Lapor Manual
            </a>
        </div>
    </div>
</div>
@endsection
