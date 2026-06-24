<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Menampilkan data profil user.
     */
    public function index()
    {
        // Mengambil data user yang sedang login
        $user = Auth::user(); 
        return view('profile.index', compact('user'));
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
}