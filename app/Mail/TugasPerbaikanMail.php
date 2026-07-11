<?php

namespace App\Mail;

use App\Models\TindakLanjut;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TugasPerbaikanMail extends Mailable
{
    use Queueable, SerializesModels;

    public TindakLanjut $tindakLanjut;

    public function __construct(TindakLanjut $tindakLanjut)
    {
        $this->tindakLanjut = $tindakLanjut;
    }

    public function build()
    {
        $pengaduan = $this->tindakLanjut->pengaduan;
        $fasilitas = $pengaduan->fasilitas;

        return $this->subject('Tugas Perbaikan Baru — ' . ($fasilitas->kategori?->nama_kategori ?? '-') . ' (' . ($fasilitas->no_fasilitas ?? '-') . ')')
            ->view('emails.tugas-perbaikan', [
                'asisten' => $this->tindakLanjut->asisten,
                'fasilitas' => $fasilitas,
                'pengaduan' => $pengaduan,
                'tindakLanjut' => $this->tindakLanjut,
            ]);
    }
}
