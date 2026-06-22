@extends('layouts.app')

@section('title', 'Kelola User - SiLapor')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display font-bold text-2xl text-gray-900">Kelola User</h1>
            <p class="text-gray-500 text-sm">Tambah akun baru, ubah profil, atau reset password user.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="bg-silapor-500 hover:bg-silapor-600 text-white text-sm font-semibold rounded-xl px-4 py-2">
            + Tambah User
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white border border-gray-100 rounded-2xl divide-y divide-gray-100">
        @forelse ($users as $u)
            <div class="px-5 py-4 flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-gray-900">{{ $u->nama }}</p>
                    <p class="text-sm text-gray-500">{{ $u->email }}</p>
                </div>
                <span class="text-xs font-semibold uppercase px-3 py-1 rounded-full bg-silapor-50 text-silapor-700 shrink-0">
                    {{ $u->role }}
                </span>
                <a href="{{ route('admin.users.edit', $u->id_user) }}"
                   class="text-sm text-silapor-600 hover:underline shrink-0">
                    Edit / Reset Password
                </a>
            </div>
        @empty
            <p class="px-5 py-6 text-gray-400 text-sm">Belum ada user.</p>
        @endforelse
    </div>
</div>
@endsection
