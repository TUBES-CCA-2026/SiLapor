@extends('layouts.silapor-dashboard', [
    'title' => 'Penugasan | SiLapor',
    'pageTitle' => 'PENUGASAN',
    'activeMenu' => 'penugasan'
])

@section('content')
@php
    $rows = isset($pengaduanList) ? $pengaduanList : collect();
    $teknisiList = isset($asisten) ? $asisten : collect();

    $statusMeta = function ($status) {
        return match ($status) {
            'NEW' => ['label' => 'Baru', 'class' => 'new'],
            'HANDLED' => ['label' => 'On Progress', 'class' => 'progress'],
            'DONE' => ['label' => 'Done', 'class' => 'done'],
            default => ['label' => $status ?: '-', 'class' => 'new'],
        };
    };
@endphp

<style>
    .penugasan-page {
        padding-top: 4px;
    }

    .penugasan-title {
        margin-bottom: 18px;
        color: #2f3d4d;
        font-size: 20px;
        font-weight: 800;
    }

    .penugasan-alert {
        width: 100%;
        margin: 0 0 16px;
        padding: 14px 18px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
    }

    .penugasan-alert.success {
        color: #137232;
        border: 1px solid #bcebc8;
        background: #e6faec;
    }

    .penugasan-alert.error {
        color: #b42318;
        border: 1px solid #ffc9c4;
        background: #fff0ef;
    }

    .penugasan-table-wrap {
        border-radius: 24px;
        overflow-x: auto;
    }

    .penugasan-table {
        min-width: 1120px;
        table-layout: auto;
    }

    .penugasan-table th,
    .penugasan-table td {
        padding-left: 18px;
        padding-right: 18px;
        white-space: nowrap;
    }

    .penugasan-table th {
        color: #39495a;
        font-size: 14px;
        font-weight: 800;
    }

    .penugasan-table td {
        color: #3f4c5a;
        font-size: 13px;
    }

    .penugasan-description {
        max-width: 240px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .assign-form {
        margin: 0;
    }

    .teknisi-select-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
        min-width: 136px;
    }

    .teknisi-select {
        width: 100%;
        height: 30px;
        padding: 0 30px 0 12px;
        border: 1px solid #b7c6d7;
        border-radius: 7px;
        background: #fff;
        color: #344b63;
        font-size: 12px;
        font-weight: 600;
        outline: none;
        appearance: auto;
        cursor: pointer;
    }

    .teknisi-select:focus {
        border-color: #0d8ff2;
        box-shadow: 0 0 0 3px rgba(13, 143, 242, .12);
    }

    .teknisi-select:disabled {
        cursor: not-allowed;
        color: #8a98a8;
        background: #eef2f7;
    }

    .penugasan-status {
        min-width: 108px;
        height: 30px;
        padding: 0 10px;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
    }

    .penugasan-status.progress {
        color: #806600;
        background: #ffd400;
    }

    .penugasan-status.done {
        color: #128a2b;
        background: #4dff41;
    }

    .penugasan-status.new {
        color: #0d5d9c;
        background: #dceeff;
    }

    .penugasan-status .status-arrow {
        margin-left: auto;
        opacity: .8;
        font-size: 11px;
    }

    .penugasan-note {
        margin: 14px 0 0;
        color: #70849b;
        font-size: 12px;
        line-height: 1.5;
    }

    @media (max-width: 820px) {
        .penugasan-table {
            min-width: 980px;
        }
    }
</style>

<section class="dashboard-card penugasan-page">
    <h2 class="section-title penugasan-title">Tugaskan Teknisi</h2>

    @if(session('success'))
        <div class="penugasan-alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="penugasan-alert error">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="table-wrap penugasan-table-wrap">
        <table class="report-table penugasan-table">
            <thead>
                <tr>
                    <th>Tanggal Lapor</th>
                    <th>Pelapor</th>
                    <th>Fasilitas</th>
                    <th>Lokasi Masalah</th>
                    <th>Deskripsi Kerusakan</th>
                    <th>Teknisi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $laporan)
                    @php
                        $tanggal = $laporan->tanggal_lapor
                            ? \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d/m/Y')
                            : '-';
                        $pelapor = data_get($laporan, 'pelapor.nama', 'Guest');
                        $fasilitas = data_get($laporan, 'fasilitas.nama_fasilitas', '-');
                        $lokasi = data_get($laporan, 'fasilitas.laboratorium.nama_laboratorium', '-');
                        $deskripsi = $laporan->deskripsi_kerusakan ?? '-';
                        $selectedAsisten = data_get($laporan, 'tindakLanjut.id_asisten');
                        $status = $statusMeta($laporan->status_pengaduan ?? null);
                    @endphp
                    <tr>
                        <td>{{ $tanggal }}</td>
                        <td>{{ $pelapor }}</td>
                        <td>{{ $fasilitas }}</td>
                        <td>{{ $lokasi }}</td>
                        <td class="penugasan-description" title="{{ $deskripsi }}">
                            {{ \Illuminate\Support\Str::limit($deskripsi, 42) }}
                        </td>
                        <td>
                            <form method="POST" action="{{ route('tindak-lanjut.assign', $laporan) }}" class="assign-form" data-assign-form>
                                @csrf
                                <span class="teknisi-select-wrap">
                                    <select name="id_asisten" class="teknisi-select" data-assign-select data-original="{{ $selectedAsisten }}" {{ $teknisiList->isEmpty() ? 'disabled' : '' }}>
                                        @if($teknisiList->isEmpty())
                                            <option value="">Belum ada teknisi</option>
                                        @else
                                            <option value="">Pilih Teknisi</option>
                                            @foreach($teknisiList as $teknisi)
                                                <option value="{{ $teknisi->id_user }}" @selected((string) $selectedAsisten === (string) $teknisi->id_user)>
                                                    {{ $teknisi->nama }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </span>
                            </form>
                        </td>
                        <td>
                            <span class="penugasan-status {{ $status['class'] }}">
                                {{ $status['label'] }}
                                <span class="status-arrow">▾</span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada laporan pengaduan untuk ditugaskan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="penugasan-note">Pilih nama teknisi/asisten pada kolom Teknisi untuk memberikan tugas perbaikan. Status akan berubah menjadi On Progress setelah penugasan berhasil disimpan.</p>
</section>
@endsection

@push('scripts')
<script>
(function () {
    document.addEventListener('change', function (event) {
        const select = event.target.closest('[data-assign-select]');
        if (!select) return;

        const selectedValue = select.value;
        const originalValue = select.dataset.original || '';

        if (!selectedValue) {
            select.value = originalValue;
            return;
        }

        const selectedText = select.options[select.selectedIndex]?.text?.trim() || 'teknisi';
        const form = select.closest('[data-assign-form]');
        if (!form) return;

        const confirmed = window.confirm('Tugaskan laporan ini ke ' + selectedText + '?');
        if (!confirmed) {
            select.value = originalValue;
            return;
        }

        form.submit();
    });
})();
</script>
@endpush
