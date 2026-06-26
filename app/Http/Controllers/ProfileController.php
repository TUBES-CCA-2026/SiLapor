<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->load('profile', 'roleData');

        return view('profile.index', compact('user'));
    }

    public function edit(): View
    {
        $user = Auth::user()->load('profile', 'roleData');

        return view('profile.index', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email,' . $user->id_user . ',id_user'],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'nim' => ['nullable', 'string', 'max:12'],
            'jurusan' => ['nullable', 'string', 'max:20'],
            'peminatan' => ['nullable', 'string', 'max:20'],
            'pj' => ['nullable', 'string', 'max:20'],
        ]);

        $hideProfileFields = in_array($user->role, ['koordinator_lab', 'laboran', 'kepala_lab'], true);

        $user->update([
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $hideProfileFields ? $user->phone : ($validated['no_hp'] ?? $user->phone),
        ]);

        if ($hideProfileFields) {
            $user->profile?->delete();
        } else {
            $profile = [
                'nim' => $validated['nim'] ?? $user->profile?->nim,
                'jurusan' => $validated['jurusan'] ?? $user->profile?->jurusan,
                'peminatan' => $validated['peminatan'] ?? $user->profile?->peminatan,
                'penanggung_jawab' => $validated['pj'] ?? $user->profile?->penanggung_jawab,
            ];

            if (array_filter($profile, fn ($value) => $value !== null && $value !== '')) {
                $user->profile()->updateOrCreate(['id_user' => $user->id_user], $profile);
            } else {
                $user->profile?->delete();
            }
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
