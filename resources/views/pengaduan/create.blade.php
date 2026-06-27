@extends('layouts.app')

@section('title', 'Pengaduan - SiLapor')

@section('content')
@php
    $selectedFacilityId = $mode === 'qr'
        ? (string) $fasilitas->id_fasilitas
        : (string) old('id_fasilitas', '');

    $selectedFacility = $mode === 'qr'
        ? $fasilitas
        : $facilities->firstWhere('id_fasilitas', old('id_fasilitas'));

    $selectedKodeBarang = $selectedFacility?->no_fasilitas ?: '-';
    $selectedNamaFasilitas = $selectedFacility?->nama_fasilitas ?: '-';
    $selectedLabName = $selectedFacility?->laboratorium?->nama_laboratorium ?: '-';
    $selectedLabLocation = $selectedFacility?->laboratorium?->lokasi;
    $selectedLokasiLab = $selectedLabLocation
        ? $selectedLabName . ' - ' . $selectedLabLocation
        : $selectedLabName;

    $backUrl = null;
    $backLabel = 'Kembali';

    if (auth()->check()) {
        if (auth()->user()->isAsisten()) {
            $backUrl = route('pengaduan.index');
            $backLabel = 'Kembali ke Halaman Pengaduan';
        } else {
            $backUrl = route('dashboard');
            $backLabel = 'Kembali ke Dashboard';
        }
    } else {
        $backUrl = route('login');
        $backLabel = 'Kembali ke Login';
    }
@endphp

