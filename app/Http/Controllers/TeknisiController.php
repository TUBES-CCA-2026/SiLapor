<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini agar DB bisa terbaca

class TeknisiController extends Controller
{
    public function index()
    {
        // Simpan hasil query ke dalam variabel $teknisi
        $teknisi = DB::table('laporan_teknisi')->get();
        
        // Kirim variabel $teknisi ke view
        return view('teknisi.index', compact('teknisi'));
    }
}