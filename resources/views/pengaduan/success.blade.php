@extends('layouts.app')

@section('title', 'Pengaduan Terkirim - SiLapor')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4 text-2xl">✓</div>
        <h1 class="font-display font-semibold text-xl text-gray-900 mb-2">Pengaduan Berhasil Dikirim</h1>
        <p class="text-gray-500 text-sm mb-6">
            Laporan kerusakan <strong>{{ $pengaduan->fasilitas->nama_fasilitas }}</strong>
            di <strong>{{ $pengaduan->fasilitas->laboratorium->nama_laboratorium }}</strong> sudah masuk ke sistem
            dan akan ditindaklanjuti oleh tim laboratorium.
        </p>
        <a href="{{ route('scan.index') }}" class="text-silapor-600 text-sm font-semibold hover:underline">
            ← Lapor fasilitas lain
        </a>
    </div>
</div>
@endsection