<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center justify-between gap-3 mb-6">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-lg"></i>
                </div>
                <span class="font-display font-bold text-lg text-gray-900">
                    SiLapor
                </span>
            </div>

        </div>

        @if ($isGuest)
            <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-3 flex items-center justify-between gap-3">
                <span>
                    Anda melapor tanpa login. Nama pelapor wajib dipilih dari user yang sudah terdaftar.
                </span>

                <a href="{{ route('login') }}" class="whitespace-nowrap font-semibold text-silapor-600 hover:underline">
                    Login dulu →
                </a>
            </div>
        @else
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                Nama pelapor otomatis memakai akun login:
                <strong>{{ auth()->user()->nama }}</strong>.
            </div>
        @endif

        <p class="text-gray-500 text-sm mb-6">
            {{ $mode === 'qr'
                ? 'Kode barang, nama fasilitas, dan lokasi lab otomatis terkunci berdasarkan QR Code yang dipindai.'
                : 'Pilih fasilitas yang rusak. Kode barang, nama fasilitas, dan lokasi lab akan terisi otomatis.' }}
        </p>

        <form
            method="POST"
            action="{{ $mode === 'qr'
                ? route('pengaduan.qr.store', $fasilitas->qr_code)
                : route('pengaduan.manual.store') }}"
            enctype="multipart/form-data"
            class="space-y-5"
        >
            @csrf

            @if ($isGuest)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Pelapor <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="id_user"
                        required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500"
                    >
                        <option value="">— Pilih nama user terdaftar —</option>

                        @foreach ($users as $user)
                            <option
                                value="{{ $user->id_user }}"
                                {{ (string) old('id_user') === (string) $user->id_user ? 'selected' : '' }}
                            >
                                {{ $user->nama }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>

                    <p class="text-xs text-gray-400 mt-1">
                        Pengaduan tanpa login tidak bisa anonim. Pilih nama sesuai data user yang sudah terdaftar.
                    </p>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Pelapor
                    </label>

                    <input
                        type="text"
                        value="{{ auth()->user()->nama }}"
                        readonly
                        class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600"
                    >
                </div>
            @endif

            @if ($mode === 'manual')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fasilitas yang Dilaporkan <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="id_fasilitas"
                        name="id_fasilitas"
                        required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500"
                    >
                        <option value="">— Pilih fasilitas —</option>

                        @foreach ($facilities as $item)
                            <option
                                value="{{ $item->id_fasilitas }}"
                                {{ $selectedFacilityId === (string) $item->id_fasilitas ? 'selected' : '' }}
                            >
                                {{ $item->nama_fasilitas }}
                                @if ($item->no_fasilitas)
                                    ({{ $item->no_fasilitas }})
                                @endif
                                — {{ $item->laboratorium?->nama_laboratorium ?? '-' }}
                            </option>
                        @endforeach
                    </select>

                    @if ($facilities->isEmpty())
                        <p class="text-xs text-red-500 mt-1">
                            Belum ada fasilitas yang dapat dipilih.
                        </p>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kode Barang
                    </label>

                    <input
                        id="kode_barang"
                        type="text"
                        value="{{ $selectedKodeBarang }}"
                        readonly
                        class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Fasilitas
                    </label>

                    <input
                        id="nama_fasilitas"
                        type="text"
                        value="{{ $selectedNamaFasilitas }}"
                        readonly
                        class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Lokasi Lab
                    </label>

                    <input
                        id="lokasi_lab"
                        type="text"
                        value="{{ $selectedLokasiLab }}"
                        readonly
                        class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600"
                    >
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Foto Kerusakan <span class="text-red-500">*</span>
                </label>

                <input
                    type="file"
                    name="foto_kerusakan"
                    accept="image/*"
                    capture="environment"
                    required
                    class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2.5 file:mr-3 file:rounded-lg file:border-0 file:bg-silapor-50 file:text-silapor-700 file:px-3 file:py-1.5"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi Kerusakan <span class="text-red-500">*</span>
                </label>

                <textarea
                    name="deskripsi_kerusakan"
                    rows="4"
                    required
                    placeholder="Contoh: Monitor tidak menyala sama sekali sejak pagi ini."
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500"
                >{{ old('deskripsi_kerusakan') }}</textarea>
            </div>

            <button
                type="submit"
                {{ $mode === 'manual' && $facilities->isEmpty() ? 'disabled' : '' }}
                class="w-full bg-silapor-500 hover:bg-silapor-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-xl py-3 transition"
            >
                Kirim Pengaduan {{ $mode === 'qr' ? 'QR' : 'Manual' }}
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-gray-100 flex flex-col sm:flex-row gap-3 text-center text-sm">
            @if ($backUrl)
                <a href="{{ $backUrl }}" class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-gray-600 font-semibold transition hover:bg-gray-50">
                    {{ $backLabel }}
                </a>
            @endif

            @if ($mode === 'qr')
                <a href="{{ auth()->check() && auth()->user()?->isAsisten() ? route('pengaduan.index') : route('pengaduan.manual.create') }}" class="flex-1 rounded-xl bg-silapor-50 px-4 py-2.5 text-silapor-700 font-semibold transition hover:bg-silapor-100">
                    Buat pengaduan manual
                </a>
            @else
                <a href="{{ route('scan.index') }}" class="flex-1 rounded-xl bg-silapor-50 px-4 py-2.5 text-silapor-700 font-semibold transition hover:bg-silapor-100">
                    Gunakan scan QR
                </a>
            @endif
        </div>
    </div>
</div>

<script>
    const facilities = @json($facilityPayload);
    const facilityMap = Object.fromEntries(facilities.map((item) => [String(item.id), item]));

    function fillFacilityDetail(id) {
        const detail = facilityMap[String(id)] || null;

        document.getElementById('kode_barang').value = detail?.kode_barang || '-';
        document.getElementById('nama_fasilitas').value = detail?.nama_fasilitas || '-';
        document.getElementById('lokasi_lab').value = detail?.lokasi_lab || '-';
    }

    const facilitySelect = document.getElementById('id_fasilitas');

    if (facilitySelect) {
        facilitySelect.addEventListener('change', function () {
            fillFacilityDetail(this.value);
        });

        fillFacilityDetail(facilitySelect.value);
    } else {
        fillFacilityDetail(@json($selectedFacilityId));
    }
</script>
@endsection