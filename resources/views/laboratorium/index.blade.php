@extends('layouts.app')

@section('title', 'Kelola Laboratorium | SiLapor')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.page-styles')

@php
    $user = auth()->user();
    $activeMenu = 'laboratorium';
    $isLaboran = $user?->role === 'laboran';
    $isKoordinator = $user?->role === 'koordinator_lab';
@endphp

@once
<style>
    .font-figma { font-family: 'Plus Jakarta Sans', sans-serif; }
    .lab-card { background:#fff; border:1px solid #E5E7EB; border-radius:2rem; box-shadow:0 15px 50px rgba(0,0,0,.05); overflow:hidden; }
    .lab-body { padding:1.5rem; }
    .form-control { width:100%; border:1px solid #D1D5DB; border-radius:.75rem; padding:.65rem .85rem; background:#fff; outline:none; font-size:.86rem; }
    .form-control:focus { border-color:#29ABE2; box-shadow:0 0 0 3px rgba(41,171,226,.12); }
    .btn-mini { border-radius:.65rem; padding:.45rem .85rem; font-size:.78rem; font-weight:800; cursor:pointer; transition:.18s ease; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:.3rem; }
    .btn-primary-mini { background:#29ABE2; color:#fff; }
    .btn-primary-mini:hover { background:#1B8DC4; }
    .btn-outline-mini { background:#E8F7FC; color:#29ABE2; border:1px solid #29ABE2; }
    .btn-outline-mini:hover { background:#29ABE2; color:#fff; }
    .btn-danger-mini { background:#FEE2E2; color:#DC2626; border:1px solid #FCA5A5; }
    .btn-danger-mini:hover { background:#DC2626; color:#fff; }
    .btn-danger-mini:disabled { opacity:.45; cursor:not-allowed; }
    .lab-form { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)) auto; gap:.85rem; align-items:end; margin-bottom:1.25rem; padding:1rem; border:1px solid #E5E7EB; border-radius:1rem; background:#fff; }
    .lab-list { border:1px solid #E5E7EB; border-radius:1rem; overflow:hidden; background:#fff; }
    .lab-item { padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9; }
    .lab-item:last-child { border-bottom:0; }
    .lab-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .lab-title { margin:0; color:#111827; font-weight:800; }
    .lab-meta { margin:.22rem 0 0; color:#64748B; font-size:.84rem; }
    .lab-pj { margin:.22rem 0 0; color:#94A3B8; font-size:.78rem; }
    .pj-form { display:grid; grid-template-columns: 1fr 1fr auto; gap:.75rem; align-items:end; margin-top:.75rem; padding:1rem; border:1px solid #DCE6F1; border-radius:1rem; background:#F8FAFC; }
    .field-label { display:block; font-size:.72rem; font-weight:800; color:#64748B; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.35rem; }
    @media (max-width: 920px) {
        .lab-form { grid-template-columns:1fr; }
        .pj-form { grid-template-columns:1fr; }
        .lab-row { align-items:flex-start; flex-direction:column; }
    }
</style>
@endonce

<div class="font-figma min-h-screen bg-[#F8FAFC] flex flex-col md:flex-row">
    @include('partials.sidebar', ['user' => $user, 'activeMenu' => $activeMenu])

    <main class="w-full min-w-0 px-4 py-6 md:px-8 md:py-8 space-y-6 overflow-x-hidden">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none hide-on-desktop md:hidden">
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
                </div>

                {{-- Form Tambah Lab: hanya untuk laboran --}}
                @if($isLaboran)
                <form method="POST" action="{{ route('laboratorium.store') }}" class="lab-form" style="grid-template-columns: 1fr 1fr 1fr auto;">
                    @csrf
                    <div>
                        <label class="field-label">Nama Laboratorium</label>
                        <input name="nama_laboratorium" placeholder="Nama lab" required class="form-control">
                    </div>
                    <div>
                        <label class="field-label">Kode Laboratorium</label>
                        <input name="kode_laboratorium" placeholder="Kode" class="form-control">
                    </div>
                    <div>
                        <label class="field-label">Koordinator Lab</label>
                        <div class="relative custom-searchable-select w-full">
                            <input type="text" readonly placeholder="— Pilih Koordinator —" class="form-control searchable-select-trigger cursor-pointer bg-white" style="padding-right: 2rem;">
                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                            <div class="absolute left-0 right-0 z-50 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg hidden searchable-select-dropdown p-2" style="min-width: 200px;">
                                <input type="text" placeholder="Cari Koordinator..." class="w-full p-2 mb-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-silapor-500 searchable-select-search">
                                <ul class="max-h-40 overflow-y-auto searchable-select-options custom-scrollbar space-y-0.5 text-left">
                                    <li data-value="" class="px-2.5 py-1.5 hover:bg-[#E8F7FC] hover:text-silapor-500 rounded-md cursor-pointer text-xs transition-colors">
                                        — Pilih Koordinator —
                                    </li>
                                    @foreach($asistenList as $asisten)
                                        <li data-value="{{ $asisten->id_user }}" class="px-2.5 py-1.5 hover:bg-[#E8F7FC] hover:text-silapor-500 rounded-md cursor-pointer text-xs transition-colors">
                                            {{ $asisten->nama }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <input type="hidden" name="id_koordinator" value="">
                        </div>
                    </div>
                    <button class="btn-mini btn-primary-mini" type="submit">
                        Submit
                    </button>
                </form>
                @endif

                <div class="lab-list">
                    @forelse($laboratoriums as $lab)
                        <div class="lab-item">
                            <div class="lab-row">
                                <div>
                                    <p class="lab-title">
                                        <span @if($isLaboran) onclick="toggleEditForm('{{ $lab->id_laboratorium }}')" style="cursor: pointer;" class="hover:underline hover:text-silapor-500 transition-colors" @endif>
                                            {{ $lab->nama_laboratorium }}
                                        </span>
                                        @if($lab->kode_laboratorium)
                                            <span class="text-xs text-gray-400 font-semibold">({{ $lab->kode_laboratorium }})</span>
                                        @endif
                                    </p>
                                    <p class="lab-pj">
                                        Koordinator: {{ $lab->koordinator?->nama ?? 'Belum ditentukan' }}
                                        · {{ $lab->fasilitas_count }} fasilitas
                                    </p>
                                </div>
                                @if($isLaboran)
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('laboratorium.destroy', $lab) }}" method="POST" onsubmit="confirmDelete(event, this)" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-mini btn-danger-mini">
                                            <i class="fa-solid fa-trash"></i>Hapus
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>

                            {{-- Laboran: form edit detail lab --}}
                            @if($isLaboran)
                            <div id="edit-form-{{ $lab->id_laboratorium }}" class="pj-form" style="display: none; margin-top: .75rem; grid-template-columns: 1fr 1fr 1fr auto;">
                                <form method="POST" action="{{ route('laboratorium.update', $lab) }}" style="display:contents;">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="field-label">Nama Laboratorium</label>
                                        <input name="nama_laboratorium" value="{{ $lab->nama_laboratorium }}" required class="form-control">
                                    </div>
                                    <div>
                                        <label class="field-label">Kode Laboratorium</label>
                                        <input name="kode_laboratorium" value="{{ $lab->kode_laboratorium }}" class="form-control">
                                    </div>
                                    <div>
                                        <label class="field-label">Koordinator Lab</label>
                                        <div class="relative custom-searchable-select w-full">
                                            <input type="text" readonly placeholder="— Pilih Koordinator —" class="form-control searchable-select-trigger cursor-pointer bg-white" style="padding-right: 2rem;">
                                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                                <i class="fa-solid fa-chevron-down text-xs"></i>
                                            </div>
                                            <div class="absolute left-0 right-0 z-50 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg hidden searchable-select-dropdown p-2" style="min-width: 200px;">
                                                <input type="text" placeholder="Cari Koordinator..." class="w-full p-2 mb-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-silapor-500 searchable-select-search">
                                                <ul class="max-h-40 overflow-y-auto searchable-select-options custom-scrollbar space-y-0.5 text-left">
                                                    <li data-value="" class="px-2.5 py-1.5 hover:bg-[#E8F7FC] hover:text-silapor-500 rounded-md cursor-pointer text-xs transition-colors">
                                                        — Pilih Koordinator —
                                                    </li>
                                                    @foreach($asistenList as $asisten)
                                                        <li data-value="{{ $asisten->id_user }}" class="px-2.5 py-1.5 hover:bg-[#E8F7FC] hover:text-silapor-500 rounded-md cursor-pointer text-xs transition-colors">
                                                            {{ $asisten->nama }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <input type="hidden" name="id_koordinator" value="{{ $lab->id_koordinator }}">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn-mini btn-primary-mini">
                                        <i class="fa-solid fa-floppy-disk"></i>Simpan
                                    </button>
                                </form>
                            </div>
                            @endif
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
    function toggleEditForm(labId) {
        // Hide all other edit forms
        document.querySelectorAll('[id^="edit-form-"]').forEach(function (form) {
            if (form.id !== 'edit-form-' + labId) {
                form.style.display = 'none';
            }
        });
        // Toggle target form
        const target = document.getElementById('edit-form-' + labId);
        if (target) {
            target.style.display = target.style.display === 'none' ? 'grid' : 'none';
        }
    }

    function togglePjForm(labId) {
        // Hide all other PJ forms
        document.querySelectorAll('.pj-form').forEach(function (form) {
            if (form.id !== 'pj-form-' + labId) {
                form.style.display = 'none';
            }
        });
        // Toggle target form
        const target = document.getElementById('pj-form-' + labId);
        if (target) {
            target.style.display = target.style.display === 'none' ? 'grid' : 'none';
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function confirmDelete(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Hapus Laboratorium?',
            text: 'Apakah Anda yakin ingin menghapus laboratorium ini? Seluruh data fasilitas di dalamnya akan di-unlink.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endsection
