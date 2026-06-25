@extends('layouts.app')

@section('title', 'Profil - SiLapor')

@section('content')
{{-- Core Alpine.js untuk handle state Open/Close Modal --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.05); }
    [x-cloak] { display: none !important; }
</style>

{{-- Inisialisasi Alpine.js state: openEdit dan openPassword --}}
<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row" x-data="{ openEdit: false, openPassword: false }">
    
    {{-- Sidebar (Konsisten dengan halaman Laporan) --}}
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

                <a href="{{ route('laporan.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                    <span>Laporan</span>
                </a>

                <a href="{{ route('riwayat.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Riwayat</span>
                </a>

                <a href="{{ route('rekapsulasi.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                    <span>Rekapsulasi</span>
                </a>

                {{-- Menu Profil (Aktif) --}}
                <a href="{{ route('profil.index') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                    <div class="flex items-center gap-3.5">
                        <i class="fa-regular fa-user text-lg text-[#0090F5]"></i>
                        <span>Profil</span>
                    </div>
                    <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
                </a>
            </nav>
        </div>

        <div class="p-8 border-t border-gray-100 bg-white rounded-br-[36px]">
            <a href="#" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    {{-- Main Content --}}
    <main class="flex-1 px-6 py-6 md:px-10 md:py-8 space-y-6 overflow-x-hidden">
        
        <header class="flex items-center justify-between pb-2">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 md:hidden">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
            <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase font-figma">PROFIL</h1>
            
            {{-- Tombol Trigger Modal Edit Profil --}}
            <button @click="openEdit = true" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-50 shadow-sm transition-all flex items-center gap-2">
                Edit Profil <i class="fa-solid fa-pen text-xs"></i>
            </button>
        </header>

        {{-- Flash Message Success --}}
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Flash Message Error / Validasi Gagal (Penting jika ganti password salah/tidak sinkron) --}}
        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl font-bold text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <p><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Main Card --}}
        <div class="bg-white border border-gray-150 rounded-[36px] p-8 shadow-figma-container">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Avatar Section --}}
                <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-[28px] border border-gray-100">
                    <div class="w-32 h-32 bg-white rounded-[32px] overflow-hidden flex items-center justify-center text-gray-400 mb-4 border-2 border-gray-100 shadow-inner">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-user text-5xl"></i>
                        @endif
                    </div>
                    <span class="px-4 py-1.5 bg-[#0090F5] text-white text-xs font-bold rounded-full uppercase tracking-wider">
                        {{ Auth::user()->role ?? 'Kepala Lab' }}
                    </span>
                </div>

                {{-- Form Section --}}
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Kepala LAB</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">
                            {{ Auth::user()->name ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ID Kepala LAB</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">
                            {{ Auth::user()->id ?? '-' }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">
                            {{ Auth::user()->email ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">No. HP</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">
                            {{ Auth::user()->no_hp ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Role</label>
                        <div class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-800 font-semibold">
                            {{ Auth::user()->role ?? 'Kepala Lab' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action --}}
            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                {{-- Memicu modal ganti password via Alpine --}}
                <button @click="openPassword = true" class="px-6 py-3 bg-[#034C5F] text-white font-bold rounded-2xl hover:bg-[#023a48] transition-all shadow-lg flex items-center gap-2">
                    Ubah Password <i class="fa-solid fa-gear"></i>
                </button>
            </div>
        </div>
    </main>

    {{-- MODAL POPUP EDIT PROFIL --}}
    <div x-show="openEdit" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" x-cloak>
        
        <div @click.away="openEdit = false" 
             class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg overflow-hidden transform transition-all"
             x-show="openEdit"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-50">
                <h3 class="text-lg font-extrabold text-[#2C3E50]">Edit Profil</h3>
                <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Modal Form --}}
            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-5">
                @csrf
                @method('PUT')

                {{-- Upload & Preview Avatar --}}
                <div class="flex flex-col items-center mb-6">
                    <div class="w-24 h-24 bg-gray-50 rounded-[24px] overflow-hidden border-2 border-gray-100 mb-3 flex items-center justify-center text-gray-400 shadow-inner">
                        <img id="preview-foto" src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=F3F4F6&color=9CA3AF' }}" class="w-full h-full object-cover">
                    </div>
                    <label class="cursor-pointer bg-white border border-gray-200 px-4 py-1.5 rounded-xl text-[11px] font-bold text-gray-600 hover:bg-gray-50 transition-all shadow-sm">
                        Ganti Profil
                        <input type="file" name="foto" class="hidden" onchange="previewImage(this)">
                    </label>
                </div>

                {{-- Input Fields --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Kepala Lab</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full mt-1.5 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#0090F5] font-medium text-sm text-gray-800">
                    </div>
                    <div class="col-span-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">ID Kepala Lab</label>
                        <input type="text" value="{{ Auth::user()->id }}" disabled class="w-full mt-1.5 p-3.5 bg-gray-100 border border-gray-200 rounded-2xl font-medium text-sm text-gray-400 cursor-not-allowed">
                    </div>
                    <div class="col-span-2">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full mt-1.5 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#0090F5] font-medium text-sm text-gray-800">
                    </div>
                    <div class="col-span-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">No Hp</label>
                        <input type="text" name="no_hp" value="{{ Auth::user()->no_hp }}" class="w-full mt-1.5 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#0090F5] font-medium text-sm text-gray-800">
                    </div>
                    <div class="col-span-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Role</label>
                        <input type="text" value="{{ Auth::user()->role ?? 'Kepala Lab' }}" disabled class="w-full mt-1.5 p-3.5 bg-gray-100 border border-gray-200 rounded-2xl font-medium text-sm text-gray-400 cursor-not-allowed">
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-white border-2 border-gray-800 text-gray-800 font-extrabold rounded-2xl hover:bg-gray-800 hover:text-white transition-all flex items-center justify-center gap-3 shadow-sm">
                        Simpan <i class="fa-solid fa-floppy-disk text-sm"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL POPUP UBAH PASSWORD (SESUAI FIGMA) --}}
    <div x-show="openPassword" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" x-cloak>
        
        <div @click.away="openPassword = false" 
             class="bg-white rounded-[32px] shadow-2xl w-full max-w-md overflow-hidden transform transition-all p-8 border border-gray-100"
             x-show="openPassword"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            {{-- Header Modal: Judul di Tengah, Tombol Close di Kanan --}}
            <div class="flex items-center justify-center pb-6 relative">
                <h3 class="text-sm font-bold text-gray-800 tracking-wide">Ubah Password</h3>
                <button @click="openPassword = false" class="text-gray-400 hover:text-gray-600 transition-colors absolute right-0">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Form Input --}}
            <form action="{{ route('profil.password') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-[11px] font-semibold text-gray-700 block mb-1.5">Password Lama</label>
                    <input type="password" name="current_password" required 
                           class="w-full p-2.5 bg-white border border-gray-400 rounded-xl focus:outline-none focus:border-[#0090F5] text-sm text-gray-800">
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-gray-700 block mb-1.5">Password Baru</label>
                    <input type="password" name="password" required 
                           class="w-full p-2.5 bg-white border border-gray-400 rounded-xl focus:outline-none focus:border-[#0090F5] text-sm text-gray-800">
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-gray-700 block mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required 
                           class="w-full p-2.5 bg-white border border-gray-400 rounded-xl focus:outline-none focus:border-[#0090F5] text-sm text-gray-800">
                </div>

                {{-- Tombol Simpan Mini di Kiri Bawah --}}
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
    // Handler toggle sidebar versi mobile asli Anda
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
        }
    }

    // Live preview setelah memilih berkas foto baru
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-foto').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection