<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';
    
    // Pastikan guarded kosong agar kolom status bisa diakses
    protected $guarded = [];
}