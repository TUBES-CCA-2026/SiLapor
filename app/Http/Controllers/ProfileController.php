<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->load('profile', 'roleData', 'laboratoriumDikoordinatori');

        return view('profile.index', compact('user'));
    }



    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user()->load('profile', 'laboratoriumDikoordinatori');

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'nama' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email,' . $user->id_user . ',id_user'],
            'no_hp' => ['nullable', 'string', 'max:13'],
            'phone' => ['nullable', 'string', 'max:13'],
            'nim' => ['nullable', 'numeric', 'digits:11'],
            'jurusan' => ['nullable', 'string', 'max:20'],
            'peminatan' => ['nullable', 'string', 'max:20'],
            'pj' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $nama = $validated['name'] ?? $validated['nama'] ?? $user->nama;
        $phone = $validated['no_hp'] ?? $validated['phone'] ?? $user->phone;

        $payload = [
            'nama' => $nama,
            'email' => $validated['email'],
            'phone' => $phone,
        ];

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            $payload['foto'] = $request->file('foto')->store('profiles', 'public');
        }

        $user->update($payload);

        $showProfileFields = in_array($user->role, ['asisten'], true);

        if ($showProfileFields) {
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

        return back()
            ->with('success', 'Profil berhasil diperbarui.')
            ->with('profile_form', 'profile');
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

        if ($this->passwordLooksSimilar($validated['current_password'], $validated['password'])) {
            return back()
                ->withErrors(['password' => 'Password baru tidak boleh sama atau terlalu mirip dengan password lama. Gunakan kombinasi yang benar-benar berbeda.'])
                ->withInput($request->except(['current_password', 'password', 'password_confirmation']));
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()
            ->with('success', 'Kata sandi berhasil diubah.')
            ->with('profile_form', 'password');
    }

    public function showPhoto(User $user)
    {
        $fallback = 'https://ui-avatars.com/api/?name=' . urlencode($user->nama ?: $user->email ?: 'User') . '&background=FFFFFF&color=0090F5';
        $photoPath = ltrim((string) $user->foto, '/');

        if ($photoPath === '') {
            return redirect()->away($fallback);
        }

        $storagePath = storage_path('app/public/' . $photoPath);
        $publicPath = public_path('storage/' . $photoPath);

        if (is_file($storagePath)) {
            return response()->file($storagePath, [
                'Cache-Control' => 'private, max-age=86400',
            ]);
        }

        if (is_file($publicPath)) {
            return response()->file($publicPath, [
                'Cache-Control' => 'private, max-age=86400',
            ]);
        }

        return redirect()->away($fallback);
    }

    private function passwordLooksSimilar(string $currentPassword, string $newPassword): bool
    {
        $current = strtolower(trim($currentPassword));
        $new = strtolower(trim($newPassword));

        if ($current === $new) {
            return true;
        }

        if (strlen($current) >= 5 && (str_contains($new, $current) || str_contains($current, $new))) {
            return true;
        }

        similar_text($current, $new, $percentage);

        return $percentage >= 70;
    }

}
      

