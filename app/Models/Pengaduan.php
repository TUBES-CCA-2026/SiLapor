<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaduan extends Model
{
    use SoftDeletes;

    protected $table = 'pengaduan';
    protected $primaryKey = 'id_pengaduan';

    protected $fillable = [
        'id_user',
        'id_fasilitas',
        'id_status_pengaduan',
        'deskripsi_kerusakan',
        'tanggal_lapor',
        'status_pengaduan',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
    ];

    protected $appends = [
        'foto_kerusakan',
        'foto_kerusakan_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function fasilitas()
    {
        return $this->belongsTo(FasilitasLab::class, 'id_fasilitas', 'id_fasilitas');
    }

    public function statusData()
    {
        return $this->belongsTo(StatusPengaduan::class, 'id_status_pengaduan', 'id_status_pengaduan');
    }

    /**
     * Relasi utama untuk semua foto pengaduan.
     */
    public function fotos()
    {
        return $this->hasMany(PengaduanFoto::class, 'id_pengaduan', 'id_pengaduan')
            ->orderBy('id_foto');
    }

    /**
     * Alias kompatibilitas untuk kode lama yang masih memakai $pengaduan->foto().
     */
    public function foto()
    {
        return $this->fotos();
    }

    public function fotoUtama()
    {
        return $this->hasOne(PengaduanFoto::class, 'id_pengaduan', 'id_pengaduan')
            ->oldestOfMany('id_foto');
    }

    public function tindakLanjut()
    {
        return $this->hasOne(TindakLanjut::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function setStatusPengaduanAttribute(string $value): void
    {
        $this->attributes['id_status_pengaduan'] = StatusPengaduan::idByKode($value);
    }

    public function getStatusPengaduanAttribute(): ?string
    {
        if ($this->relationLoaded('statusData')) {
            return $this->statusData?->kode_status;
        }

        return $this->statusData()->value('kode_status');
    }

    /**
     * Nilai mentah kompatibilitas untuk kode lama.
     */
    public function getFotoKerusakanAttribute(): ?string
    {
        if ($this->relationLoaded('fotoUtama')) {
            return $this->fotoUtama?->file_path;
        }

        if ($this->relationLoaded('fotos')) {
            return $this->fotos->first()?->file_path;
        }

        return $this->fotos()->oldest('id_foto')->value('file_path');
    }

    /**
     * URL siap pakai untuk <img src="...">.
     */
    public function getFotoKerusakanUrlAttribute(): ?string
    {
        if ($this->relationLoaded('fotoUtama')) {
            return $this->fotoUtama?->url;
        }

        if ($this->relationLoaded('fotos')) {
            return $this->fotos->first()?->url;
        }

        return $this->fotos()->oldest('id_foto')->first()?->url;
    }

    /**
     * Daftar semua URL foto untuk modal/detail.
     */
    public function getFotoUrlsAttribute(): array
    {
        $fotos = $this->relationLoaded('fotos')
            ? $this->fotos
            : $this->fotos()->get();

        return $fotos
            ->map(fn (PengaduanFoto $foto) => $foto->url)
            ->filter()
            ->values()
            ->all();
    }

    public function scopeStatusKode($query, string|array $kodeStatus)
    {
        $kodeStatus = (array) $kodeStatus;

        return $query->whereHas('statusData', fn ($q) => $q->whereIn('kode_status', $kodeStatus));
    }


}
