@extends('layouts.app')

<<<<<<< HEAD
@section('title', ($mode === 'qr' ? 'Lapor via QR' : 'Pengaduan Manual') . ' - SiLapor')
=======
@section('title', 'Pengaduan - SiLapor')
>>>>>>> aee1dc517f209b604b089a603900e052841ba59b

@section('content')
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
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi kerusakan</label>
                    <textarea name="deskripsi" rows="5" class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#0090F5] bg-[#F8FAFC]"></textarea>
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
        </div>
    </main>
</div>
@endsection