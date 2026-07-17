<div class="modal-backdrop" id="detailModal" hidden>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="detailModalTitle">
        <div class="modal-header">
            <h2 id="detailModalTitle">Detail Pengaduan</h2>
            <button type="button" class="modal-close" data-close-modal aria-label="Tutup">×</button>
        </div>
        <div class="modal-body" id="modalContent"></div>
    </div>
</div>

@once
<style>
    .modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 60;
        padding: 20px;
        background: rgba(15, 23, 42, .34);
        backdrop-filter: blur(4px);
        display: grid;
        place-items: center;
    }
    .modal-backdrop[hidden] { display: none !important; }
    .modal-card {
        width: min(780px, 96vw);
        max-height: 92vh;
        overflow: hidden;
        border: 1px solid #DCE6F1;
        border-radius: 28px;
        background: #fff;
        box-shadow: 0 28px 70px rgba(30, 64, 175, .18);
    }
    .modal-header {
        min-height: 68px;
        padding: 0 24px;
        border-bottom: 0;
        background: linear-gradient(135deg, #29ABE2, #156C99);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-header h2 {
        margin: 0;
        color: #fff;
        font-size: 18px;
        font-weight: 800;
    }
    .modal-close {
        border: 0;
        background: transparent;
        color: #fff;
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }
    .modal-body {
        padding: 24px;
        overflow-y: auto;
        max-height: calc(92vh - 68px);
        background: #F8FAFC;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: minmax(220px, 280px) minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }
    .detail-photo-wrap {
        display: grid;
        gap: 10px;
        width: 100%;
        max-height: 360px;
        margin: 0;
        border: 1px solid #DCE6F1;
        border-radius: 18px;
        overflow-y: auto;
        background: #fff;
        padding: 8px;
    }
    .modal-photo {
        width: 100%;
        height: 160px;
        display: block;
        object-fit: cover;
        border-radius: 12px;
    }
    .modal-photo-placeholder {
        width: 100%;
        min-height: 160px;
        display: grid;
        place-items: center;
        color: #777;
        font-size: 13px;
        font-weight: 700;
    }
    .detail-panel {
        width: 100%;
        margin: 0;
        border: 1px solid #DCE6F1;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
    }
    .modal-row {
        min-height: 38px;
        display: grid;
        grid-template-columns: 96px 12px 1fr;
        align-items: center;
        padding: 0 16px;
        background: #fff;
        color: #475569;
        font-size: 14px;
        border-bottom: 1px solid #EEF2F7;
    }
    .modal-row:nth-child(even) { background: #F8FAFC; }
    .modal-label { font-weight: 700; }
    .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 5px;
        font-size: 11px;
        font-style: normal;
        line-height: 1.25;
        color: #6b5700;
        background: #ffe03d;
    }
    .status-badge.new { color: #0b5b9c; background: #d8ecff; }
    .status-badge.done { color: #0f7433; background: #d9f7e3; }
    .status-badge.progress { color: #6b5700; background: #ffe03d; }
    .status-badge.cancel { color: #B91C1C; background: #FEE2E2; }
    .status-badge.no-sparepart { color: #374151; background: #E5E7EB; }
    .modal-row-description {
        min-height: 116px;
        align-items: start;
        padding-top: 14px;
        padding-bottom: 14px;
    }
    .description-box {
        min-height: 76px;
        padding: 14px;
        border: 1px solid #DCE6F1;
        border-radius: 18px;
        background: #fff;
        color: #475569;
        line-height: 1.45;
        white-space: pre-wrap;
    }
    .loading-line {
        height: 13px;
        margin: 13px 0;
        border-radius: 30px;
        background: linear-gradient(90deg, #edf2f7, #f8fbff, #edf2f7);
    }
    .loading-line.short { width: 60%; }

    @media (max-width: 820px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endonce

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    if (!modal || !modalContent || modal.dataset.bound === '1') return;
    modal.dataset.bound = '1';

    const isKoordinatorLab = {{ auth()->user() && auth()->user()->role === 'koordinator_lab' ? 'true' : 'false' }};
    const csrfToken = "{{ csrf_token() }}";

    function closeModal() {
        modal.hidden = true;
        modalContent.innerHTML = '';
        document.body.style.overflow = '';
    }

    function esc(value) {
        return String(value ?? '-')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderDetail(data) {
        const fotoItems = Array.isArray(data.fotos) && data.fotos.length ? data.fotos : (data.foto ? [{ url: data.foto }] : []);
        const foto = fotoItems.length
            ? fotoItems.map((item, index) => {
                const url = typeof item === 'string' ? item : item.url;
                return url ? `<img src="${esc(url)}" alt="Foto kerusakan ${index + 1}" class="modal-photo" loading="lazy">` : '';
            }).join('')
            : `<div class="modal-photo-placeholder">Tidak ada foto</div>`;

        const statusClass = esc(data.statusClass || 'new');
        const statusLabel = esc(data.statusLabel || data.status || '-');

        let descriptionHTML = `<div class="description-box">${esc(data.deskripsi)}</div>`;
        if (isKoordinatorLab && data.raw_id) {
            const updateUrl = `/laporan/${data.raw_id}`;
            descriptionHTML = `
                <form method="POST" action="${updateUrl}" style="width: 100%; display: flex; flex-direction: column; gap: 8px; margin: 4px 0;">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="PATCH">
                    <textarea name="deskripsi_kerusakan" class="form-control" style="min-height: 80px; resize: vertical; font-family: inherit; font-size: 13px; border: 1px solid #DCE6F1; border-radius: 12px; padding: 10px; outline: none; background: #fff;" required>${esc(data.deskripsi)}</textarea>
                    <button type="submit" class="btn-primary" style="align-self: flex-start; font-size: 12px; padding: 6px 14px; border-radius: 8px; font-weight: 700; height: auto; cursor: pointer; border: 0; background: #29ABE2; color: #fff;">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Deskripsi
                    </button>
                </form>
            `;
        }

        return `
            <div class="detail-grid">
                <div class="detail-photo-wrap">${foto}</div>
                <div class="detail-panel">
                    <div class="modal-row"><span class="modal-label">ID</span><span>:</span><span>${esc(data.id)}</span></div>
                    <div class="modal-row"><span class="modal-label">Status</span><span>:</span><span><mark class="status-badge ${statusClass}">${statusLabel}</mark></span></div>
                    <div class="modal-row"><span class="modal-label">Pelapor</span><span>:</span><span>${esc(data.pelapor)}</span></div>
                    <div class="modal-row"><span class="modal-label">Lokasi</span><span>:</span><span>${esc(data.lokasi)}</span></div>
                    <div class="modal-row"><span class="modal-label">Fasilitas</span><span>:</span><span>${esc(data.fasilitas)}</span></div>
                    <div class="modal-row"><span class="modal-label">Tgl Lapor</span><span>:</span><span>${esc(data.tanggal || data.tanggalLapor)}</span></div>
                    <div class="modal-row"><span class="modal-label">Tgl Selesai</span><span>:</span><span>${esc(data.tanggalSelesai)}</span></div>
                    <div class="modal-row"><span class="modal-label">PJ</span><span>:</span><span>${esc(data.penanggungJawab)}</span></div>
                    <div class="modal-row modal-row-description"><span class="modal-label">Deskripsi</span><span>:</span>${descriptionHTML}</div>
                    <div class="modal-row modal-row-description"><span class="modal-label">Catatan</span><span>:</span><div class="description-box">${esc(data.catatanPerbaikan)}</div></div>
                </div>
            </div>`;
    }

    document.addEventListener('click', async function (event) {
        const closeButton = event.target.closest('[data-close-modal]');
        if (closeButton || event.target === modal) {
            closeModal();
            return;
        }

        const detailButton = event.target.closest('[data-detail-url]');
        if (!detailButton) return;

        event.preventDefault();
        event.stopPropagation();

        const url = detailButton.dataset.detailUrl;
        modal.hidden = false;
        modalContent.innerHTML = '<div class="loading-line"></div><div class="loading-line short"></div><div class="loading-line"></div>';
        document.body.style.overflow = 'hidden';

        if (!url || url === '#') {
            modalContent.innerHTML = '<p>URL detail belum tersedia.</p>';
            return;
        }

        try {
            const response = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Gagal mengambil detail laporan.');
            const data = await response.json();
            modalContent.innerHTML = renderDetail(data);
        } catch (error) {
            console.error(error);
            modalContent.innerHTML = '<p>Detail laporan belum bisa ditampilkan. Pastikan route detail pengaduan sudah benar.</p>';
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hidden) closeModal();
    });
})();
</script>
@endpush
