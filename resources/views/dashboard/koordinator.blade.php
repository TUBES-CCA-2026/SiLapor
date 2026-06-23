@extends('layouts.app')

@section('title', 'Dashboard Koordinator - SiLapor')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-10">

    <div>
        <h1 class="font-display font-bold text-2xl text-gray-900">Pengaduan Masuk</h1>
        <p class="text-gray-500 text-sm">Tugaskan setiap pengaduan baru ke salah satu asisten untuk diperbaiki.</p>
    </div>

    <div class="space-y-3">
        @forelse ($pengaduanBaru as $p)
            <div class="bg-white border border-gray-100 rounded-2xl p-5 flex gap-4">
                @if ($p->foto_kerusakan)
                    <img src="{{ asset('storage/'.$p->foto_kerusakan) }}" alt="Foto kerusakan"
                         class="w-20 h-20 rounded-xl object-cover shrink-0 bg-gray-100">
                @endif

                <div class="flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $p->fasilitas->nama_fasilitas }}</p>
                            <p class="text-sm text-gray-500">{{ $p->fasilitas->laboratorium->nama_laboratorium }}</p>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-red-100 text-red-700 whitespace-nowrap">
                            NEW
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 mt-2">{{ $p->deskripsi_kerusakan }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Dilapor oleh {{ $p->pelapor?->nama ?? 'Anonim' }} · {{ $p->tanggal_lapor->format('d M Y') }}
                    </p>

                    <form method="POST" action="{{ route('tindak-lanjut.assign', $p->id_pengaduan) }}" class="mt-4 flex gap-2">
                        @csrf
                        <select name="id_asisten" required
                                class="flex-1 text-sm rounded-xl border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-silapor-500">
                            <option value="" disabled selected>Pilih asisten…</option>
                            @foreach ($asisten as $a)
                                <option value="{{ $a->id_user }}">{{ $a->nama }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="bg-silapor-500 hover:bg-silapor-600 text-white text-sm font-semibold rounded-xl px-4 py-2 whitespace-nowrap">
                            Tugaskan
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm">Tidak ada pengaduan baru saat ini. 🎉</p>
        @endforelse
    </div>

    <div>
        <h2 class="font-display font-bold text-lg text-gray-900 mb-3">Sedang Ditangani / Selesai</h2>
        <div class="bg-white border border-gray-100 rounded-2xl divide-y divide-gray-100">
            @forelse ($pengaduanDitangani as $p)
                <div class="px-5 py-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $p->fasilitas->nama_fasilitas }}</p>
                        <p class="text-xs text-gray-500">{{ $p->fasilitas->laboratorium->nama_laboratorium }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            Ditugaskan ke: {{ $p->tindakLanjut?->asisten?->nama ?? '—' }}
                        </p>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap
                        {{ $p->status_pengaduan === 'DONE' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $p->status_pengaduan }}
                    </span>
                </div>
            @empty
                <p class="px-5 py-6 text-gray-400 text-sm">Belum ada pengaduan yang ditangani.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
