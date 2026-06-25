@extends('layouts.app')

@section('title', 'Profile - SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .shadow-figma-container { box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.04); }
    @media (min-width: 850px) {
        .sidebar-desktop { transform: translateX(0) !important; position: sticky !important; top: 0; height: 100vh; }
        .hide-on-desktop { display: none !important; }
    }
    /* Style untuk Modal Pop-up */
    #edit-modal { backdrop-filter: blur(4px); }
</style>

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    <!-- SIDEBAR KIRI -->
    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
        <div class="p-8 flex-1 flex flex-col overflow-y-auto">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0090F5] to-[#3B82F6] flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-[#0090F5] to-[#1E3A8A] bg-clip-text text-transparent">SiLapor</span>
            </div>

            <nav class="mt-10 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                <i class="fa-solid fa-table-columns text-lg"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('pengaduan.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                <i class="fa-regular fa-file-lines text-lg"></i> <span>Pengaduan</span>
            </a>
            <a href="{{ route('tindak-lanjut.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                <i class="fa-solid fa-screwdriver-wrench text-lg"></i> <span>Tindak Lanjut</span>
            </a>
            <a href="{{ route('riwayat.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                <i class="fa-solid fa-clock-rotate-left text-lg"></i> <span>Riwayat</span>
            </a>
            <a href="{{ route('teknisi.index') }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i> <span>Teknisi</span>
            </a>
            <a href="{{ route('profile.index') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                <div class="flex items-center gap-3.5">
                    <i class="fa-regular fa-user text-lg text-[#0090F5]"></i> <span>Profil</span>
                </div>
                <div class="w-1.5 h-6 rounded-full bg-[#0090F5]"></div>
            </a>
        </nav>
    </div>

    <!-- Bagian Bawah Sidebar (Logout) -->
    <div class="p-8">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
            <i class="fa-solid fa-right-from-bracket text-lg"></i> <span>Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    </div>
</aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 px-4 py-6 md:px-10 md:py-8 space-y-6">
        <header class="flex items-center justify-between pb-4 border-b border-gray-100/50">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-600">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h1 class="text-2xl font-extrabold text-[#2C3E50] uppercase tracking-wider">PROFIL</h1>
            </div>
            
            <button onclick="toggleModal(true)" class="bg-[#0090F5] text-white px-6 py-2.5 rounded-2xl flex items-center gap-2 shadow-md hover:bg-blue-600 transition cursor-pointer">
                <i class="fa-solid fa-pencil text-sm"></i> <span class="font-bold">Edit Profil</span>
            </button>
        </header>

        <section class="bg-white border border-gray-150 rounded-[32px] shadow-figma-container p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-48 h-48 rounded-2xl bg-gray-200 border-2 border-gray-100 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb" alt="Profile" class="w-full h-full object-cover">
                    </div>
                    <div class="bg-gray-100 px-6 py-2 rounded-xl font-bold text-gray-600">Asisten Lab</div>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-extrabold text-gray-400 uppercase mb-2">Nama Asisten</label>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl font-semibold text-gray-800">{{ $user->name }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-gray-400 uppercase mb-2">Nim Asisten</label>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl font-semibold text-gray-800">{{ $user->nim }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold text-gray-400 uppercase mb-2">Email</label>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl font-semibold text-gray-800">{{ $user->email }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-gray-400 uppercase mb-2">No Hp.</label>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl font-semibold text-gray-800">{{ $user->no_hp }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-gray-400 uppercase mb-2">Role</label>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl font-semibold text-gray-800">{{ $user->role }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-gray-400 uppercase mb-2">Jurusan</label>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl font-semibold text-gray-800">{{ $user->jurusan }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-gray-400 uppercase mb-2">Peminatan</label>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl font-semibold text-gray-800">{{ $user->peminatan }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold text-gray-400 uppercase mb-2">Penanggung Jawab LAB</label>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl font-semibold text-gray-800">{{ $user->pj }}</div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Modal Pop-up Edit -->
<div id="edit-modal" class="fixed inset-0 bg-black/40 z-[999] hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[32px] p-8 shadow-2xl relative">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Edit Profil</h2>
            <button onclick="toggleModal(false)" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider ml-1">Foto Profil</label>
                    <input type="file" name="foto" class="p-3 bg-gray-50 rounded-xl border border-gray-200 w-full">
                </div>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="name" value="{{ $user->name }}" placeholder="Nama" class="p-3 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 outline-none">
                <input type="text" name="nim" value="{{ $user->nim }}" placeholder="NIM" class="p-3 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 outline-none">
                <input type="email" name="email" value="{{ $user->email }}" placeholder="Email" class="col-span-1 md:col-span-2 p-3 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 outline-none">
                <input type="text" name="no_hp" value="{{ $user->no_hp }}" placeholder="No HP" class="p-3 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 outline-none">
                <input type="text" name="role" value="{{ $user->role }}" placeholder="Role" class="p-3 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 outline-none">
                <input type="text" name="jurusan" value="{{ $user->jurusan }}" placeholder="Jurusan" class="p-3 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 outline-none">
                <input type="text" name="peminatan" value="{{ $user->peminatan }}" placeholder="Peminatan" class="p-3 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 outline-none">
                <input type="text" name="pj" value="{{ $user->pj }}" placeholder="PJ Lab" class="col-span-1 md:col-span-2 p-3 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>
            <button type="submit" class="w-full mt-4 bg-[#0090F5] text-white py-3 rounded-2xl font-bold hover:bg-blue-600 transition">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function toggleModal(show) {
        const modal = document.getElementById('edit-modal');
        if (show) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
@endsection