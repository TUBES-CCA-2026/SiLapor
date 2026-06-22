@extends('layouts.app')

@section('title', 'Dashboard Admin - SiLapor')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="font-display font-bold text-2xl text-gray-900 mb-1">Panel Admin</h1>
    <p class="text-gray-500 mb-8">Kelola seluruh isi sistem SiLapor dari sini.</p>

    <div class="grid sm:grid-cols-2 gap-4">
        <a href="{{ route('admin.users.index') }}" class="bg-white border border-gray-100 rounded-2xl p-5 hover:border-silapor-300 transition">
            <p class="font-semibold text-gray-900">👤 Kelola User</p>
            <p class="text-sm text-gray-500 mt-1">Tambah akun, ubah profil, reset password user.</p>
        </a>

        <a href="{{ route('fasilitas.index') }}" class="bg-white border border-gray-100 rounded-2xl p-5 hover:border-silapor-300 transition">
            <p class="font-semibold text-gray-900">🏷️ Fasilitas & QR Code</p>
            <p class="text-sm text-gray-500 mt-1">Tambah fasilitas baru & cetak/regenerasi QR.</p>
        </a>

        <a href="{{ route('laboratorium.index') }}" class="bg-white border border-gray-100 rounded-2xl p-5 hover:border-silapor-300 transition">
            <p class="font-semibold text-gray-900">🏫 Data Laboratorium</p>
            <p class="text-sm text-gray-500 mt-1">Kelola daftar laboratorium & koordinatornya.</p>
        </a>
    </div>
</div>
@endsection
