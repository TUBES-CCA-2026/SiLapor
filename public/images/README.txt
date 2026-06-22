Taruh 2 file gambar kamu di sini, dengan nama PERSIS seperti ini:

1. logo-silapor.png       -> logo/icon SiLapor (disarankan PNG transparan, ukuran persegi, min 128x128px)
2. lab-background.jpg     -> foto laboratorium (untuk panel kiri halaman login & header scan)

Kalau mau nama file beda, ganti juga referensinya di:
- resources/views/auth/login.blade.php   (baris asset('images/lab-background.jpg') & asset('images/logo-silapor.png'))
- resources/views/layouts/app.blade.php  (logo di navbar)
- resources/views/scan/index.blade.php   (logo di halaman scan)
- resources/views/pengaduan/create.blade.php (logo di form lapor)
