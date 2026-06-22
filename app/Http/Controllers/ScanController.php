<?php

namespace App\Http\Controllers;

class ScanController extends Controller
{
    /**
     * Halaman publik berisi kamera scanner (html5-qrcode).
     * Begitu QR terbaca, JS langsung redirect browser ke URL hasil decode
     * (URL itu sendiri berbentuk /lapor/{qr_code}, lihat FasilitasLab::scanUrl()).
     * Tidak perlu login untuk membuka halaman ini.
     */
    public function index()
    {
        return view('scan.index');
    }
}
