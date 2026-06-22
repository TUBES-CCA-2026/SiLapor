@extends('layouts.app')

@section('title', 'Dashboard Asisten - SiLapor')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-8">

    <div>
        <h1 class="font-display font-bold text-2xl text-gray-900">Tugas Perbaikan Saya</h1>
        <p class="text-gray-500 text-sm">Daftar fasilitas yang ditugaskan koordinator untuk kamu perbaiki.</p>
    </div>

    <div class="space-y-3">
        @forelse ($tugas as $t)
            <div class="bg-white border border-gray-100 rounded-2xl p-5 flex items-start justify-between gap-4">
                <div>
                    <p class="font-semibold text-gray-900">{{ $t->pengaduan->fasilitas->nama_fasilitas }}</p>
                    <p class="text-sm text-gray-500">{{ $t->pengaduan->fasilitas->laboratorium->nama_laboratorium }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ $t->pengaduan->deskripsi_kerusakan }}</p>
                </div>
                <div class="text-right shrink-0">
                    <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                        {{ $t->status_penanganan === 'DONE' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $t->status_penanganan }}
                    </span>

                    @if ($t->status_penanganan !== 'DONE')
                        <form method="POST" action="{{ route('tindak-lanjut.update', $t->id_tindak_lanjut) }}" class="mt-3 text-left">
                            @csrf
                            @method('PATCH')
                            <textarea name="catatan_perbaikan" rows="2" placeholder="Catatan perbaikan…" required
                                      class="w-48 text-sm rounded-lg border border-gray-300 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-silapor-500"></textarea>
                            <div class="flex gap-2 mt-2">
                                <button name="status_penanganan" value="ON PROGRES"
                                        class="text-xs px-2 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">Simpan Progres</button>
                                <button name="status_penanganan" value="DONE"
                                        class="text-xs px-2 py-1 rounded-lg bg-silapor-500 text-white hover:bg-silapor-600">Tandai Selesai</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm">Belum ada tugas perbaikan untuk kamu saat ini.</p>
        @endforelse
    </div>

    <div>
        <h2 class="font-display font-bold text-lg text-gray-900 mb-3">Riwayat Notifikasi</h2>
        <div class="bg-white border border-gray-100 rounded-2xl divide-y divide-gray-100">
            @forelse ($notifikasi as $n)
                <div class="px-5 py-3 flex items-center justify-between text-sm">
                    <div>
                        <p class="text-gray-700">
                            Tugas perbaikan: <strong>{{ $n->tindakLanjut->pengaduan->fasilitas->nama_fasilitas }}</strong>
                        </p>
                        <p class="text-gray-400 text-xs">{{ $n->tanggal_pengiriman?->format('d M Y, H:i') }}</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full
                        {{ $n->status_pengiriman === 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $n->status_pengiriman === 'sent' ? 'Terkirim' : 'Gagal' }}
                    </span>
                </div>
            @empty
                <p class="px-5 py-4 text-gray-400 text-sm">Belum ada notifikasi.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
