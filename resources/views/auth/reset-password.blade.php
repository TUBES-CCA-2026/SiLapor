@extends('layouts.app')

@section('title', 'Reset Password - SiLapor')

@section('suppress_global_notification', 'true')

@section('content')
<div class="min-h-screen flex items-center justify-center p-8 bg-gray-50">
    <div class="w-full max-w-md">
        <div class="flex items-center gap-2 mb-8">
            <img src="{{ asset('images/logo-silapor.png') }}" alt="SiLapor" class="w-9 h-9 rounded-lg object-contain">
            <span class="font-display font-bold text-lg text-gray-900">SiLapor</span>
        </div>

        <h2 class="font-display font-bold text-2xl text-gray-900 mb-1">Buat Password Baru</h2>
        <p class="text-gray-500 mb-8">Kode OTP terverifikasi. Masukkan password baru kamu.</p>

        @if (session('success'))
            <div class="mb-5 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" autofocus required minlength="8"
                       placeholder="Minimal 8 karakter"
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-silapor-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required minlength="8"
                       placeholder="Ulangi password baru"
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-silapor-500">
            </div>

            <button type="submit"
                    class="w-full bg-silapor-500 hover:bg-silapor-600 text-white font-semibold rounded-xl py-3 transition">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>
@endsection
