<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#F4F7F9; padding:24px; margin:0;">
    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #E5EAF0;">
        <div style="background:#0E3A4D;padding:20px 24px;">
            <span style="color:#fff;font-size:18px;font-weight:700;">SiLapor</span>
        </div>
        <div style="padding:24px;text-align:center;">
            <h2 style="color:#111827;margin:0 0 8px;">Kode Reset Password</h2>
            <p style="color:#6B7280;font-size:14px;margin:0 0 20px;">
                Halo {{ $user->nama }}, gunakan kode berikut untuk reset password kamu:
            </p>

            <div style="font-size:32px;font-weight:700;letter-spacing:10px;color:#29ABE2;margin:16px 0;">
                {{ $otp }}
            </div>

            <p style="color:#9CA3AF;font-size:13px;">
                Kode ini berlaku selama <strong>10 menit</strong>.
                Jangan bagikan kode ini ke siapa pun.
            </p>

            <p style="color:#9CA3AF;font-size:12px;margin-top:20px;">
                Kalau kamu tidak meminta reset password, abaikan email ini.
            </p>
        </div>
    </div>
</body>
</html>
