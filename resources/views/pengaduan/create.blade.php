@extends('layouts.app')

@section('title', 'Lapor Kerusakan - SiLapor')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        <div class="flex items-center gap-2 mb-6">
            <img src="{{ asset('images/logo-silapor.png') }}" alt="SiLapor" class="w-9 h-9 rounded-lg object-contain">
            <span class="font-display font-bold text-lg text-gray-900">SiLapor</span>
        </div>

        @if ($isGuest)
            <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-3 flex items-center justify-between gap-3">
                <span>Anda melapor sebagai <strong>Guest</strong> (tanpa login).</span>
                <a href="{{ route('login') }}" class="whitespace-nowrap font-semibold text-silapor-600 hover:underline">
                    Login dulu →
                </a>
            </div>
        @else
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                Melapor sebagai <strong>{{ auth()->user()->nama }}</strong>.
            </div>
        @endif

        <h1 class="font-display font-semibold text-xl text-gray-900 mb-1">Lapor Kerusakan Fasilitas</h1>
        <p class="text-gray-500 text-sm mb-6">Data fasilitas terisi otomatis dari hasil scan QR.</p>

        @if ($errors->any())
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('pengaduan.store', $fasilitas->qr_code) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Auto-filled & readonly, sesuai requirement: user tidak perlu isi manual --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                    <input type="text" value="{{ $fasilitas->nama_fasilitas }}" readonly
                           class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Lab</label>
                    <input type="text" value="{{ $fasilitas->laboratorium->nama_laboratorium }}" readonly
                           class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600">
                </div>
            </div>

            @if ($fasilitas->no_fasilitas)
                <p class="text-xs text-gray-400">Kode aset: {{ $fasilitas->no_fasilitas }}</p>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kerusakan</label>
                <input type="file" name="foto_kerusakan" accept="image/*" capture="environment" required
                       class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2.5 file:mr-3 file:rounded-lg file:border-0 file:bg-silapor-50 file:text-silapor-700 file:px-3 file:py-1.5">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kerusakan</label>
                <textarea name="deskripsi_kerusakan" rows="4" required
                          placeholder="Contoh: Monitor tidak menyala sama sekali sejak pagi ini."
                          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">{{ old('deskripsi_kerusakan') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-silapor-500 hover:bg-silapor-600 text-white font-semibold rounded-xl py-3 transition">
                Kirim Pengaduan
            </button>
        </form>
    </div>
</div>
@endsection
