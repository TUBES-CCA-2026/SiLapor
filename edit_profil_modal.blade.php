<!-- EDIT PROFIL MODAL -->
<div id="editModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <strong>Edit Profil</strong>
            <span onclick="closeEditModal()" style="cursor:pointer;font-size:20px;">&times;</span>
        </div>

        <div style="text-align:center;margin-bottom:15px;">
            <img src="{{ asset('images/alisa.jpg') }}"
                 style="width:90px;height:90px;border-radius:8px;object-fit:cover;">
            <br><br>
            <button type="button" class="btn-outline">Ganti Profil</button>
        </div>

        <div class="form-grid">
            <div class="field">
                <label>Nama Koordinator</label>
                <input type="text" value="{{ auth()->user()->name ?? '' }}">
            </div>

            <div class="field">
                <label>ID Koordinator</label>
                <input type="text" value="{{ auth()->id() }}">
            </div>

            <div class="field full">
                <label>Email</label>
                <input type="email" value="{{ auth()->user()->email ?? '' }}">
            </div>

            <div class="field">
                <label>No Hp</label>
                <input type="text">
            </div>

            <div class="field">
                <label>Role</label>
                <input type="text">
            </div>
        </div>

        <div style="margin-top:20px;">
            <button class="btn-outline">Simpan</button>
        </div>
    </div>
</div>
