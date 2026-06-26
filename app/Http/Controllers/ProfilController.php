<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User; 

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        // 1. Ambil ID user yang sedang login (Menggunakan primary key custom Anda)
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        // 2. Validasi Input (Disesuaikan dengan panjang kolom & nama kolom di ERD)
        $request->validate([
            'nama'  => 'required|string|max:120', // Sesuai varchar(120) di DB
            'phone' => 'nullable|string|max:15',  // Sesuai nama kolom 'phone' di DB
            'email' => [
                'required',
                'email',
                'max:120',
                // SOLUSI UTAMA: Mengabaikan email milik user ini sendiri berdasarkan 'id_user'
                Rule::unique('users', 'email')->ignore($userId, 'id_user')
            ],
            'foto'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 3. Mapping data dari Form ke Kolom Database yang Benar
        $user->nama = $request->nama; 
        $user->email = $request->email;
        $user->phone = $request->phone; // Menggunakan 'phone' sesuai database

        // 4. Proses Upload Foto
        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $path = $request->file('foto')->store('profiles', 'public');
            $user->foto = $path;
        }

        $user->save();
        // Mengarahkan langsung ke fungsi index() profil menggunakan GET secara aman
        return redirect()->route('profil.index')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed', 
        ]);

        $user = User::findOrFail(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password Anda berhasil diperbarui!');
    }
}