<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        // Mengirim data user yang sedang login ke view
        $user = Auth::user();
        return view('profil.index', compact('user'));
    }
}
