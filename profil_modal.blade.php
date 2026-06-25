@extends('layouts.silapor-dashboard')
@section('content')
<style>
/* tempel ke halaman profil */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:999}
.modal-backdrop.show{display:block}
.modal-box{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:500px;background:#fff;border-radius:20px;padding:16px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.field input{width:100%;padding:8px}
.full{grid-column:1/-1}
</style>

<button onclick="openEditModal()">Edit Profil ✎</button>

<div id="editModal" class="modal-backdrop">
<div class="modal-box">
<div style="display:flex;justify-content:space-between"><b>Edit Profil</b><span onclick="closeEditModal()">✕</span></div>
<div style="text-align:center"><img src="{{ asset('images/alisa.jpg') }}" width="90"><br><button>Ganti Profil</button></div>
<div class="form-grid">
<div class="field"><label>Nama Koordinator</label><input></div>
<div class="field"><label>ID Koordinator</label><input></div>
<div class="field full"><label>Email</label><input></div>
<div class="field"><label>No Hp</label><input></div>
<div class="field"><label>Role</label><input></div>
</div>
<button>Simpan</button>
</div></div>
<script>
function openEditModal(){document.getElementById('editModal').classList.add('show')}
function closeEditModal(){document.getElementById('editModal').classList.remove('show')}
</script>
@endsection