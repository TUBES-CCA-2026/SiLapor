<?php

namespace App\Http\Controllers;

use App\Models\FasilitasLab;
use App\Models\Laboratorium;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = FasilitasLab::with(['laboratorium', 'kategori'])
            ->activeQr()
            ->orderBy('id_laboratorium')
            ->orderBy('nama_fasilitas')
            ->get();

        $laboratoriums = Laboratorium::orderBy('nama_laboratorium')->get();
        $categories = \App\Models\KategoriFasilitas::orderBy('nama_kategori')->get();

        return view('fasilitas.index', compact('fasilitas', 'laboratoriums', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_fasilitas' => ['required', 'string', 'max:120'],
            'id_laboratorium' => ['required', 'exists:laboratorium,id_laboratorium'],
            'id_kategori' => ['nullable', 'exists:kategori_fasilitas,id_kategori'],
            'no_fasilitas' => ['nullable', 'string', 'max:120'],
        ]);

        $validated['qr_code'] = Str::uuid()->toString(); // token unik untuk QR
        $validated['qr_generated_date'] = now();

        $fasilitas = FasilitasLab::create($validated);

        return back()
            ->with('success', 'Fasilitas baru berhasil ditambahkan & QR siap dicetak.')
            ->with('new_fasilitas_id', $fasilitas->id_fasilitas);
    }

    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori_fasilitas,nama_kategori'],
        ], [
            'nama_kategori.unique' => 'Nama kategori ini sudah terdaftar.',
        ]);

        \App\Models\KategoriFasilitas::create($validated);

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Regenerasi token QR (kalau QR fisik hilang/rusak, token lama otomatis
     * tidak berlaku lagi).
     */
    public function regenerateQr(FasilitasLab $fasilitas)
    {
        $fasilitas->update([
            'qr_code' => Str::uuid()->toString(),
            'qr_generated_date' => now(),
            'qr_deleted_at' => null,
        ]);

        return back()
            ->with('success', 'QR Code untuk ' . $fasilitas->nama_fasilitas . ' berhasil diperbarui.')
            ->with('new_fasilitas_id', $fasilitas->id_fasilitas);
    }


    public function deleteQr(FasilitasLab $fasilitas)
    {
        $namaFasilitas = $fasilitas->nama_fasilitas;

        $fasilitas->update([
            'qr_code' => null,
            'qr_generated_date' => null,
            'qr_deleted_at' => now(),
        ]);

        return back()->with('success', 'QR Code untuk ' . $namaFasilitas . ' berhasil dihapus. Data fasilitas tersebut otomatis tidak tampil di Fasilitas & QR dan tidak dihitung lagi pada Laboratorium.');
    }

    public function importTemplate()
    {
        $content = implode("\n", [
            'nama_fasilitas,kode_laboratorium,no_fasilitas',
            'Komputer Client A,LAB-COMP-1,PC-001',
            'Switch Cisco 24 Port,LAB-COMP-2,SW-002',
            'Proyektor Epson,LAB-COMP-1,PR-003',
        ]);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_fasilitas_silapor.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'spreadsheet' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:5120'],
        ], [
            'spreadsheet.required' => 'File spreadsheet wajib dipilih.',
            'spreadsheet.mimes' => 'Format file harus .xlsx atau .csv.',
        ]);

        $rows = $this->readSpreadsheetRows($request->file('spreadsheet'));

        if (count($rows) < 2) {
            return back()->with('error', 'File import belum memiliki data fasilitas. Pastikan baris pertama adalah header kolom.');
        }

        $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), array_shift($rows));
        $created = 0;
        $skipped = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if ($this->rowIsEmpty($row)) {
                continue;
            }

            $data = [];
            foreach ($headers as $columnIndex => $header) {
                if ($header === '') {
                    continue;
                }

                $data[$header] = trim((string) ($row[$columnIndex] ?? ''));
            }

            $namaFasilitas = $this->firstImportValue($data, ['nama_fasilitas', 'nama', 'fasilitas', 'name']);
            $noFasilitas = $this->firstImportValue($data, ['no_fasilitas', 'nomor_fasilitas', 'kode_barang', 'kode', 'no']);
            $labIdent = $this->firstImportValue($data, ['kode_laboratorium', 'nama_laboratorium', 'id_laboratorium', 'laboratorium', 'lab']);

            if (empty($namaFasilitas)) {
                $skipped[] = 'Baris ' . $rowNumber . ': nama_fasilitas wajib diisi.';
                continue;
            }

            if (empty($labIdent)) {
                $skipped[] = 'Baris ' . $rowNumber . ': kolom laboratorium (kode_laboratorium / nama_laboratorium) wajib diisi.';
                continue;
            }

            // Look up laboratorium
            $lab = null;
            // 1. By code
            $lab = Laboratorium::where('kode_laboratorium', $labIdent)->first();
            
            if (!$lab) {
                // 2. By name
                $lab = Laboratorium::where('nama_laboratorium', $labIdent)->first();
            }

            if (!$lab && is_numeric($labIdent)) {
                // 3. By ID
                $lab = Laboratorium::find((int)$labIdent);
            }

            if (!$lab) {
                $skipped[] = 'Baris ' . $rowNumber . ': Laboratorium "' . $labIdent . '" tidak ditemukan.';
                continue;
            }

            try {
                FasilitasLab::create([
                    'nama_fasilitas' => $namaFasilitas,
                    'id_laboratorium' => $lab->id_laboratorium,
                    'no_fasilitas' => $noFasilitas ?: null,
                    'qr_code' => Str::uuid()->toString(),
                    'qr_generated_date' => now(),
                ]);

                $created++;
            } catch (\Throwable $exception) {
                $skipped[] = 'Baris ' . $rowNumber . ': data gagal diimport.';
            }
        }

        $message = $created . ' fasilitas berhasil diimport.';
        if (count($skipped) > 0) {
            $message .= ' ' . count($skipped) . ' baris dilewati.';
        }

        return back()
            ->with($created > 0 ? 'success' : 'error', $created > 0 ? $message : 'Tidak ada fasilitas yang berhasil diimport.')
            ->with('import_errors', $skipped);
    }

    protected function readSpreadsheetRows($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['csv', 'txt'], true)) {
            return $this->readCsvRows($file->getRealPath());
        }

        if ($extension === 'xlsx') {
            return $this->readXlsxRows($file->getRealPath());
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'spreadsheet' => 'Format file belum didukung. Gunakan .xlsx atau .csv.',
        ]);
    }

    protected function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'spreadsheet' => 'File CSV tidak bisa dibaca.',
            ]);
        }

        $firstLine = fgets($handle) ?: '';
        rewind($handle);

        $delimiter = $this->detectCsvDelimiter($firstLine);
        $rows = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $row);
        }

        fclose($handle);

        return $rows;
    }

    protected function detectCsvDelimiter(string $line): string
    {
        $delimiters = [',', ';', "\t"];
        $scores = [];

        foreach ($delimiters as $delimiter) {
            $scores[$delimiter] = substr_count($line, $delimiter);
        }

        arsort($scores);

        return array_key_first($scores) ?: ',';
    }

    protected function readXlsxRows(string $path): array
    {
        if (!class_exists(\ZipArchive::class)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'spreadsheet' => 'Server belum mengaktifkan ekstensi PHP ZipArchive. Gunakan CSV atau aktifkan ext-zip untuk import .xlsx.',
            ]);
        }

        if (!function_exists('simplexml_load_string')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'spreadsheet' => 'Server belum mengaktifkan ekstensi PHP SimpleXML. Gunakan CSV atau aktifkan php-xml untuk import .xlsx.',
            ]);
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'spreadsheet' => 'File XLSX tidak bisa dibuka.',
            ]);
        }

        $sheetName = $zip->locateName('xl/worksheets/sheet1.xml') !== false
            ? 'xl/worksheets/sheet1.xml'
            : null;

        if (!$sheetName) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_starts_with($name, 'xl/worksheets/') && str_ends_with($name, '.xml')) {
                    $sheetName = $name;
                    break;
                }
            }
        }

        if (!$sheetName) {
            $zip->close();
            throw \Illuminate\Validation\ValidationException::withMessages([
                'spreadsheet' => 'Worksheet pada file XLSX tidak ditemukan.',
            ]);
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = simplexml_load_string((string) $zip->getFromName($sheetName));
        $zip->close();

        if (!$sheetXml) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'spreadsheet' => 'Worksheet pada file XLSX tidak bisa dibaca.',
            ]);
        }

        $ns = $sheetXml->getNamespaces(true);
        $sheetChildren = isset($ns['']) ? $sheetXml->children($ns['']) : $sheetXml->children();
        $sheetData = $sheetChildren->sheetData ?? null;
        $rows = [];

        if (!$sheetData) {
            return $rows;
        }

        foreach ($sheetData->children() as $rowXml) {
            $rowValues = [];
            $maxColumn = -1;

            foreach ($rowXml->children() as $cell) {
                $reference = (string) ($cell['r'] ?? '');
                $columnIndex = $reference !== '' ? $this->xlsxColumnIndex($reference) : count($rowValues);
                $rowValues[$columnIndex] = $this->xlsxCellValue($cell, $sharedStrings);
                $maxColumn = max($maxColumn, $columnIndex);
            }

            $normalizedRow = [];
            for ($i = 0; $i <= $maxColumn; $i++) {
                $normalizedRow[] = $rowValues[$i] ?? '';
            }

            $rows[] = $normalizedRow;
        }

        return $rows;
    }

    protected function readSharedStrings(\ZipArchive $zip): array
    {
        $sharedStrings = [];

        if ($zip->locateName('xl/sharedStrings.xml') === false) {
            return $sharedStrings;
        }

        $xml = simplexml_load_string((string) $zip->getFromName('xl/sharedStrings.xml'));
        if (!$xml) {
            return $sharedStrings;
        }

        $ns = $xml->getNamespaces(true);
        $children = isset($ns['']) ? $xml->children($ns['']) : $xml->children();

        foreach ($children as $item) {
            $sharedStrings[] = $this->xmlText($item);
        }

        return $sharedStrings;
    }

    protected function xlsxCellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) ($cell['t'] ?? '');
        $ns = $cell->getNamespaces(true);
        $children = isset($ns['']) ? $cell->children($ns['']) : $cell->children();

        if ($type === 's') {
            $index = (int) ($children->v ?? 0);
            return trim((string) ($sharedStrings[$index] ?? ''));
        }

        if ($type === 'inlineStr') {
            return trim($this->xmlText($children->is));
        }

        return trim((string) ($children->v ?? ''));
    }

    protected function xmlText($element): string
    {
        if (!$element instanceof \SimpleXMLElement) {
            return '';
        }

        $text = '';
        foreach (($element->xpath('.//*[local-name()="t"]') ?: []) as $node) {
            $text .= (string) $node;
        }

        return trim($text !== '' ? $text : (string) $element);
    }

    protected function xlsxColumnIndex(string $reference): int
    {
        preg_match('/^[A-Z]+/i', $reference, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return $index - 1;
    }

    protected function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;
        $header = strtolower(trim($header));
        $header = str_replace([' ', '-', '/'], '_', $header);
        $header = preg_replace('/[^a-z0-9_]/', '', $header) ?? $header;

        return trim($header, '_');
    }

    protected function firstImportValue(array $data, array $keys): string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && trim((string) $data[$key]) !== '') {
                return trim((string) $data[$key]);
            }
        }

        return '';
    }

    protected function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
