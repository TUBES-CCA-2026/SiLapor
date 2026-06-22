@extends('layouts.app')

@section('title', 'Pengaduan Terkirim - SiLapor')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4 text-2xl">✓</div>
        <h1 class="font-display font-semibold text-xl text-gray-900 mb-2">Pengaduan Berhasil Dikirim</h1>
        <p class="text-gray-500 text-sm mb-2">
            Laporan kerusakan <strong>{{ $pengaduan->fasilitas->nama_fasilitas }}</strong>
            di <strong>{{ $pengaduan->fasilitas->laboratorium->nama_laboratorium }}</strong> sudah masuk ke sistem
            dan akan ditindaklanjuti oleh tim laboratorium.
        </p>
        <p class="text-gray-400 text-xs mb-6">
            Dilaporkan oleh: {{ $pengaduan->pelapor?->nama ?? 'Anonim (tanpa nama)' }}
        </p>

        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('scan.index') }}"
               class="rounded-xl border border-gray-200 px-4 py-2.5 text-gray-600 text-sm font-semibold hover:bg-gray-50">
                Scan QR
            </a>
            <a href="{{ route('pengaduan.manual.create') }}"
               class="rounded-xl bg-silapor-500 px-4 py-2.5 text-white text-sm font-semibold hover:bg-silapor-600">
                Lapor Manual
            </a>
        </div>
    </div>
</div>
@endsection
