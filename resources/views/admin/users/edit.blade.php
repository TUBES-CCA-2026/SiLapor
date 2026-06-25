@extends('layouts.app')

@section('title', 'Edit User - SiLapor')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8 space-y-6">

    <div>
        <h1 class="font-display font-bold text-2xl text-gray-900 mb-1">{{ $user->nama }}</h1>
        <p class="text-gray-500 text-sm">{{ $user->email }}</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">{{ $errors->first() }}</div>
    @endif

    {{-- ===== Form 1: Edit Profil ===== --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6">
        <h2 class="font-display font-semibold text-gray-900 mb-4">Edit Profil</h2>

        <form method="POST" action="{{ route('admin.users.update', $user->id_user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input name="nama" value="{{ old('nama', $user->nama) }}" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                    @foreach ($roles as $r)
                        <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                    <input name="nim" value="{{ old('nim', $user->nim) }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                    <input name="jurusan" value="{{ old('jurusan', $user->jurusan) }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peminatan</label>
                    <input name="peminatan" value="{{ old('peminatan', $user->peminatan) }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab</label>
                    <input name="penanggung_jawab" value="{{ old('penanggung_jawab', $user->penanggung_jawab) }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
            </div>

            <button type="submit" class="w-full bg-silapor-500 hover:bg-silapor-600 text-white font-semibold rounded-xl py-3">
                Simpan Profil
            </button>
        </form>
    </div>

    {{-- ===== Form 2: Admin langsung set password baru, tanpa OTP ===== --}}
    <div class="bg-white border border-amber-200 rounded-2xl p-6">
        <h2 class="font-display font-semibold text-gray-900 mb-1">Buat Password Baru</h2>
        <p class="text-sm text-gray-500 mb-4">
            Password baru ini langsung aktif begitu disimpan — user lama tidak akan
            dikirimi email/OTP apapun, jadi pastikan kamu sampaikan password ini langsung ke user-nya.
        </p>

        <form method="POST" action="{{ route('admin.users.reset-password', $user->id_user) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" minlength="8" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" minlength="8" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl py-3">
                Ganti Password
            </button>
        </form>
    </div>

    {{-- ===== Hapus user ===== --}}
    @if ($user->id_user !== auth()->id())
        <form method="POST" action="{{ route('admin.users.destroy', $user->id_user) }}"
              onsubmit="return confirm('Yakin hapus user {{ $user->nama }}? Tindakan ini tidak bisa dibatalkan.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full text-sm text-red-500 hover:underline">Hapus user ini</button>
        </form>
    @endif

    <a href="{{ route('admin.users.index') }}" class="block text-center text-sm text-silapor-600 hover:underline">← Kembali ke daftar user</a>
</div>
@endsection
