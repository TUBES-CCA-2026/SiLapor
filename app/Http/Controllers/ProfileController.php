<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $user->update([
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['no_hp'] ?? null,
        ]);

        $profile = [
            'nim' => $validated['nim'] ?? null,
            'jurusan' => $validated['jurusan'] ?? null,
            'peminatan' => $validated['peminatan'] ?? null,
            'penanggung_jawab' => $validated['pj'] ?? null,
        ];

        if (array_filter($profile, fn ($value) => $value !== null && $value !== '')) {
            $user->profile()->updateOrCreate(['id_user' => $user->id_user], $profile);
        } else {
            $user->profile?->delete();
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
