<div class="modal-backdrop" id="detailModal" hidden>
    <div class="modal-card" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2>Detail Pengaduan</h2>
            <button type="button" class="modal-close" data-close-modal aria-label="Tutup">×</button>
        </div>
        <div class="modal-body" id="modalContent"></div>
    </div>
</div>

@once
<style>
    .modal-backdrop { position: fixed; inset: 0; z-index: 60; padding: 20px; background: rgba(15,23,42,.35); display: grid; place-items: center; }
    .modal-backdrop[hidden] { display: none !important; }
    .modal-card { width: min(520px, 96vw); max-height: 92vh; overflow: hidden; border-radius: 1.5rem; background: #fff; box-shadow: 0 18px 35px rgba(0,0,0,.18); }
    .modal-header { height: 58px; padding: 0 20px; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; }
    .modal-header h2 { margin: 0; color: #404040; font-size: 16px; font-weight: 800; }
    .modal-close { border: 0; background: transparent; color: #4a4a4a; font-size: 38px; font-weight: 800; line-height: 1; cursor: pointer; padding: 0; }
    .modal-body { padding: 34px 32px 36px; overflow-y: auto; max-height: calc(92vh - 58px); }
    .detail-photo-wrap { display: grid; gap: 10px; width: min(100%, 280px); max-height: 360px; margin: 0 auto 20px; border: 1px solid #E5E7EB; border-radius: 18px; overflow-y: auto; background: #f1f1f1; padding: 8px; }
    .modal-photo { width: 100%; height: 160px; display: block; object-fit: cover; border-radius: 12px; }
    .modal-photo-placeholder { width: 100%; min-height: 160px; display: grid; place-items: center; color: #777; font-size: 13px; font-weight: 700; }
    .detail-panel { width: min(100%, 420px); margin: 0 auto; border: 1px solid #E5E7EB; border-radius: 20px; overflow: hidden; background: #f7f7f7; }
    .modal-row { min-height: 38px; display: grid; grid-template-columns: 96px 12px 1fr; align-items: center; padding: 0 16px; background: #f0f0f0; color: #555; font-size: 14px; }
    .modal-row:nth-child(even) { background: #e8e8e8; }
    .status-badge { display: inline-block; padding: 2px 8px; border-radius: 5px; font-size: 11px; line-height: 1.25; color: #6b5700; background: #ffe03d; }
    .status-badge.new { color: #0b5b9c; background: #d8ecff; }
    .status-badge.done { color: #0f7433; background: #d9f7e3; }
    .status-badge.progress { color: #6b5700; background: #ffe03d; }
    .status-badge.cancel { color: #B91C1C; background: #FEE2E2; }
    .status-badge.no-sparepart { color: #374151; background: #E5E7EB; }
    .modal-row-description { min-height: 116px; align-items: start; padding-top: 14px; padding-bottom: 14px; }
    .description-box { min-height: 76px; padding: 14px; border: 1px solid #E5E7EB; border-radius: 18px; background: #fff; color: #555; line-height: 1.45; white-space: pre-wrap; }
    .loading-line { height: 13px; margin: 13px 0; border-radius: 30px; background: linear-gradient(90deg, #edf2f7, #f8fbff, #edf2f7); }
    .loading-line.short { width: 60%; }
</style>
@endonce

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    if (!modal || !modalContent || modal.dataset.bound === '1') return;
    modal.dataset.bound = '1';

    function closeModal() {
        modal.hidden = true;
        modalContent.innerHTML = '';
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

        return `
            <div class="detail-photo-wrap">${foto}</div>
            <div class="detail-panel">
                <div class="modal-row"><span>ID</span><span>:</span><span>${esc(data.id)}</span></div>
                <div class="modal-row"><span>Status</span><span>:</span><span><mark class="status-badge ${statusClass}">${statusLabel}</mark></span></div>
                <div class="modal-row"><span>Pelapor</span><span>:</span><span>${esc(data.pelapor)}</span></div>
                <div class="modal-row"><span>Lokasi</span><span>:</span><span>${esc(data.lokasi)}</span></div>
                <div class="modal-row"><span>Fasilitas</span><span>:</span><span>${esc(data.fasilitas)}</span></div>
                <div class="modal-row"><span>Tgl Lapor</span><span>:</span><span>${esc(data.tanggal)}</span></div>
                <div class="modal-row modal-row-description"><span>Deskripsi</span><span>:</span><div class="description-box">${esc(data.deskripsi)}</div></div>
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

        const url = detailButton.dataset.detailUrl;
        modal.hidden = false;
        modalContent.innerHTML = '<div class="loading-line"></div><div class="loading-line short"></div><div class="loading-line"></div>';

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
            modalContent.innerHTML = '<p>Detail laporan belum bisa ditampilkan. Pastikan route detail pengaduan sudah benar.</p>';
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
    });
})();
</script>
@endpush
