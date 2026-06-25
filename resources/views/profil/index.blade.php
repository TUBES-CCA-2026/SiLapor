```php
@extends('layouts.silapor-dashboard', [
    'title' => 'Profil | SiLapor',
    'pageTitle' => 'PROFIL',
    'activeMenu' => 'profil'
])

@section('content')

<style>
.profile-card{
    background:#fff;
    border:1px solid #e5ebf2;
    border-radius:24px;
    padding:28px;
    box-shadow:0 10px 25px rgba(0,0,0,.05);
    margin:0 38px 40px;
}

.profile-top{
    display:flex;
    justify-content:flex-end;
    margin-bottom:20px;
}

.btn-outline{
    border:1px solid #bfc8d4;
    padding:10px 16px;
    border-radius:10px;
    background:#fff;
    cursor:pointer;
}

.profile-grid{
    display:grid;
    grid-template-columns:180px 1fr;
    gap:30px;
}

.avatar-box{
    text-align:center;
}

.avatar{
    width:120px;
    height:120px;
    border-radius:10px;
    object-fit:cover;
    border:1px solid #ddd;
}

.badge-role{
    margin-top:8px;
    border:1px solid #bbb;
    border-radius:6px;
    padding:4px 8px;
    font-size:12px;
    display:inline-block;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px 20px;
}

.field label{
    display:block;
    margin-bottom:6px;
    font-size:12px;
}

.field input{
    width:100%;
    padding:10px;
    border:1px solid #aaa;
    border-radius:8px;
    background:#fafafa;
}

.full{
    grid-column:1/-1;
}

.action-row{
    display:flex;
    justify-content:flex-end;
    margin-top:20px;
}

/* MODAL */
.modal-backdrop{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.35);
    z-index:9999;
}

.show{
    display:block;
}

.modal-box{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    background:#fff;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
}

/* EDIT PROFIL */
.edit-profile-modal{
    width:360px;
    overflow:hidden;
}

.edit-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:10px 14px;
    border-bottom:1px solid #ccc;
}

.close-btn{
    cursor:pointer;
    font-size:24px;
    font-weight:bold;
}

.edit-body{
    padding:16px;
}

.edit-photo{
    text-align:center;
    margin-bottom:18px;
}

.edit-photo img{
    width:90px;
    height:120px;
    object-fit:cover;
    border-radius:6px;
    border:1px solid #aaa;
}

.change-photo-btn{
    margin-top:8px;
}

.save-btn{
    border:1px solid #bfc8d4;
    background:#fff;
    padding:8px 18px;
    border-radius:8px;
    cursor:pointer;
}

.save-area{
    margin-top:20px;
}

/* PASSWORD */
.password-modal{
    width:410px;
    overflow:hidden;
    padding:0;
    border-radius:20px;
}

.password-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 18px;
    border-bottom:1px solid #ddd;
    font-size:14px;
    font-weight:600;
}

.password-body{
    padding:18px;
}

.modal-field{
    margin-bottom:20px;
}

.modal-field label{
    display:block;
    margin-bottom:6px;
    font-size:12px;
    color:#444;
}

.modal-field input{
    width:100%;
    height:28px;
    padding:6px 10px;
    border:1px solid #888;
    border-radius:6px;
    font-size:12px;
}

.save-password-btn{
    border:1px solid #888;
    background:#fff;
    border-radius:6px;
    padding:6px 12px;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:10px;
    font-size:12px;
}
</style>

<div class="profile-card">

    <div class="profile-top">
        <button class="btn-outline" onclick="openEditModal()">
            Edit Profil ✎
        </button>
    </div>

    <div class="profile-grid">

        <div class="avatar-box">
            <img class="avatar"
                 src="{{ asset('images/alisa.jpg') }}"
                 alt="Foto">

            <div class="badge-role">
                Koordinator Lab
            </div>
        </div>

        <div>

            <div class="form-grid">

                <div class="field">
                    <label>Nama Koordinator</label>
                    <input value="{{ auth()->user()->name ?? 'Nur Alisa' }}" readonly>
                </div>

                <div class="field">
                    <label>ID Koordinator</label>
                    <input value="{{ auth()->id() ?? '3' }}" readonly>
                </div>

                <div class="field full">
                    <label>Email</label>
                    <input value="{{ auth()->user()->email ?? 'koor@silapor.test' }}" readonly>
                </div>

                <div class="field">
                    <label>No Hp</label>
                    <input value="+62813xxxxxxx" readonly>
                </div>

                <div class="field">
                    <label>Role</label>
                    <input value="Koordinator LAB" readonly>
                </div>

            </div>

            <div class="action-row">
                <button class="btn-outline" onclick="openPasswordModal()">
                    Ubah Password ⚙
                </button>
            </div>

        </div>

    </div>

</div>

<!-- MODAL EDIT PROFIL -->
<div id="editModal" class="modal-backdrop">

    <div class="modal-box edit-profile-modal">

        <div class="edit-header">
            <strong>Edit Profil</strong>
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
        </div>

        <div class="edit-body">

            <div class="edit-photo">

                <img src="{{ asset('images/alisa.jpg') }}"
                     alt="Foto Profil">

                <br>

                <button class="save-btn change-photo-btn">
                    Ganti Profil
                </button>

            </div>

            <div class="form-grid">

                <div class="field">
                    <label>Nama Koordinator</label>
                    <input type="text"
                           value="{{ auth()->user()->name ?? 'Nur Alisa' }}">
                </div>

                <div class="field">
                    <label>ID Koordinator</label>
                    <input type="text"
                           value="{{ auth()->id() ?? '3' }}">
                </div>

                <div class="field full">
                    <label>Email</label>
                    <input type="email"
                           value="{{ auth()->user()->email ?? 'koor@silapor.test' }}">
                </div>

                <div class="field">
                    <label>No Hp</label>
                    <input type="text"
                           value="+62813xxxxxxx">
                </div>

                <div class="field">
                    <label>Role</label>
                    <input type="text"
                           value="Koordinator LAB">
                </div>

            </div>

            <div class="save-area">
                <button class="save-btn">
                    Simpan
                </button>
            </div>

        </div>

    </div>

</div>

<!-- MODAL PASSWORD -->
<!-- MODAL PASSWORD -->
<div id="passwordModal" class="modal-backdrop">

    <div class="modal-box password-modal">

        <div class="password-header">
            <div>Ubah Password</div>

            <span class="close-btn"
                  onclick="closePasswordModal()">
                ×
            </span>
        </div>

        <div class="password-body">

            <div class="modal-field">
                <label>Password Lama</label>
                <input type="password">
            </div>

            <div class="modal-field">
                <label>Password Baru</label>
                <input type="password">
            </div>

            <div class="modal-field">
                <label>Konfirmasi Password Baru</label>
                <input type="password">
            </div>

            <button class="save-password-btn">
                Simpan
        </button>

    </div>

</div>

<script>
function openEditModal(){
    document.getElementById('editModal').classList.add('show');
}

function closeEditModal(){
    document.getElementById('editModal').classList.remove('show');
}

function openPasswordModal(){
    document.getElementById('passwordModal').classList.add('show');
}

function closePasswordModal(){
    document.getElementById('passwordModal').classList.remove('show');
}
</script>

@endsection
```
