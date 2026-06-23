<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan; // Pastikan ini ada!
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema; // Tambahkan ini

class RiwayatController extends Controller
{
    public function index()
    {
        // Debug: Cek apakah kolom status ada di tabel pengaduan
        if (!Schema::hasColumn('pengaduan', 'status_pengaduan')) {
            dd("Error: Kolom 'status' tidak ditemukan di tabel 'pengaduan'. Cek nama kolom Anda!");
        }

        $riwayat = Pengaduan::where('status_pengaduan', 'DONE')
                            ->latest()
                            ->get();

        return view('riwayat.index', compact('riwayat'));
    }
}


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan'; // Pastikan nama tabelnya benar
    protected $guarded = []; // Atau isi $fillable jika ada
}
