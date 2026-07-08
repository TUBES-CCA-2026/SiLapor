@extends('layouts.app')

@section('title', 'Profil - SiLapor')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $user = $user ?? auth()->user();
    $user->loadMissing('profile', 'roleData', 'laboratoriumPenanggungJawab');
    $activeMenu = 'profil';

    $routeSafe = function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };

    $menuItems = \App\Support\SidebarMenu::forRole($user?->role);

    $roleLabel = match($user?->role) {
        'kepala_lab' => 'Kepala Lab',
        'koordinator_lab' => 'Koordinator Lab',
        'laboran' => 'Laboran',
        'asisten' => 'Asisten Lab',
        default => 'User',
    };

    $nameFieldLabel = match($user?->role) {
        'kepala_lab' => 'Nama Kepala Lab',
        'koordinator_lab' => 'Nama Koordinator Lab',
        'laboran' => 'Nama Laboran',
        'asisten' => 'Nama Asisten Lab',
        default => 'Nama User',
    };



    $avatarUrl = $user?->profile_photo_url
        ?? ('https://ui-avatars.com/api/?name=' . urlencode($user?->nama ?? 'User') . '&background=F3F4F6&color=9CA3AF');

    $laboratoriumPj = $user?->laboratoriumPenanggungJawab ?? collect();
    $penanggungJawabLabel = $laboratoriumPj->pluck('nama_laboratorium')->filter()->values()->join(', ');
    $penanggungJawabLabel = $penanggungJawabLabel ?: ($user->profile?->penanggung_jawab ?: '-');

    $laboratoriumPendamping = $user?->laboratoriumPendamping ?? collect();
    $pendampingLabel = $laboratoriumPendamping->pluck('nama_laboratorium')->filter()->values()->join(', ');
    $pendampingLabel = $pendampingLabel ?: '-';
@endphp

