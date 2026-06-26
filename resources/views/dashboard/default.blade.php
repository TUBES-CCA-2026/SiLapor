@extends('layouts.app')

@section('title', 'Dashboard - SiLapor')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-start justify-center px-6 py-16">
    <div class="w-full max-w-3xl bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-start justify-between gap-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">
                    Selamat datang, {{ auth()->user()->nama ?? auth()->user()->name ?? 'User' }} 👋
                </h1>

                <p class="mt-2 text-gray-600">
                    Role:
                    <span class="font-bold text-blue-600">
                        {{ auth()->user()->role ?? '-' }}
                    </span>
                </p>

                <p class="mt-8 text-gray-400">
                    Ini halaman placeholder. Ganti dengan dashboard sesuai role masing-masing.
                </p>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection