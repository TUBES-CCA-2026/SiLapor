@extends('layouts.app')

@section('title', 'Pengaduan - SiLapor')

@section('content')
<<<<<<< HEAD
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
    $selectedLokasiLab = $selectedLabLocation ? $selectedLabName . ' - ' . $selectedLabLocation : $selectedLabName;
@endphp

<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center gap-2 mb-6">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                <i class="fa-solid fa-square-poll-vertical text-lg"></i>
            </div>
            <span class="font-display font-bold text-lg text-gray-900">SiLapor</span>
        </div>

        @if ($isGuest)
            <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-3 flex items-center justify-between gap-3">
                <span>Anda melapor tanpa login. Nama pelapor wajib dipilih dari user yang sudah terdaftar.</span>
                <a href="{{ route('login') }}" class="whitespace-nowrap font-semibold text-silapor-600 hover:underline">
                    Login dulu →
                </a>
            </div>
        @else
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                Nama pelapor otomatis memakai akun login: <strong>{{ auth()->user()->nama }}</strong>.
            </div>
        @endif
=======
<!-- Import Google Fonts & FontAwesome untuk icon persis seperti Figma -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    <!-- SIDEBAR KIRI (SAMA DENGAN DASHBOARD) -->
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:sticky md:top-0 md:h-screen rounded-r-[36px] shadow-sm shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </div>

            <nav class="mt-10 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-table-columns text-lg"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- MENU PENGADUAN (ACTIVE) -->
                <a href="{{ route('pengaduan') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-regular fa-file-lines text-lg text-[#0090F5]"></i>
                        <span>Pengaduan</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>
>>>>>>> b342d8f (dashboard-pengaduan)

                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    <span>Tindak Lanjut</span>
                </a>
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Riwayat</span>
                </a>
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span>Teknisi</span>
                </a>
                <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-regular fa-user text-lg"></i>
                    <span>Profil</span>
                </a>
            </nav>
        </div>
        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px]">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

<<<<<<< HEAD
        <p class="text-gray-500 text-sm mb-6">
            {{ $mode === 'qr'
                ? 'Kode barang, nama fasilitas, dan lokasi lab otomatis terkunci berdasarkan QR Code yang dipindai.'
                : 'Pilih fasilitas yang rusak. Kode barang, nama fasilitas, dan lokasi lab akan terisi otomatis.' }}
        </p>
<form method="POST"
              action="{{ $mode === 'qr'
                    ? route('pengaduan.qr.store', $fasilitas->qr_code)
                    : route('pengaduan.manual.store') }}"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf

            @if ($isGuest)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Pelapor <span class="text-red-500">*</span>
                    </label>
                    <select name="id_user" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
                        <option value="">— Pilih nama user terdaftar —</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id_user }}"
                                    {{ (string) old('id_user') === (string) $user->id_user ? 'selected' : '' }}>
                                {{ $user->nama }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pengaduan tanpa login tidak bisa anonim. Pilih nama sesuai data user yang sudah terdaftar.</p>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelapor</label>
                    <input type="text" value="{{ auth()->user()->nama }}" readonly
                           class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600">
                </div>
            @endif

            @if ($mode === 'manual')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fasilitas yang Dilaporkan <span class="text-red-500">*</span>
                    </label>
                    <select id="id_fasilitas" name="id_fasilitas" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
                        <option value="">— Pilih fasilitas —</option>
                        @foreach ($facilities as $item)
                            <option value="{{ $item->id_fasilitas }}"
                                    {{ $selectedFacilityId === (string) $item->id_fasilitas ? 'selected' : '' }}>
                                {{ $item->nama_fasilitas }}
                                @if ($item->no_fasilitas)
                                    ({{ $item->no_fasilitas }})
                                @endif
                                — {{ $item->laboratorium?->nama_laboratorium ?? '-' }}
                            </option>
                        @endforeach
                    </select>

                    @if ($facilities->isEmpty())
                        <p class="text-xs text-red-500 mt-1">Belum ada fasilitas yang dapat dipilih.</p>
                    @endif
=======
    <!-- KONTEN UTAMA -->
    <main class="flex-1 px-6 py-6 md:px-10 md:py-8 overflow-x-hidden">
        <header class="flex items-center justify-between pb-8">
            <h1 class="text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Pengaduan</h1>
            <div class="bg-[#0090F5] text-white px-5 py-2.5 rounded-2xl flex items-center gap-3.5 shadow-md">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0090F5] shrink-0">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div class="text-left flex flex-col justify-center leading-tight">
                    <span class="text-[11px] font-light opacity-90 block">Selamat datang,</span>
                    <span class="text-sm font-extrabold block tracking-wide">{{ auth()->user()->nama ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <!-- Form Card (Sesuai Figma) -->
        <div class="bg-white p-8 rounded-[32px] shadow-figma-container border border-gray-150 w-full">
            <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pelapor</label>
                        <select name="id_user" class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]">
                            <option value="">Pilih Pelapor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Masalah</label>
                        <select name="id_lokasi" class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]">
                            <option value="">Pilih Lokasi</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Fasilitas</label>
                    <input type="text" name="fasilitas" class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]" placeholder="Masukkan nama fasilitas">
>>>>>>> b342d8f (dashboard-pengaduan)
                </div>

<<<<<<< HEAD
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang</label>
                    <input id="kode_barang" type="text" value="{{ $selectedKodeBarang }}" readonly
                           class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Fasilitas</label>
                    <input id="nama_fasilitas" type="text" value="{{ $selectedNamaFasilitas }}" readonly
                           class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Lab</label>
                    <input id="lokasi_lab" type="text" value="{{ $selectedLokasiLab }}" readonly
                           class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-gray-600">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kerusakan <span class="text-red-500">*</span></label>
                <input type="file" name="foto_kerusakan" accept="image/*" capture="environment" required
                       class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2.5 file:mr-3 file:rounded-lg file:border-0 file:bg-silapor-50 file:text-silapor-700 file:px-3 file:py-1.5">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
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
=======
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi kerusakan</label>
                    <textarea name="deskripsi" rows="5" class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload foto kerusakan</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center bg-[#F8FAFC] cursor-pointer hover:border-[#0090F5] transition-colors">
                        <input type="file" name="foto" class="hidden" id="fileInput">
                        <label for="fileInput" class="flex flex-col items-center cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-[#0090F5] mb-2"></i>
                            <span class="text-[#0090F5] font-bold">Upload foto</span>
                            <span class="text-xs text-gray-400 mt-1">klik untuk memilih foto format: jpg/png</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <button type="submit" class="w-full bg-[#0090F5] hover:bg-[#007cd5] text-white font-extrabold py-4 rounded-2xl shadow-md transition-all">
                        Selesai
                    </button>
                    
                    <a href="{{ route('scan.index') }}" class="flex items-center justify-center gap-2 text-gray-500 hover:text-[#0090F5] transition-colors font-medium text-sm">
                        <i class="fa-solid fa-qrcode"></i>
                        Gunakan QR Code untuk pelaporan instan
                    </a>
                </div>
            </form>
>>>>>>> b342d8f (dashboard-pengaduan)
        </div>
    </main>
</div>
<<<<<<< HEAD

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
=======
@endsection
>>>>>>> b342d8f (dashboard-pengaduan)
