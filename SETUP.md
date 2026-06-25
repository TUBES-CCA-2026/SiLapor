
# SETUP — Fitur Login, Notifikasi Asisten & Scan QR (SiLapor)

File-file di paket ini dibuat berdiri sendiri (tanpa lihat project asli kamu, karena
`SiLapor.zip` gagal terupload). Cara pakainya: **copy folder/file ini ke lokasi yang
sama** di project Laravel kamu yang sudah ada, lalu sesuaikan kalau ada nama
controller/model yang bentrok.

## 1. Copy file
```
database/migrations/*.php        -> database/migrations/
app/Models/*.php                 -> app/Models/
app/Http/Controllers/**/*.php    -> app/Http/Controllers/
app/Http/Middleware/*.php        -> app/Http/Middleware/
app/Mail/*.php                   -> app/Mail/
resources/views/**/*.blade.php   -> resources/views/
routes/web.php                   -> SALIN ISINYA (jangan timpa file routes/web.php
                                     existing kamu), tempel di bawahnya.
```

## 2. Daftarkan middleware `role`

**Laravel 11+** — di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

**Laravel 10 ke bawah** — di `app/Http/Kernel.php`, dalam `$middlewareAliases`:
```php
'role' => \App\Http\Middleware\RoleMiddleware::class,
```

## 3. Konfigurasi Auth
Pastikan `config/auth.php` provider `users` memakai model `App\Models\User::class`
(default Laravel sudah begitu).

## 4. Storage untuk foto kerusakan
```bash
php artisan storage:link
```
Ini supaya file di `storage/app/public/pengaduan/...` bisa diakses lewat URL publik.

## 5. Konfigurasi email (.env)
Untuk uji coba lokal tanpa kirim email asli, pakai driver `log` dulu:
```
MAIL_MAILER=log
```
Nanti isi pengiriman email akan masuk ke `storage/logs/laravel.log`. Kalau sudah siap
pakai SMTP asli (Gmail/Mailtrap/dll), ganti `MAIL_MAILER=smtp` + isi host/port/user/pass.

## 6. Migrasi & seed user percobaan
```bash
php artisan migrate
php artisan tinker
```
```php
\App\Models\User::create([
    'nama' => 'Budi Asisten', 'email' => 'budi@silapor.test',
    'password' => bcrypt('password'), 'role' => 'asisten',
]);
\App\Models\User::create([
    'nama' => 'Koordinator Lab', 'email' => 'koor@silapor.test',
    'password' => bcrypt('password'), 'role' => 'koordinator_lab',
]);
```

## 7. Alur testing end-to-end
1. Login sebagai **koordinator_lab** → buka `/fasilitas` → tambah 1 fasilitas →
   QR otomatis muncul (di-generate di browser).
2. Logout, atau buka tab **incognito** → buka `/scan` → izinkan kamera →
   arahkan ke QR di langkah 1 (atau ketik manual kode dari URL QR-nya).
3. Halaman lapor terbuka dengan **Nama Barang** & **Lokasi Lab** sudah terisi
   otomatis. Karena belum login, ada banner "melapor sebagai **Guest**".
   Isi foto + deskripsi → kirim.
4. Login lagi sebagai koordinator → di halaman pengaduan (perlu kamu buat
   list view-nya, atau bisa lewat tinker dulu) → assign pengaduan ke asisten
   Budi lewat route `tindak-lanjut.assign`.
5. Cek `storage/logs/laravel.log` → akan ada email notifikasi tugas perbaikan
   untuk Budi. Login sebagai Budi (`budi@silapor.test` / `password`) →
   `/dashboard` akan menampilkan tugas + riwayat notifikasi tersebut.

## Catatan desain penting
- **`pengaduan.id_user` dibuat nullable.** Di ERD asli kolom ini kelihatan wajib,
  tapi supaya guest (tanpa akun) bisa lapor lewat scan QR, kolom ini harus
  boleh `NULL`. Kalau kamu lebih suka tetap NOT NULL, alternatifnya buat satu
  akun khusus "Guest" yang dipakai bersama — tapi itu kurang akurat untuk
  audit/pelacakan siapa yang benar-benar lapor.
- **QR Code TIDAK menyimpan `id_fasilitas` secara langsung** — yang disimpan
  adalah token acak (UUID) di kolom `qr_code`. Ini supaya orang tidak bisa
  menebak-nebak ID fasilitas lain hanya dengan mengubah angka di URL.
- Scan QR & form lapor **sengaja tidak diberi middleware `auth`**, sesuai
  requirement guest mode. Kalau user sudah login, sistem otomatis mendeteksi
  lewat `Auth::check()` tanpa perlu user pilih apa-apa.
