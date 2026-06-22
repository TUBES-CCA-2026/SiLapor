@extends('layouts.app')

@section('title', 'Kelola Laboratorium - SiLapor')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-8">

    <div>
        <h1 class="font-display font-bold text-2xl text-gray-900">Data Laboratorium</h1>
        <p class="text-gray-500 text-sm">
            Daftar laboratorium yang ada. Tambahkan dulu di sini sebelum bisa dipilih
            saat menambah fasilitas di <a href="{{ route('fasilitas.index') }}" class="text-silapor-600 hover:underline">halaman Fasilitas</a>.
        </p>
    </div>

    <form method="POST" action="{{ route('laboratorium.store') }}" class="bg-white border border-gray-100 rounded-2xl p-5 grid sm:grid-cols-3 gap-3">
        @csrf
        <input name="nama_laboratorium" placeholder="Nama lab (cth: Lab RPL)" required
               class="rounded-xl border border-gray-300 px-3 py-2 text-sm">
        <input name="kode_laboratorium" placeholder="Kode (cth: LAB-RPL)"
               class="rounded-xl border border-gray-300 px-3 py-2 text-sm">
        <input name="lokasi" placeholder="Lokasi (cth: Gedung A Lantai 2)"
               class="rounded-xl border border-gray-300 px-3 py-2 text-sm">

        <select name="id_koordinator"
                class="rounded-xl border border-gray-300 px-3 py-2 text-sm">
            <option value="">Koordinator (opsional)</option>
            @foreach ($koordinators as $k)
                <option value="{{ $k->id_user }}">{{ $k->nama }}</option>
            @endforeach
        </select>
        <input type="number" name="kapasitas" placeholder="Kapasitas unit (opsional)" min="0"
               class="rounded-xl border border-gray-300 px-3 py-2 text-sm">
        <button class="bg-silapor-500 hover:bg-silapor-600 text-white text-sm font-semibold rounded-xl px-4 py-2">
            + Tambah Laboratorium
        </button>
    </form>

    <div class="bg-white border border-gray-100 rounded-2xl divide-y divide-gray-100">
        @forelse ($laboratoriums as $lab)
            <div class="px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-900">
                        {{ $lab->nama_laboratorium }}
                        @if ($lab->kode_laboratorium)
                            <span class="text-xs font-normal text-gray-400">({{ $lab->kode_laboratorium }})</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-500">{{ $lab->lokasi ?? '—' }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Koordinator: {{ $lab->koordinator?->nama ?? 'Belum ditentukan' }}
                        @if ($lab->kapasitas) · {{ $lab->kapasitas }} unit @endif
                    </p>
                </div>
                <span class="text-xs text-gray-400">{{ $lab->fasilitas()->count() }} fasilitas</span>
            </div>
        @empty
            <p class="px-5 py-6 text-gray-400 text-sm">Belum ada data laboratorium.</p>
        @endforelse
    </div>
</div>
@endsection
