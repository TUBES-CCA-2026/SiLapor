<div class="modal-backdrop" id="detailModal" hidden>
    <div class="modal-card" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h2 class="font-extrabold text-gray-700">Detail Pengaduan</h2>
            <button type="button" class="modal-close" data-close-modal aria-label="Tutup">×</button>
        </div>
        <div class="modal-body" id="modalContent"></div>
    </div>
</div>

<script>
    function handleResponsiveSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        if (window.innerWidth < 850) {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
    window.addEventListener('resize', handleResponsiveSidebar);
    window.addEventListener('load', handleResponsiveSidebar);

    (function () {
        const modal = document.getElementById('detailModal');
        const modalContent = document.getElementById('modalContent');
        if (!modal || !modalContent) return;
        function closeModal() { modal.hidden = true; modalContent.innerHTML = ''; }
        function esc(value) { return String(value ?? '-').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
        function renderDetail(data) {
            const fotoItems = Array.isArray(data.fotos) && data.fotos.length ? data.fotos : (data.foto ? [{url:data.foto}] : []);
            const foto = fotoItems.length ? fotoItems.map((item, index) => {
                const url = typeof item === 'string' ? item : item.url;
                return url ? `<img src="${esc(url)}" alt="Foto kerusakan ${index + 1}" class="modal-photo" loading="lazy">` : '';
            }).join('') : `<div class="modal-photo-placeholder">Tidak ada foto</div>`;
            return `
                <div class="detail-photo-wrap">${foto}</div>
                <div class="detail-panel">
                    <div class="modal-row"><span>ID</span><span>:</span><span>${esc(data.id)}</span></div>
                    <div class="modal-row"><span>Status</span><span>:</span><span>${esc(data.statusLabel || data.status)}</span></div>
                    <div class="modal-row"><span>Pelapor</span><span>:</span><span>${esc(data.pelapor)}</span></div>
                    <div class="modal-row"><span>Lokasi</span><span>:</span><span>${esc(data.lokasi)}</span></div>
                    <div class="modal-row"><span>Fasilitas</span><span>:</span><span>${esc(data.fasilitas)}</span></div>
                    <div class="modal-row"><span>Tgl Lapor</span><span>:</span><span>${esc(data.tanggal)}</span></div>
                    <div class="modal-row" style="align-items:start;padding-top:14px;padding-bottom:14px;"><span>Deskripsi</span><span>:</span><div class="description-box">${esc(data.deskripsi)}</div></div>
                </div>`;
        }
        document.addEventListener('click', async function (event) {
            const detailButton = event.target.closest('[data-detail-url]');
            const closeButton = event.target.closest('[data-close-modal]');
            if (closeButton || event.target === modal) { closeModal(); return; }
            if (!detailButton) return;
            const url = detailButton.dataset.detailUrl;
            modal.hidden = false;
            modalContent.innerHTML = '<div class="loading-line"></div><div class="loading-line" style="width:60%"></div><div class="loading-line"></div>';
            if (!url || url === '#') { modalContent.innerHTML = '<p>URL detail belum tersedia.</p>'; return; }
            try {
                const response = await fetch(url, {headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
                if (!response.ok) throw new Error('Gagal mengambil detail laporan.');
                modalContent.innerHTML = renderDetail(await response.json());
            } catch (error) {
                modalContent.innerHTML = '<p>Detail laporan belum bisa ditampilkan. Pastikan route detail pengaduan sudah benar.</p>';
            }
        });
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeModal(); });
    })();
</script>
