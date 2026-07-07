@extends('layouts.app')

@section('title', 'Lupa Password - SiLapor')

@section('suppress_global_notification', 'true')

@section('content')
<div class="min-h-screen flex items-center justify-center p-8 bg-gray-50">
    <div class="w-full max-w-md">
        <div class="flex items-center gap-2 mb-8">
            <img src="{{ asset('images/logo-silapor.png') }}" alt="SiLapor" class="w-9 h-9 rounded-lg object-contain">
            <span class="font-display font-bold text-lg text-gray-900">SiLapor</span>
        </div>

        <h2 class="font-display font-bold text-2xl text-gray-900 mb-1">Lupa Password?</h2>
        <p class="text-gray-500 mb-8">Masukkan email kamu, kami kirim kode OTP 6 digit untuk reset password.</p>

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

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" autofocus required
                       placeholder="nama@email.com"
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-silapor-500">
            </div>

            <button type="submit"
                    class="w-full bg-silapor-500 hover:bg-silapor-600 text-white font-semibold rounded-xl py-3 transition">
                Kirim Kode OTP
            </button>
        </form>

        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="{{ route('login') }}" class="text-silapor-600 hover:underline">← Kembali ke login</a>
        </p>
    </div>
</div>
@endsection
