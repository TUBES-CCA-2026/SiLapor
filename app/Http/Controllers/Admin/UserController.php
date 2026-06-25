<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected array $roles = ['asisten', 'laboran', 'koordinator_lab', 'kepala_lab', 'admin'];

    public function index()
    {
        $users = User::with(['roleData', 'profile'])
            ->join('roles', 'users.id_role', '=', 'roles.id_role')
            ->orderBy('roles.nama_role')
            ->orderBy('users.nama')
            ->select('users.*')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', ['roles' => $this->roles]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'id_role' => Role::idByName($validated['role']),
            'phone' => $validated['phone'] ?? null,
        ]);

        $this->syncProfile($user, $validated);

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $user->load(['roleData', 'profile']);

        return view('admin.users.edit', ['user' => $user, 'roles' => $this->roles]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, $user);

        $user->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'id_role' => Role::idByName($validated['role']),
            'phone' => $validated['phone'] ?? null,
        ]);

        $this->syncProfile($user, $validated);

        return back()->with('success', 'Profil ' . $user->nama . ' berhasil diperbarui.');
    }

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

    protected function validateUser(Request $request, ?User $user = null): array
    {
        $emailRule = $user
            ? 'unique:users,email,' . $user->id_user . ',id_user'
            : 'unique:users,email';

        return $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', $emailRule],
            'password' => $user ? ['sometimes', 'nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'role' => ['required', 'in:' . implode(',', $this->roles)],
            'phone' => ['nullable', 'string', 'max:15'],
            'nim' => ['nullable', 'string', 'max:12'],
            'jurusan' => ['nullable', 'string', 'max:20'],
            'peminatan' => ['nullable', 'string', 'max:20'],
            'penanggung_jawab' => ['nullable', 'string', 'max:20'],
        ]);
    }

    protected function syncProfile(User $user, array $validated): void
    {
        $profile = [
            'nim' => $validated['nim'] ?? null,
            'jurusan' => $validated['jurusan'] ?? null,
            'peminatan' => $validated['peminatan'] ?? null,
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
        ];

        if (array_filter($profile, fn ($value) => $value !== null && $value !== '')) {
            $user->profile()->updateOrCreate(['id_user' => $user->id_user], $profile);
        } else {
            $user->profile?->delete();
        }
    }
}
