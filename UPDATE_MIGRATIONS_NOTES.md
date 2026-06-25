# Update Migrasi SiLapor

Struktur database sudah disesuaikan dengan ERD baru pada database `silapor_db`.

## Perubahan utama

- `users.role` dipindahkan menjadi relasi `users.id_role` ke tabel `roles`.
- Data profil user (`nim`, `jurusan`, `peminatan`, `penanggung_jawab`) dipindahkan ke tabel `user_profiles`.
- Status pengaduan, penanganan, dan pengiriman dipindahkan menjadi tabel master:
  - `status_pengaduan`
  - `status_penanganan`
  - `status_pengiriman`
- Foto laporan dipindahkan dari kolom `pengaduan.foto_kerusakan` menjadi tabel `pengaduan_foto`.
- `tindak_lanjut.id_asisten` disesuaikan menjadi `tindak_lanjut.id_petugas`.
- `notifikasi.id_asisten` dan `email_tujuan` disesuaikan menjadi `id_user_penerima` dengan status pengiriman berbasis FK.
- `password_reset_otps.email` disesuaikan menjadi `password_reset_otps.id_user`.

## File yang ikut disesuaikan

- Semua migration di `database/migrations`.
- Model relasi baru di `app/Models`.
- Controller pengaduan, tindak lanjut, user admin, laboratorium, dashboard, dan forgot password.
- Seeder master data di `database/seeders/ReferenceDataSeeder.php`.
- Form user admin agar data profil tetap tersimpan ke `user_profiles`.

## Cara reset database lokal

```bash
php artisan migrate:fresh --seed
```

Setelah itu login awal memakai akun dari `UserSeeder`, contoh:

- `admin@silapor.test`
- password: `password`
