<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    const MAX_ATTEMPTS = 3;
    const LOCK_MINUTES = 15;

    // Tampilkan halaman login (sesuai desain SiLapor)
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Sesuai flowchart:
     * Login gagal -> "Tambah jumlah kesalahan login" -> "Salah 3x atau lebih?"
     * -> YA -> "Tunggu 15 menit" -> baru boleh coba lagi setelah "waktu blokir selesai".
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Nama pengguna / email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $email = strtolower($credentials['email']);
        $lockKey = 'login_lock_' . $email;
        $attemptsKey = 'login_attempts_' . $email;

        // Cek dulu: apakah akun ini masih dalam masa blokir?
        if (Cache::has($lockKey)) {
            $unlockAt = Cache::get($lockKey);
            $minutesLeft = max(1, now()->diffInMinutes($unlockAt));

            return back()
                ->withErrors(['email' => "Terlalu banyak percobaan gagal. Akun ini diblokir sementara, coba lagi dalam {$minutesLeft} menit."])
                ->withInput($request->only('email'));
        }

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            // "Tambah jumlah kesalahan login" — dihitung per email, bertahan 15 menit
            // sejak kesalahan PERTAMA (supaya tidak terus reset selama masih nyoba-nyoba).
            $attempts = Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(self::LOCK_MINUTES));

            // "Salah 3x atau lebih?"
            if ($attempts >= self::MAX_ATTEMPTS) {
                Cache::put($lockKey, now()->addMinutes(self::LOCK_MINUTES), now()->addMinutes(self::LOCK_MINUTES));
                Cache::forget($attemptsKey);

                return back()
                    ->withErrors(['email' => 'Anda salah memasukkan password 3 kali. Akun diblokir selama 15 menit, atau gunakan "Lupa Password".'])
                    ->withInput($request->only('email'));
            }

            $sisa = self::MAX_ATTEMPTS - $attempts;
            return back()
                ->withErrors(['email' => "Username atau password salah. Sisa kesempatan: {$sisa} kali."])
                ->withInput($request->only('email'));
        }

        // Login berhasil -> "waktu blokir selesai" / reset hitungan kesalahan
        Cache::forget($attemptsKey);
        Cache::forget($lockKey);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
