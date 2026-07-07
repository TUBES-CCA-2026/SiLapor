<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    // Tampilkan halaman login (sesuai desain SiLapor)
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $rememberEmail = Cookie::get('remember_email');

        return view('auth.login', compact('rememberEmail'));
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

        $remember = $request->boolean('remember');

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Akun belum terdaftar.'])
                ->withInput($request->only('email'));
        }

        if (!Auth::attempt($credentials, $remember)) {
            $attempts = $request->session()->increment('login_attempts');

            if ($attempts >= 3) {
                return back()
                    ->withErrors(['email' => 'Terlalu banyak percobaan gagal. Coba lagi nanti atau gunakan "Lupa Password".'])
                    ->withInput($request->only('email'));
            }

            return back()
                ->withErrors(['email' => 'Username atau password salah.'])
                ->withInput($request->only('email'));
        }

        // Simpan email ke cookie jika "Remember Me" dicentang
        if ($remember) {
            Cookie::queue('remember_email', $credentials['email'], 1209600); // 14 hari
        } else {
            Cookie::queue(Cookie::forget('remember_email'));
        }

        $request->session()->forget('login_attempts');
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
