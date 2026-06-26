@php
    $displayUser = $user ?? auth()->user();
    $displayName = $displayUser?->nama ?? $displayUser?->name ?? 'User';
    $roleLabel = $displayUser?->role_label ?? match($displayUser?->role) {
        'kepala_lab' => 'Kepala Lab',
        'koordinator_lab' => 'Koordinator Lab',
        'laboran' => 'Laboran',
        'asisten' => 'Asisten Lab',
        'admin' => 'Admin',
        default => 'User',
    };
    $avatarUrl = $displayUser?->profile_photo_url
        ?? ($displayUser?->foto
            ? asset('storage/' . $displayUser->foto)
            : 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=FFFFFF&color=0090F5');
@endphp

<div class="bg-[#0090F5] text-white px-5 py-2.5 rounded-2xl flex items-center gap-4 shadow-lg border border-white/5 transition-all hover:shadow-xl w-full sm:w-auto min-w-[260px]">
    <img
        src="{{ $avatarUrl }}"
        alt="Foto Profil {{ $displayName }}"
        class="w-12 h-12 rounded-full object-cover border-2 border-white/40 shadow-sm shrink-0 bg-white"
    >

    <div class="text-left flex flex-col justify-center overflow-hidden min-w-0">
        <span class="text-[11px] font-medium opacity-80 block tracking-tight">
            Selamat datang,
        </span>

        <div class="flex items-center gap-3 mt-0.5 min-w-0">
            <span class="text-base font-extrabold block tracking-wide truncate max-w-[155px]">
                {{ $displayName }}
            </span>

            <div class="h-4 w-[1px] bg-white/35 rounded-full shrink-0"></div>

            <span class="text-[10px] font-bold opacity-90 block tracking-widest uppercase whitespace-nowrap">
                {{ $roleLabel }}
            </span>
        </div>
    </div>
</div>
