@extends('layouts.app')

@section('title', 'Contact Laboratory Support - SiLapor')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="w-full max-w-3xl bg-white border border-gray-100 rounded-3xl shadow-sm p-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-silapor-500 to-silapor-700 text-white grid place-items-center shadow-md">
                    <i class="fa-solid fa-headset text-xl"></i>
                </div>
                <div>
                    <h1 class="font-display font-bold text-2xl text-gray-900">Contact Laboratory Support</h1>
                    <p class="text-sm text-gray-500">Hubungi laboran yang terdaftar pada sistem.</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="text-sm font-semibold text-silapor-600 hover:underline">Kembali</a>
        </div>

        <div class="grid gap-4">
            @forelse($laborans as $laboran)
                <div class="border border-gray-100 rounded-2xl p-5 bg-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="font-bold text-gray-900">{{ $laboran->nama }}</p>
                        <p class="text-sm text-gray-500">Laboran</p>
                    </div>
                    <div class="grid gap-2 text-sm">
                        <a href="mailto:{{ $laboran->email }}" class="text-silapor-600 font-semibold hover:underline">
                            <i class="fa-regular fa-envelope mr-2"></i>{{ $laboran->email ?? '-' }}
                        </a>
                        <a href="tel:{{ $laboran->phone }}" class="text-gray-700 font-semibold hover:underline">
                            <i class="fa-solid fa-phone mr-2"></i>{{ $laboran->phone ?? 'Nomor belum tersedia' }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="border border-dashed border-gray-200 rounded-2xl p-8 text-center text-gray-500">
                    Belum ada akun laboran yang bisa dihubungi.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
