@extends('layouts.app')

@section('title', 'Login - SiLapor')

@section('content')
<div class="min-h-screen grid lg:grid-cols-[60%_40%]">

     <div class="relative hidden lg:flex flex-col justify-between p-12 text-white overflow-hidden bg-cover bg-center"
         style="background-image: linear-gradient(160deg, rgba(14,58,77,.88) 0%, rgba(21,108,153,.82) 100%), url('{{ asset('images/lab-background.jpg') }}');">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_iclabs.png') }}" alt="SiLapor"
                 class="w-99 h-99 p-1.5">
            <span class="font-display font-extrabold text-4xl tracking-tight">SiLapor</span>
        </div>

        <div mb-2>
            <h1 class="font-display font-extrabold text-4xl leading-tight mb-4">
                Integrated Computer<br>Laboratory System
            </h1>
            <p class="text-white/80 max-w-md">
                Platform terpadu untuk manajemen laboratorium komputer yang modern dan efisien.
            </p>
        </div>
        <div></div>
    </div>

    {{-- Panel kanan: form login --}}
    <div class="flex items-center justify-center p-8">
        <div class="w-full max-w-md justify-center">
            <h2 class="font-display font-bold text-3xl text-gray-900 mb-1 text-center">Sign In To Your Account</h2>
            <p class="text-gray-500 text-center mb-8">Welcome Back!</p>

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="text" name="email" value="{{ old('email', $rememberEmail ?? '') }}" autofocus
                           placeholder="Email"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-silapor-500">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="{{ route('password.request') }}" class="text-sm text-silapor-600 hover:underline">Forgot Password?</a>
                    </div>
                    <input type="password" name="password" placeholder="Password"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-silapor-500">
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" {{ (old('remember') || isset($rememberEmail)) ? 'checked' : '' }} class="rounded border-gray-300 text-silapor-600 focus:ring-silapor-500">
                    Remember Me
                </label>

                <button type="submit"
                        class="w-full bg-silapor-500 hover:bg-silapor-600 text-white font-semibold rounded-xl py-3 transition">
                    LOGIN TO SYSTEM
                </button>
            </form>

            <div class="border-t border-gray-100 my-6"></div>

            <p class="text-center text-sm text-gray-500">
                Need help? <a href="{{ route('support.contact') }}" class="text-silapor-600 hover:underline">Contact Laboratory Support</a>
            </p>

            <div class="text-center text-sm text-gray-400 mt-6">
                <p class="mb-2">Mau lapor kerusakan fasilitas tanpa login?</p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('scan.index') }}" class="text-silapor-600 hover:underline">Scan QR</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('pengaduan.manual.create') }}" class="text-silapor-600 hover:underline">Lapor Manual</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection