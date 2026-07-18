<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected array $roles = ['asisten', 'laboran', 'koordinator_lab', 'kepala_lab'];

    protected array $roleLimits = [
        'laboran' => 1,
        'koordinator_lab' => 7,
        'kepala_lab' => 3,
    ];

    public function index()
    {
        $users = User::with(['roleData', 'profile'])
            ->join('roles', 'users.id_role', '=', 'roles.id_role')
            ->where('users.id_user', '!=', Auth::id())
            ->where('roles.nama_role', '!=', 'admin')
            ->orderBy('roles.nama_role')
            ->orderBy('users.nama')
            ->select('users.*')
            ->get();

        return view('admin.users.index', compact('users'));
    }


    public function import(Request $request)
    {
        $validated = $request->validate([
            'spreadsheet' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:5120'],
            'default_password' => ['nullable', 'string', 'min:8'],
        ], [
            'spreadsheet.required' => 'File spreadsheet wajib dipilih.',
            'spreadsheet.mimes' => 'Format file harus .xlsx atau .csv.',
            'default_password.min' => 'Password default minimal 8 karakter.',
        ]);

        $defaultPassword = $validated['default_password'] ?: 'password123';
        $rows = $this->readSpreadsheetRows($request->file('spreadsheet'));

        if (count($rows) < 2) {
            return back()->with('error', 'File import belum memiliki data user. Pastikan baris pertama adalah header kolom.');
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

            $role = $this->normalizeRole($this->firstImportValue($data, ['role', 'jabatan', 'hak_akses']));
            $password = $this->firstImportValue($data, ['password', 'kata_sandi']);

            $payload = [
                'nama' => $this->firstImportValue($data, ['nama', 'nama_user', 'nama_pengguna', 'name']),
                'email' => $this->firstImportValue($data, ['email', 'alamat_email']),
                'password' => $password !== '' ? $password : $defaultPassword,
                'role' => $role,
                'phone' => $this->firstImportValue($data, ['phone', 'no_hp', 'nomor_hp', 'telepon']),
                'nim' => $this->firstImportValue($data, ['nim', 'npm', 'nrp']),
                'jurusan' => $this->firstImportValue($data, ['jurusan', 'prodi', 'program_studi']),
                'penanggung_jawab' => $this->firstImportValue($data, ['penanggung_jawab', 'pj']),
            ];

            $validator = validator($payload, [
                'nama' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:120', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
                'role' => ['required', 'in:' . implode(',', $this->roles)],
                'phone' => ['nullable', 'string', 'max:13'],
                'nim' => ['nullable', 'numeric', 'digits:11'],
                'jurusan' => ['nullable', 'string', 'max:20'],
                'penanggung_jawab' => ['nullable', 'string', 'max:20'],
            ], [
                'role.in' => 'Role harus salah satu dari: ' . implode(', ', $this->roles) . '.',
                'email.unique' => 'Email sudah terdaftar.',
            ]);

            if ($validator->fails()) {
                $skipped[] = 'Baris ' . $rowNumber . ': ' . implode(' ', $validator->errors()->all());
                continue;
            }

            try {
                DB::transaction(function () use ($payload) {
                    $this->enforceRoleLimit($payload['role']);

                    $user = User::create([
                        'nama' => $payload['nama'],
                        'email' => $payload['email'],
                        'password' => Hash::make($payload['password']),
                        'id_role' => Role::idByName($payload['role']),
                        'phone' => $payload['phone'] ?: null,
                    ]);

                    $this->syncProfile($user, $payload);
                });

                $created++;
            } catch (ValidationException $exception) {
                $skipped[] = 'Baris ' . $rowNumber . ': ' . implode(' ', $exception->validator->errors()->all());
            } catch (\Throwable $exception) {
                $skipped[] = 'Baris ' . $rowNumber . ': data gagal diimport.';
            }
        }

        $message = $created . ' user berhasil diimport.';
        if (count($skipped) > 0) {
            $message .= ' ' . count($skipped) . ' baris dilewati.';
        }

        return back()
            ->with($created > 0 ? 'success' : 'error', $created > 0 ? $message : 'Tidak ada user yang berhasil diimport.')
            ->with('import_errors', $skipped);
    }

    public function importTemplate()
    {
        $content = implode("\n", [
            'nama,email,password,role,phone,nim,jurusan,penanggung_jawab',
            'Budi Asisten,budi.asisten@example.com,password123,asisten,081234567890,221001,Teknik Informatika,Lab RPL',
            'Sari Koordinator,sari.koordinator@example.com,password123,koordinator_lab,081234567891,,,',
        ]);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_user_silapor.csv"',
        ]);
    }

    public function create()
    {
        $laboratoriums = \App\Models\Laboratorium::orderBy('nama_laboratorium')->get();
        return view('admin.users.create', [
            'roles' => $this->roles,
            'roleCounts' => $this->roleCounts(),
            'roleLimits' => $this->roleLimits,
            'laboratoriums' => $laboratoriums,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);
        $this->enforceRoleLimit($validated['role']);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'id_role' => Role::idByName($validated['role']),
                'phone' => $validated['phone'] ?? null,
            ]);

            $this->syncProfile($user, $validated);

            if ($validated['role'] === 'koordinator_lab' && $request->filled('id_laboratorium')) {
                $labId = $request->input('id_laboratorium');
                $lab = \App\Models\Laboratorium::find($labId);
                if ($lab) {
                    $oldKoordinatorId = $lab->id_koordinator;

                    $lab->update(['id_koordinator' => $user->id_user]);

                    if ($oldKoordinatorId && $oldKoordinatorId != $user->id_user) {
                        $stillCoordinates = \App\Models\Laboratorium::where('id_koordinator', $oldKoordinatorId)->exists();
                        if (!$stillCoordinates) {
                            $oldKoordinator = User::find($oldKoordinatorId);
                            if ($oldKoordinator) {
                                $oldKoordinator->role = 'asisten';
                                $oldKoordinator->save();
                            }
                        }
                    }
                }
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $user->load(['roleData', 'profile', 'laboratoriumDikoordinatori']);
        $laboratoriums = \App\Models\Laboratorium::orderBy('nama_laboratorium')->get();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $this->roles,
            'roleCounts' => $this->roleCounts($user),
            'roleLimits' => $this->roleLimits,
            'laboratoriums' => $laboratoriums,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, $user);
        $this->enforceRoleLimit($validated['role'], $user);

        DB::transaction(function () use ($user, $validated, $request) {
            $oldRole = $user->role;

            $user->update([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'id_role' => Role::idByName($validated['role']),
                'phone' => $validated['phone'] ?? null,
            ]);

            $this->syncProfile($user, $validated);

            if ($validated['role'] === 'koordinator_lab') {
                $labId = $request->input('id_laboratorium');

                \App\Models\Laboratorium::where('id_koordinator', $user->id_user)
                    ->where('id_laboratorium', '!=', $labId)
                    ->update(['id_koordinator' => null]);

                if ($labId) {
                    $lab = \App\Models\Laboratorium::find($labId);
                    if ($lab) {
                        $oldKoordinatorId = $lab->id_koordinator;

                        $lab->update(['id_koordinator' => $user->id_user]);

                        if ($oldKoordinatorId && $oldKoordinatorId != $user->id_user) {
                            $stillCoordinates = \App\Models\Laboratorium::where('id_koordinator', $oldKoordinatorId)->exists();
                            if (!$stillCoordinates) {
                                $oldKoordinator = User::find($oldKoordinatorId);
                                if ($oldKoordinator) {
                                    $oldKoordinator->role = 'asisten';
                                    $oldKoordinator->save();
                                }
                            }
                        }
                    }
                }
            } else {
                if ($oldRole === 'koordinator_lab') {
                    \App\Models\Laboratorium::where('id_koordinator', $user->id_user)
                        ->update(['id_koordinator' => null]);
                }
            }
        });

        return back()->with('success', 'Profil ' . $user->nama . ' berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password untuk ' . $user->nama . ' berhasil diganti.');
    }

    public function destroy(User $user)
    {
        if ($user->id_user === Auth::id()) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus akun yang sedang login.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
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

        throw ValidationException::withMessages([
            'spreadsheet' => 'Format file belum didukung. Gunakan .xlsx atau .csv.',
        ]);
    }

    protected function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            throw ValidationException::withMessages([
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
            throw ValidationException::withMessages([
                'spreadsheet' => 'Server belum mengaktifkan ekstensi PHP ZipArchive. Gunakan CSV atau aktifkan ext-zip untuk import .xlsx.',
            ]);
        }

        if (!function_exists('simplexml_load_string')) {
            throw ValidationException::withMessages([
                'spreadsheet' => 'Server belum mengaktifkan ekstensi PHP SimpleXML. Gunakan CSV atau aktifkan php-xml untuk import .xlsx.',
            ]);
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages([
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
            throw ValidationException::withMessages([
                'spreadsheet' => 'Worksheet pada file XLSX tidak ditemukan.',
            ]);
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = simplexml_load_string((string) $zip->getFromName($sheetName));
        $zip->close();

        if (!$sheetXml) {
            throw ValidationException::withMessages([
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

    protected function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));
        $role = str_replace([' ', '-'], '_', $role);

        return match ($role) {
            'asisten_lab', 'assistant', 'asisten' => 'asisten',
            'laboran', 'admin_laboran' => 'laboran',
            'koordinator', 'koordinator_lab', 'koordinator_laboratorium' => 'koordinator_lab',
            'kepala', 'kepala_lab', 'kepala_laboratorium' => 'kepala_lab',
            default => $role,
        };
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

    protected function roleCounts(?User $except = null): array
    {
        $counts = [];

        foreach ($this->roleLimits as $role => $limit) {
            $query = User::role($role);

            if ($except) {
                $query->where('id_user', '!=', $except->id_user);
            }

            $counts[$role] = $query->count();
        }

        return $counts;
    }

    protected function enforceRoleLimit(string $role, ?User $except = null): void
    {
        if (!array_key_exists($role, $this->roleLimits)) {
            return;
        }

        $query = User::role($role);

        if ($except) {
            $query->where('id_user', '!=', $except->id_user);
        }

        if ($query->count() >= $this->roleLimits[$role]) {
            throw ValidationException::withMessages([
                'role' => 'Role ' . str_replace('_', ' ', $role) . ' hanya boleh digunakan maksimal ' . $this->roleLimits[$role] . ' akun.',
            ]);
        }
    }

    protected function validateUser(Request $request, ?User $user = null): array
    {
        $emailRule = $user
            ? 'unique:users,email,' . $user->id_user . ',id_user'
            : 'unique:users,email';

        return $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', $emailRule],
            'password' => $user ? ['sometimes', 'nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'role' => ['required', 'in:' . implode(',', $this->roles)],
            'phone' => ['nullable', 'string', 'max:13'],
            'nim' => ['nullable', 'numeric', 'digits:11'],
            'jurusan' => ['nullable', 'string', 'max:20'],
            'id_laboratorium' => ['nullable', 'exists:laboratorium,id_laboratorium'],
        ]);
    }

    protected function syncProfile(User $user, array $validated): void
    {
        if (($validated['role'] ?? $user->role) !== 'asisten') {
            $user->profile?->delete();
            return;
        }

        $profile = [
            'nim' => $validated['nim'] ?? null,
            'jurusan' => $validated['jurusan'] ?? null,
        ];

        if (array_key_exists('penanggung_jawab', $validated)) {
            $profile['penanggung_jawab'] = $validated['penanggung_jawab'];
        } else {
            $profile['penanggung_jawab'] = $user->profile?->penanggung_jawab;
        }

        if (array_filter($profile, fn ($value) => $value !== null && $value !== '')) {
            $user->profile()->updateOrCreate(['id_user' => $user->id_user], $profile);
        } else {
            $user->profile?->delete();
        }
    }
}
