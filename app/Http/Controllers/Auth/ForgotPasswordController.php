<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /**
     * STEP 1: tampilkan form input email.
     */
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * STEP 1 (proses): generate OTP 6 digit, simpan (hashed), kirim ke email.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Pesan sukses SELALU sama walau email tidak ditemukan, supaya orang
        // tidak bisa menebak-nebak email mana saja yang terdaftar di sistem.
        $genericMessage = 'Jika email tersebut terdaftar, kode OTP sudah kami kirim.';

        if (!$user) {
            return redirect()->route('password.request')->with('success', $genericMessage);
        }

        // Anti-spam: tidak boleh minta kode baru < 60 detik dari permintaan terakhir.
        $last = PasswordResetOtp::where('id_user', $user->id_user)->latest('id')->first();
        if ($last && $last->created_at && $last->created_at->diffInSeconds(now()) < 60) {
            return back()->withErrors(['email' => 'Tunggu beberapa saat sebelum minta kode baru.']);
        }

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::create([
            'id_user' => $user->id_user,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user));

        // Simpan email di session, BUKAN di URL, supaya tidak mudah ditebak/dibagikan.
        session(['reset_email' => $user->email]);

        return redirect()->route('password.otp.form')->with('success', $genericMessage);
    }

    /**
     * STEP 2: tampilkan form input kode OTP.
     */
    public function showOtpForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', ['email' => session('reset_email')]);
    }

    /**
     * STEP 2 (proses): cek kode OTP cocok & belum kedaluwarsa (10 menit).
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $email)->firstOrFail();
        $record = PasswordResetOtp::where('id_user', $user->id_user)->latest('id')->first();

        if (!$record || $record->expires_at->isPast() || !Hash::check($request->otp, $record->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kedaluwarsa.']);
        }

        // Tandai sudah lolos verifikasi, baru boleh lanjut ke form set password baru.
        session(['otp_verified' => true]);

        return redirect()->route('password.reset.form');
    }

    /**
     * Kirim ulang OTP (dari halaman verifikasi, tanpa perlu isi email lagi).
     */
    public function resendOtp(Request $request)
    {
        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        return $this->sendOtp($request->merge(['email' => $email]));
    }

    /**
     * STEP 3: tampilkan form set password baru (hanya bisa diakses kalau
     * OTP sudah lolos verifikasi di step sebelumnya).
     */
    public function showResetForm()
    {
        if (!session('reset_email') || !session('otp_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    /**
     * STEP 3 (proses): simpan password baru, hapus OTP & session supaya
     * tidak bisa dipakai ulang.
     */
    public function resetPassword(Request $request)
    {
        if (!session('reset_email') || !session('otp_verified')) {
            return redirect()->route('password.request');
        }

        $validated = $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::where('email', session('reset_email'))->firstOrFail();
        $user->update(['password' => Hash::make($validated['password'])]);

        PasswordResetOtp::where('id_user', $user->id_user)->delete();
        $request->session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
