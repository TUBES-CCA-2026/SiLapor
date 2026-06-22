<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#F4F7F9; padding:24px; margin:0;">
    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #E5EAF0;">
        <div style="background:#0E3A4D;padding:20px 24px;">
            <span style="color:#fff;font-size:18px;font-weight:700;">SiLapor</span>
        </div>
        <div style="padding:24px;">
            <h2 style="color:#111827;margin:0 0 12px;">Tugas Perbaikan Baru</h2>
            <p style="color:#374151;font-size:14px;">Halo {{ $asisten->nama }},</p>
            <p style="color:#374151;font-size:14px;">
                Kamu ditugaskan untuk memperbaiki fasilitas berikut:
            </p>

            <table style="width:100%;font-size:14px;color:#374151;margin:16px 0;">
                <tr>
                    <td style="padding:4px 0;color:#6B7280;">Fasilitas</td>
                    <td style="padding:4px 0;font-weight:600;">{{ $fasilitas->nama_fasilitas }}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#6B7280;">Laboratorium</td>
                    <td style="padding:4px 0;font-weight:600;">{{ $fasilitas->laboratorium->nama_laboratorium }}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#6B7280;">Deskripsi</td>
                    <td style="padding:4px 0;">{{ $pengaduan->deskripsi_kerusakan }}</td>
                </tr>
            </table>

            <a href="{{ url('/dashboard') }}"
               style="display:inline-block;background:#29ABE2;color:#fff;text-decoration:none;
                      padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;">
                Buka Dashboard
            </a>
        </div>
    </div>
</body>
</html>
