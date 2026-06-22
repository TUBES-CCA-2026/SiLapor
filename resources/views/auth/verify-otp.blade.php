@extends('layouts.app')

@section('title', 'Verifikasi OTP - SiLapor')

@section('content')
<div class="min-h-screen flex items-center justify-center p-8 bg-gray-50">
    <div class="w-full max-w-md">
        <div class="flex items-center gap-2 mb-8">
            <img src="{{ asset('images/logo-silapor.png') }}" alt="SiLapor" class="w-9 h-9 rounded-lg object-contain">
            <span class="font-display font-bold text-lg text-gray-900">SiLapor</span>
        </div>

        <h2 class="font-display font-bold text-2xl text-gray-900 mb-1">Masukkan Kode OTP</h2>
        <p class="text-gray-500 mb-8">
            Kode 6 digit sudah dikirim ke <strong>{{ $email }}</strong>. Berlaku 10 menit.
        </p>

        @if ($errors->any())
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode OTP</label>
                <input type="text" name="otp" inputmode="numeric" pattern="[0-9]*" maxlength="6" autofocus required
                       placeholder="• • • • • •"
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 text-center text-2xl tracking-[0.5em] focus:outline-none focus:ring-2 focus:ring-silapor-500">
            </div>

            <button type="submit"
                    class="w-full bg-silapor-500 hover:bg-silapor-600 text-white font-semibold rounded-xl py-3 transition">
                Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('password.otp.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full text-sm text-silapor-600 hover:underline">
                Belum dapat kode? Kirim ulang
            </button>
        </form>

        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="{{ route('login') }}" class="text-silapor-600 hover:underline">← Kembali ke login</a>
        </p>
    </div>
</div>
@endsection
