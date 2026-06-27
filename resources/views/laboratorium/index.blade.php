@extends('layouts.app')

@section('title', 'Kelola Laboratorium | SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.page-styles')

@php
    $user = auth()->user();
    $activeMenu = 'laboratorium';
    $pjOptions = $penanggungJawabs ?? $koordinators ?? collect();
@endphp

@once
<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .lab-card { background:#fff; border:1px solid #E5E7EB; border-radius:2rem; box-shadow:0 15px 50px rgba(0,0,0,.05); overflow:hidden; }
    .lab-body { padding:1.5rem; }
    .form-control { width:100%; border:1px solid #D1D5DB; border-radius:.75rem; padding:.65rem .85rem; background:#fff; outline:none; font-size:.86rem; }
    .form-control:focus { border-color:#0090F5; box-shadow:0 0 0 3px rgba(0,144,245,.12); }
    .btn-mini { min-height:36px; border-radius:.7rem; padding:.5rem .85rem; font-size:.78rem; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:.4rem; cursor:pointer; border:0; transition:.18s ease; white-space:nowrap; }
    .btn-primary-mini { background:#0090F5; color:#fff; }
    .btn-primary-mini:hover { background:#007CD5; }
    .btn-outline-mini { background:#EEF8FF; color:#0090F5; border:1px solid #0090F5; }
    .btn-outline-mini:hover { background:#0090F5; color:#fff; }
    .btn-danger-mini { background:#FEE2E2; color:#DC2626; border:1px solid #FCA5A5; }
    .btn-danger-mini:hover { background:#DC2626; color:#fff; }
    .btn-danger-mini:disabled { opacity:.45; cursor:not-allowed; }
    .lab-form { display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:.85rem; align-items:start; margin-bottom:1.25rem; padding:1rem; border:1px solid #E5E7EB; border-radius:1rem; background:#fff; }
    .pj-checks { display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:.45rem; padding:.75rem; border:1px solid #D1D5DB; border-radius:.75rem; background:#F8FAFC; max-height:132px; overflow:auto; }
    .pj-check { display:flex; align-items:center; gap:.45rem; color:#475569; font-size:.82rem; font-weight:700; }
    .pj-check input { accent-color:#0090F5; }
    .lab-list { border:1px solid #E5E7EB; border-radius:1rem; overflow:hidden; background:#fff; }
    .lab-item { padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9; }
    .lab-item:last-child { border-bottom:0; }
    .lab-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .lab-title { margin:0; color:#111827; font-weight:800; }
    .lab-meta { margin:.22rem 0 0; color:#64748B; font-size:.84rem; }
    .lab-pj { margin:.22rem 0 0; color:#94A3B8; font-size:.78rem; }
    @media (max-width: 920px) {
        .lab-form { grid-template-columns:1fr; }
        .lab-row { align-items:flex-start; flex-direction:column; }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['user' => $user, 'activeMenu' => $activeMenu])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6 overflow-x-hidden">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#2C3E50] tracking-wider uppercase">Laboratorium</h1>
            </div>
            @include('partials.user-welcome-box', ['user' => $user])
        </header>

        <section class="lab-card">
            <div class="lab-body">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <h2 class="text-lg font-extrabold text-[#2C3E50] m-0">Data Laboratorium</h2>
                    <a href="{{ route('fasilitas.index') }}" class="btn-mini btn-outline-mini">
                        <i class="fa-solid fa-qrcode"></i>Fasilitas
                    </a>
                </div>

                <form method="POST" action="{{ route('laboratorium.store') }}" class="lab-form">
                    @csrf
                    <input name="nama_laboratorium" placeholder="Nama lab" required class="form-control">
                    <input name="kode_laboratorium" placeholder="Kode" class="form-control">
                    <input name="lokasi" placeholder="Lokasi" class="form-control">

                    <div class="md:col-span-2">
                        <div class="pj-checks">
                            @forelse($pjOptions as $k)
                                <label class="pj-check">
                                    <input type="checkbox" name="id_penanggung_jawab[]" value="{{ $k->id_user }}">
                                    <span>{{ $k->nama }}</span>
                                </label>
                            @empty
                                <span class="text-sm text-gray-400">Belum ada asisten.</span>
                            @endforelse
                        </div>
                    </div>

                    <button class="btn-mini btn-primary-mini" type="submit">
                        <i class="fa-solid fa-plus"></i>Lab
                    </button>
                </form>

                <div class="lab-list">
                    @forelse($laboratoriums as $lab)
                        @php
                            $selectedPj = $lab->penanggungJawabs->pluck('id_user')->map(fn($id) => (string) $id)->all();
                            if (empty($selectedPj) && $lab->id_koordinator) {
                                $selectedPj[] = (string) $lab->id_koordinator;
                            }
                        @endphp
                        <div class="lab-item">
                            <div class="lab-row">
                                <div>
                                    <p class="lab-title">
                                        {{ $lab->nama_laboratorium }}
                                        @if($lab->kode_laboratorium)
                                            <span class="text-xs text-gray-400 font-semibold">({{ $lab->kode_laboratorium }})</span>
                                        @endif
                                    </p>
                                    <p class="lab-meta">{{ $lab->lokasi ?? '—' }}</p>
                                    <p class="lab-pj">PJ: {{ $lab->penanggungJawabs->pluck('nama')->join(', ') ?: ($lab->koordinator?->nama ?? 'Belum ditentukan') }} · {{ $lab->fasilitas_count }} fasilitas</p>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" class="btn-mini btn-outline-mini" onclick="document.getElementById('edit-lab-{{ $lab->id_laboratorium }}').toggleAttribute('hidden')">
                                        <i class="fa-solid fa-pen"></i>Edit
                                    </button>
                                    <form method="POST" action="{{ route('laboratorium.destroy', $lab) }}" onsubmit="return confirm('Hapus laboratorium {{ $lab->nama_laboratorium }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-mini btn-danger-mini" {{ $lab->fasilitas_count > 0 ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-trash"></i>Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div id="edit-lab-{{ $lab->id_laboratorium }}" hidden class="mt-4 p-4 rounded-2xl border border-[#DCE6F1] bg-[#F8FAFC]">
                                <form method="POST" action="{{ route('laboratorium.update', $lab) }}" class="lab-form" style="margin-bottom:0;background:transparent;border:0;padding:0;">
                                    @csrf
                                    @method('PATCH')
                                    <input name="nama_laboratorium" value="{{ $lab->nama_laboratorium }}" required class="form-control" placeholder="Nama lab">
                                    <input name="kode_laboratorium" value="{{ $lab->kode_laboratorium }}" class="form-control" placeholder="Kode">
                                    <input name="lokasi" value="{{ $lab->lokasi }}" class="form-control" placeholder="Lokasi">

                                    <div class="md:col-span-2">
                                        <div class="pj-checks">
                                            @forelse($pjOptions as $k)
                                                <label class="pj-check">
                                                    <input type="checkbox" name="id_penanggung_jawab[]" value="{{ $k->id_user }}" @checked(in_array((string) $k->id_user, $selectedPj, true))>
                                                    <span>{{ $k->nama }}</span>
                                                </label>
                                            @empty
                                                <span class="text-sm text-gray-400">Belum ada asisten.</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    <button type="submit" class="btn-mini btn-primary-mini">
                                        <i class="fa-solid fa-floppy-disk"></i>Simpan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="m-0 p-6 text-sm text-gray-400">Belum ada data laboratorium.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
@endsection
