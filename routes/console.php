<?php

use App\Models\PengaduanFoto;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('silapor:migrate-foto-db', function () {
    $fotos = PengaduanFoto::query()
        ->whereNull('file_data')
        ->get();

    $migrated = 0;
    $skipped = 0;

    foreach ($fotos as $foto) {
        if (!blank($foto->file_base64)) {
            $binary = base64_decode($foto->file_base64, true);

            if ($binary !== false && $binary !== '') {
                $foto->forceFill([
                    'file_path' => 'database',
                    'file_data' => $binary,
                    'file_base64' => null,
                ])->save();

                $migrated++;
                continue;
            }
        }

        $path = str_replace('\\', '/', trim((string) $foto->file_path));

        if ($path === '' || $path === 'database' || preg_match('/^https?:\/\//i', $path)) {
            $skipped++;
            continue;
        }

        $relativePath = $path;

        if (str_starts_with($relativePath, '/storage/')) {
            $relativePath = substr($relativePath, strlen('/storage/'));
        } elseif (str_starts_with($relativePath, 'storage/')) {
            $relativePath = substr($relativePath, strlen('storage/'));
        } elseif (str_starts_with($relativePath, 'public/')) {
            $relativePath = substr($relativePath, strlen('public/'));
        }

        $candidates = [
            storage_path('app/public/' . ltrim($relativePath, '/')),
            public_path('storage/' . ltrim($relativePath, '/')),
            public_path(ltrim($path, '/')),
            base_path(ltrim($path, '/')),
        ];

        $sourceFile = collect($candidates)->first(fn (string $candidate) => is_file($candidate));

        if (!$sourceFile) {
            $this->warn("Lewati id_foto {$foto->id_foto}: file tidak ditemukan ({$foto->file_path}).");
            $skipped++;
            continue;
        }

        $foto->forceFill([
            'file_path' => 'database',
            'file_data' => file_get_contents($sourceFile),
            'file_base64' => null,
            'mime_type' => mime_content_type($sourceFile) ?: 'image/jpeg',
            'original_name' => basename($sourceFile),
            'file_size' => filesize($sourceFile) ?: null,
        ])->save();

        $migrated++;
    }

    $this->info("Selesai. Foto dimasukkan ke tabel database: {$migrated}. Dilewati: {$skipped}.");
})->purpose('Move existing stored report photos into database table');

