@extends('layouts.app')

@section('title', 'Dashboard - SiLapor')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="font-display font-bold text-2xl text-gray-900">Selamat datang, {{ $user->nama }} 👋</h1>
    <p class="text-gray-500 mt-1">
        Role: <span class="font-semibold uppercase text-silapor-600">{{ $user->role }}</span>
    </p>
    <p class="text-gray-400 text-sm mt-6">
        Ini halaman placeholder. Ganti dengan dashboard {{ $user->role }} yang sudah kamu desain
        sesuai flowchart masing-masing role.
    </p>
</div>
@endsection
