# Catatan Fix Sistem Foto SiLapor

Perbaikan utama:

1. Sistem foto pengaduan distandarkan ke tabel `pengaduan_foto` melalui model `PengaduanFoto`.
2. Model `Pengaduan` sekarang punya helper:
   - `foto_kerusakan`: path foto utama.
   - `foto_kerusakan_url`: URL siap pakai untuk `<img src="...">`.
   - `foto_urls`: semua URL foto pengaduan.
3. Modal detail dashboard, laporan, dan detail-laporan sekarang membaca `data.fotos` dan tetap kompatibel dengan `data.foto`.
4. Halaman asisten dan tindak lanjut sekarang menampilkan foto memakai `foto_kerusakan_url`, bukan merangkai path manual.
5. `routes/web.php` dirapikan dari hasil merge yang dobel/keluar dari middleware auth.
6. `RiwayatController.php` dibersihkan dari deklarasi model yang salah dan query status diperbaiki memakai relasi status.
7. `ProfileController.php` diberi method `update()` agar route profile tidak error.

Setelah extract/pull di Windows Laragon, jalankan:

```bat
FIX_STORAGE_WINDOWS.bat
```

Atau manual:

```bat
rmdir /s /q public\storage
php artisan storage:link
php artisan optimize:clear
```

Jika memakai Linux/Mac:

```bash
./FIX_STORAGE_LINUX_MAC.sh
```

Catatan: foto upload tetap disimpan sesuai standar Laravel di `storage/app/public/pengaduan` dan ditampilkan melalui URL `/storage/pengaduan/...`.


Tambahan revisi:
- Link Lapor Manual pada halaman login sudah diarahkan ke route publik `pengaduan.manual.create` (`/lapor/manual`), bukan route `/pengaduan/create` yang berada di middleware `auth`.
- Upload foto baru disimpan sebagai binary di kolom `file_data` bertipe `LONGBLOB` pada tabel `pengaduan_foto`.
