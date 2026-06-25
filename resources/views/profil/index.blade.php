
@extends('layouts.silapor-dashboard', [
    'title' => 'Profil | SiLapor',
    'pageTitle' => 'PROFIL',
    'activeMenu' => 'profil'
])

@section('content')
<style>
.profile-card{background:#fff;border:1px solid #e5ebf2;border-radius:24px;padding:28px;box-shadow:0 10px 25px rgba(0,0,0,.05);margin:0 38px 40px}
.profile-top{display:flex;justify-content:flex-end;margin-bottom:20px}
.btn-outline{border:1px solid #bfc8d4;padding:8px 16px;border-radius:10px;text-decoration:none;color:#333;background:#fff}
.profile-grid{display:grid;grid-template-columns:180px 1fr;gap:30px}
.avatar-box{text-align:center}.avatar{width:120px;height:120px;border-radius:10px;object-fit:cover;border:1px solid #ddd}
.badge-role{margin-top:8px;border:1px solid #bbb;border-radius:6px;padding:4px 8px;font-size:12px;display:inline-block}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px 28px}.field label{display:block;margin-bottom:6px;font-size:13px}.field input{width:100%;padding:10px;border:1px solid #aaa;border-radius:8px;background:#fafafa}.full{grid-column:1/-1}.bottom-actions{text-align:right;margin-top:24px}
</style>
<div class="profile-card">
<div class="profile-top"><a href="#" class="btn-outline">Edit Profil ✎</a></div>
<div class="profile-grid">
<div class="avatar-box">
    <img class="avatar" src="{{ asset('images/alisa.jpg') }}" alt="Foto Alisa">
    <div class="badge-role">Koordinator Lab</div>
</div>
<div class="form-grid">
<div class="field"><label>Nama Koordinator</label><input value="{{ auth()->user()->name ?? 'Nur Alisa' }}" readonly></div>
<div class="field"><label>ID Koordinator</label><input value="{{ auth()->id() ?? 'xxxxx' }}" readonly></div>
<div class="field full"><label>Email</label><input value="{{ auth()->user()->email ?? 'koor@silapor.test' }}" readonly></div>
<div class="field"><label>No Hp</label><input value="+62813xxxxxxx" readonly></div>
<div class="field"><label>Role</label><input value="Koordinator LAB" readonly></div>
</div>
<div class="bottom-actions">
    <a href="#" class="btn-outline">Ubah Password ⚙</a>
</div></div></div>
@endsection
