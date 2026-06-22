<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected array $roles = ['asisten', 'laboran', 'koordinator_lab', 'kepala_lab', 'admin'];

    public function index()
    {
        $users = User::orderBy('role')->orderBy('nama')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', ['roles' => $this->roles]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:' . implode(',', $this->roles)],
            'phone' => ['nullable', 'string', 'max:15'],
            'nim' => ['nullable', 'string', 'max:12'],
            'jurusan' => ['nullable', 'string', 'max:20'],
            'peminatan' => ['nullable', 'string', 'max:20'],
            'penanggung_jawab' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => $this->roles]);
    }

    /**
     * Update data profil (TANPA password — password punya form & route terpisah
     * di bawah, supaya tidak ke-reset tidak sengaja saat admin cuma edit profil).
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email,' . $user->id_user . ',id_user'],
            'role' => ['required', 'in:' . implode(',', $this->roles)],
            'phone' => ['nullable', 'string', 'max:15'],
            'nim' => ['nullable', 'string', 'max:12'],
            'jurusan' => ['nullable', 'string', 'max:20'],
            'peminatan' => ['nullable', 'string', 'max:20'],
            'penanggung_jawab' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil ' . $user->nama . ' berhasil diperbarui.');
    }

    /**
     * Admin LANGSUNG membuatkan password baru untuk user lain — tanpa OTP,
     * tanpa email, langsung aktif begitu disimpan.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password untuk ' . $user->nama . ' berhasil diganti.');
    }

    public function destroy(User $user)
    {
        if ($user->id_user === Auth::id()) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus akun yang sedang login.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
