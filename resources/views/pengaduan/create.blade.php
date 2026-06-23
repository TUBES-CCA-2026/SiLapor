@extends('layouts.app')

@section('title', ($mode === 'qr' ? 'Lapor via QR' : 'Pengaduan Manual') . ' - SiLapor')

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

        <div class="flex items-start justify-between gap-4 mb-1">
            <h1 class="font-display font-semibold text-xl text-gray-900">
                {{ $mode === 'qr' ? 'Lapor Kerusakan via QR' : 'Pengaduan Kerusakan Manual' }}
            </h1>
            <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $mode === 'qr' ? 'bg-blue-50 text-blue-700' : 'bg-violet-50 text-violet-700' }}">
                {{ $mode === 'qr' ? 'QR' : 'MANUAL' }}
            </span>
        </div>

        <p class="text-gray-500 text-sm mb-6">
            {{ $mode === 'qr'
                ? 'Data fasilitas dikunci berdasarkan QR Code yang dipindai.'
                : 'Pilih fasilitas yang rusak, lalu lengkapi detail pengaduan.' }}
        </p>

        @if ($errors->any())
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ $mode === 'qr'
                    ? route('pengaduan.qr.store', $fasilitas->qr_code)
                    : route('pengaduan.manual.store') }}"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf

            @if ($mode === 'qr')
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
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fasilitas yang Dilaporkan
                    </label>
                    <select name="id_fasilitas" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
                        <option value="">— Pilih fasilitas —</option>
                        @foreach ($facilities as $item)
                            <option value="{{ $item->id_fasilitas }}"
                                    {{ (string) old('id_fasilitas') === (string) $item->id_fasilitas ? 'selected' : '' }}>
                                {{ $item->nama_fasilitas }}
                                @if ($item->no_fasilitas)
                                    ({{ $item->no_fasilitas }})
                                @endif
                                — {{ $item->laboratorium->nama_laboratorium }}
                            </option>
                        @endforeach
                    </select>

                    @if ($facilities->isEmpty())
                        <p class="text-xs text-red-500 mt-1">Belum ada fasilitas yang dapat dipilih.</p>
                    @endif
                </div>
            @endif

            @if ($isGuest)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Pelapor <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <select name="id_user"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
                        <option value="">— Lapor tanpa nama (anonim) —</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id_user }}"
                                    {{ (string) old('id_user') === (string) $user->id_user ? 'selected' : '' }}>
                                {{ $user->nama }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        Nama hanya dapat dipilih dari pengguna yang sudah terdaftar. Pengaduan anonim tetap diperbolehkan.
                    </p>
                </div>
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

            <button type="submit" {{ $mode === 'manual' && $facilities->isEmpty() ? 'disabled' : '' }}
                    class="w-full bg-silapor-500 hover:bg-silapor-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-xl py-3 transition">
                Kirim Pengaduan {{ $mode === 'qr' ? 'QR' : 'Manual' }}
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-gray-100 text-center text-sm">
            @if ($mode === 'qr')
                <a href="{{ route('pengaduan.manual.create') }}" class="text-silapor-600 font-semibold hover:underline">
                    Buat pengaduan manual
                </a>
            @else
                <a href="{{ route('scan.index') }}" class="text-silapor-600 font-semibold hover:underline">
                    Gunakan scan QR
                </a>
            @endif
        </div>
    </div>
</div>
@endsection