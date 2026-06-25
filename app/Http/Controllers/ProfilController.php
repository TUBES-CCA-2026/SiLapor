<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // WAJIB DITAMBAHKAN agar Eloquent terikat dengan sempurna

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        // Mengambil langsung model User berdasarkan ID yang login untuk menghindari error .save()
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'name'  => 'required|string|max:255', // Ganti ke 'nama' jika kolom DB Anda adalah nama
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:15',
            'foto'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Catatan: Jika di database menggunakan 'nama', ubah $user->name menjadi $user->nama
        $user->name = $request->name; 
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $path = $request->file('foto')->store('profiles', 'public');
            $user->foto = $path;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        // Validasi input form ganti password
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed', // otomatis mengecek password_confirmation
        ]);

        $user = User::findOrFail(Auth::id());

        // Periksa kesesuaian password lama dengan hash di database
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
        }

        // Enkripsi dan simpan password baru
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password Anda berhasil diperbarui!');
    }
}