@once
<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #F1F5F9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
    [x-cloak] { display: none !important; }

    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; }
        .hide-on-desktop { display: none !important; }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row" x-data="{ openEdit: {{ (($errors->any() && old('_form') === 'profile') || session('profile_form') === 'profile') ? 'true' : 'false' }}, openPassword: {{ (($errors->any() && old('_form') === 'password') || session('profile_form') === 'password') ? 'true' : 'false' }} }">
    @include('partials.sidebar', ['user' => auth()->user(), 'activeMenu' => 'profil'])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6 overflow-x-hidden">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-tight uppercase">PROFIL</h1>
            </div>

            @include('partials.user-welcome-box', ['user' => $user])
        </header>



        @if($errors->any() && old('_form') !== 'password')
            <div class="rounded-2xl border border-red-200 bg-red-50 text-red-700 px-5 py-4 text-sm font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white border border-gray-100 rounded-[36px] p-8 shadow-figma-container">
            <div class="flex flex-col sm:flex-row justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-xl font-extrabold text-[#2C3E50]">Profil Pengguna</h2>
                    <p class="text-sm text-gray-500 mt-1">Foto profil, identitas, dan akses akun berlaku untuk semua role.</p>
                </div>
                <button @click="openEdit = true" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-50 shadow-sm transition-all flex items-center gap-2">
                    Edit Profil <i class="fa-solid fa-pen text-xs"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-[28px] border border-gray-100">
                    <div class="w-32 h-32 bg-white rounded-[32px] overflow-hidden flex items-center justify-center text-gray-400 mb-4 border-2 border-gray-100 shadow-inner">
                        <img src="{{ $avatarUrl }}" alt="Foto Profil" class="w-full h-full object-cover rounded-[24px]">
                    </div>
                    <span class="px-4 py-1.5 bg-[#0090F5] text-white text-xs font-bold rounded-full uppercase tracking-wider">
                        {{ $roleLabel }}
                    </span>
                </div>

                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ $nameFieldLabel }}</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">{{ $user->nama ?? '-' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">{{ $user->email ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">No. HP</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">{{ $user->phone ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Role</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">{{ $roleLabel }}</div>
                    </div>

                    @if($user->role === 'asisten')
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">NIM</label>
                            <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">{{ $user->profile?->nim ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Jurusan</label>
                            <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">{{ $user->profile?->jurusan ?? '-' }}</div>
                        </div>
                        @if($laboratoriumPj->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Penanggung Jawab</label>
                            <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">{{ $penanggungJawabLabel }}</div>
                        </div>
                        @endif
                        @if($laboratoriumPendamping->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pendamping</label>
                            <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">{{ $pendampingLabel }}</div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                <button @click="openPassword = true" class="px-6 py-3 bg-[#034C5F] text-white font-bold rounded-2xl hover:bg-[#023a48] transition-all shadow-lg flex items-center gap-2">
                    Ubah Password <i class="fa-solid fa-gear"></i>
                </button>
            </div>
        </div>
    </main>

    <div x-show="openEdit" class="fixed inset-0 z-[60] flex items-center justify-center p-3 sm:p-4 bg-black/40 backdrop-blur-sm" x-cloak>
        <div @click.away="openEdit = false" class="bg-white rounded-[28px] sm:rounded-[40px] shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto" x-show="openEdit" x-transition>
            <div class="flex items-center justify-between px-5 sm:px-8 py-5 sm:py-6 border-b border-gray-50">
                <h3 class="text-lg font-extrabold text-[#2C3E50]">Edit Profil</h3>
                <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 transition-colors" type="button">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="{{ $routeSafe('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-8 space-y-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="profile">



                @if($errors->any() && old('_form') === 'profile')
                    <div class="rounded-2xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm font-semibold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex flex-col items-center mb-6">
                    <div class="w-24 h-24 bg-gray-50 rounded-[24px] overflow-hidden border-2 border-gray-100 mb-3 flex items-center justify-center text-gray-400 shadow-inner">
                        <img id="preview-foto" src="{{ $avatarUrl }}" alt="Preview Foto" class="w-full h-full object-cover rounded-[24px]">
                    </div>
                    <label class="cursor-pointer bg-white border border-gray-200 px-4 py-1.5 rounded-xl text-[11px] font-bold text-gray-600 hover:bg-gray-50 transition-all shadow-sm">
                        Ganti Profil
                        <input type="file" name="foto" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">{{ $nameFieldLabel }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->nama) }}" required class="w-full mt-1.5 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#0090F5] font-medium text-sm text-gray-800">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full mt-1.5 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#0090F5] font-medium text-sm text-gray-800">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">No Hp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->phone) }}" class="w-full mt-1.5 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#0090F5] font-medium text-sm text-gray-800">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Role</label>
                        <input type="text" value="{{ $roleLabel }}" disabled class="w-full mt-1.5 p-3.5 bg-gray-100 border border-gray-200 rounded-2xl font-medium text-sm text-gray-400 cursor-not-allowed">
                    </div>

                    @if($user->role === 'asisten')
                        <div class="sm:col-span-1">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">NIM</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" name="nim" value="{{ old('nim', $user->profile?->nim) }}" class="w-full mt-1.5 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#0090F5] font-medium text-sm text-gray-800" maxlength="11">
                        </div>
                        <div class="sm:col-span-1">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jurusan</label>
                            <input type="text" name="jurusan" value="{{ old('jurusan', $user->profile?->jurusan) }}" class="w-full mt-1.5 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#0090F5] font-medium text-sm text-gray-800">
                        </div>
                        @if($laboratoriumPj->isNotEmpty())
                        <div class="sm:col-span-1">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Penanggung Jawab</label>
                            <div class="w-full mt-1.5 p-3.5 bg-gray-100 border border-gray-200 rounded-2xl font-medium text-sm text-gray-600">
                                {{ $penanggungJawabLabel }}
                            </div>
                        </div>
                        @endif
                        @if($laboratoriumPendamping->isNotEmpty())
                        <div class="sm:col-span-1">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pendamping</label>
                            <div class="w-full mt-1.5 p-3.5 bg-gray-100 border border-gray-200 rounded-2xl font-medium text-sm text-gray-600">
                                {{ $pendampingLabel }}
                            </div>
                        </div>
                        @endif
                        @if($laboratoriumPj->isNotEmpty() || $laboratoriumPendamping->isNotEmpty())
                        <div class="col-span-2">
                            <p class="text-[11px] text-gray-400 mt-1 ml-1">Relasi penanggung jawab & pendamping laboratorium diatur dari menu Laboratorium pada role Laboran atau Koordinator.</p>
                        </div>
                        @endif
                    @endif
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-white border-2 border-gray-800 text-gray-800 font-extrabold rounded-2xl hover:bg-gray-800 hover:text-white transition-all flex items-center justify-center gap-3 shadow-sm">
                        Simpan <i class="fa-solid fa-floppy-disk text-sm"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openPassword" class="fixed inset-0 z-[60] flex items-center justify-center p-3 sm:p-4 bg-black/40 backdrop-blur-sm" x-cloak>
        <div @click.away="openPassword = false" class="bg-white rounded-[28px] sm:rounded-[32px] shadow-2xl w-full max-w-md max-h-[92vh] overflow-y-auto p-5 sm:p-8 border border-gray-100" x-show="openPassword" x-transition>
            <div class="flex items-center justify-center pb-6 relative">
                <h3 class="text-sm font-bold text-gray-800 tracking-wide">Ubah Password</h3>
                <button @click="openPassword = false" class="text-gray-400 hover:text-gray-600 transition-colors absolute right-0" type="button">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ $routeSafe('profile.password.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="password">



                @if($errors->any() && old('_form') === 'password')
                    <div class="rounded-2xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm font-semibold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="text-[11px] font-semibold text-gray-700 block mb-1.5">Password Lama</label>
                    <input type="password" name="current_password" required class="w-full p-2.5 bg-white border border-gray-400 rounded-xl focus:outline-none focus:border-[#0090F5] text-sm text-gray-800">
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-gray-700 block mb-1.5">Password Baru</label>
                    <input type="password" name="password" required class="w-full p-2.5 bg-white border border-gray-400 rounded-xl focus:outline-none focus:border-[#0090F5] text-sm text-gray-800">
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-gray-700 block mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required class="w-full p-2.5 bg-white border border-gray-400 rounded-xl focus:outline-none focus:border-[#0090F5] text-sm text-gray-800">
                </div>

                <div class="pt-4 flex justify-start">
                    <button type="submit" class="px-6 py-2 bg-white border border-gray-700 text-gray-800 font-bold text-xs rounded-xl hover:bg-gray-800 hover:text-white transition-all flex items-center gap-4 shadow-sm">
                        <span>Simpan</span>
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-foto').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
