# Patch Foto Pengaduan Disimpan di Database

Perubahan utama:

1. Upload foto pengaduan baru tidak lagi disimpan ke `storage/app/public` atau `public/storage`.
2. Foto disimpan di tabel `pengaduan_foto`, kolom:
   - `file_base64`
   - `mime_type`
   - `original_name`
   - `file_size`
3. Kolom `file_path` tetap dipertahankan untuk kompatibilitas database lama.
4. Foto ditampilkan melalui route:
   - `GET /pengaduan-foto/{foto}`
5. File lama yang masih berada di storage dapat dipindahkan ke database dengan command:

```bash
php artisan migrate
php artisan silapor:migrate-foto-db
```

Catatan:
- Jalankan `php artisan migrate` terlebih dahulu agar kolom baru masuk ke database.
- Upload baru otomatis masuk database setelah migration selesai.
- Jika foto lama sudah hilang dari folder storage, command migrasi foto lama tidak bisa memulihkannya karena sumber filenya sudah tidak ada.
