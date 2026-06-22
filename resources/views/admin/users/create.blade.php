@extends('layouts.app')

@section('title', 'Tambah User - SiLapor')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
    <h1 class="font-display font-bold text-2xl text-gray-900 mb-1">Tambah User Baru</h1>
    <p class="text-gray-500 text-sm mb-6">Buat akun untuk asisten, laboran, koordinator, kepala lab, atau admin lain.</p>

    @if ($errors->any())
        <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white border border-gray-100 rounded-2xl p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input name="nama" value="{{ old('nama') }}" required
                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Awal</label>
            <input type="password" name="password" minlength="8" required
                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-silapor-500">
                <option value="" disabled selected>Pilih role</option>
                @foreach ($roles as $r)
                    <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                <input name="nim" value="{{ old('nim') }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <input name="jurusan" value="{{ old('jurusan') }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Peminatan</label>
                <input name="peminatan" value="{{ old('peminatan') }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
            </div>
        </div>

        <button type="submit" class="w-full bg-silapor-500 hover:bg-silapor-600 text-white font-semibold rounded-xl py-3">
            Simpan User
        </button>
    </form>

    <a href="{{ route('admin.users.index') }}" class="block text-center text-sm text-silapor-600 hover:underline mt-4">← Kembali ke daftar user</a>
</div>
@endsection
