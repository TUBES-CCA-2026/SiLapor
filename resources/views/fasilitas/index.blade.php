@extends('layouts.app')

@section('title', 'Kelola Fasilitas & QR - SiLapor')

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-8">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display font-bold text-2xl text-gray-900">Fasilitas Lab</h1>
            <p class="text-gray-500 text-sm">Setiap fasilitas punya QR unik untuk lapor kerusakan.</p>
        </div>
        <a href="{{ route('laboratorium.index') }}" class="text-sm text-silapor-600 hover:underline">
            Kelola Laboratorium →
        </a>
    </div>

    <form method="POST" action="{{ route('fasilitas.store') }}" class="bg-white border border-gray-100 rounded-2xl p-5 grid sm:grid-cols-4 gap-3">
        @csrf
        <input name="nama_fasilitas" placeholder="Nama fasilitas (cth: PC-01)" required
               class="rounded-xl border border-gray-300 px-3 py-2 text-sm sm:col-span-1">
        <select name="id_laboratorium" required
                class="rounded-xl border border-gray-300 px-3 py-2 text-sm sm:col-span-1">
            <option value="" disabled selected>Pilih Laboratorium</option>
            @foreach ($laboratoriums as $lab)
                <option value="{{ $lab->id_laboratorium }}">{{ $lab->nama_laboratorium }}</option>
            @endforeach
        </select>
        <input name="no_fasilitas" placeholder="Kode aset (opsional)"
               class="rounded-xl border border-gray-300 px-3 py-2 text-sm sm:col-span-1">
        <button class="bg-silapor-500 hover:bg-silapor-600 text-white text-sm font-semibold rounded-xl px-4 py-2">
            + Tambah Fasilitas
        </button>
    </form>

    <div class="grid sm:grid-cols-3 gap-4">
        @foreach ($fasilitas as $f)
            <div class="bg-white border border-gray-100 rounded-2xl p-5 text-center">
                <p class="font-semibold text-gray-900">{{ $f->nama_fasilitas }}</p>
                <p class="text-xs text-gray-500 mb-3">{{ $f->laboratorium->nama_laboratorium }}</p>

                {{-- QR digenerate langsung di browser dari URL scan fasilitas ini --}}
                <div class="flex justify-center mb-3" id="qr-{{ $f->id_fasilitas }}"></div>
                <script>
                    new QRCode(document.getElementById('qr-{{ $f->id_fasilitas }}'), {
                        text: '{{ $f->scanUrl() }}',
                        width: 140,
                        height: 140,
                    });
                </script>

                <form method="POST" action="{{ route('fasilitas.regenerate-qr', $f->id_fasilitas) }}">
                    @csrf
                    <button class="text-xs text-silapor-600 hover:underline">Regenerasi QR</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
