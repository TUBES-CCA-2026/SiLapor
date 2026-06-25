<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanFoto extends Model
{
    protected $table = 'pengaduan_foto';
    protected $primaryKey = 'id_foto';
    public $timestamps = false;

    protected $fillable = [
        'id_pengaduan',
        'file_path',
        'file_data',
        'file_base64',
        'mime_type',
        'original_name',
        'file_size',
        'created_at',
    ];

    protected $hidden = [
        'file_data',
        'file_base64',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'url',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->file_data !== null || !blank($this->file_base64)) {
            return route('pengaduan-foto.show', $this->id_foto);
        }

        return self::urlFor($this->file_path);
    }

    /**
     * Normalisasi path foto lama agar data lama yang masih tersimpan di storage
     * tetap bisa dibaca. Upload baru tidak lagi menggunakan storage project.
     */
    public static function urlFor(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));

        if ($path === '' || $path === 'database') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
