<?php

namespace App\Http\Controllers;

use App\Models\Laboratorium;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LaboratoriumController extends Controller
{
    public function index()
    {
        $laboratoriums = Laboratorium::with(['koordinator', 'penanggungJawabs'])
            ->withCount('fasilitas')
            ->orderBy('nama_laboratorium')
            ->get();

        // Penanggung jawab laboratorium dipilih dari role asisten.
        $penanggungJawabs = User::role('asisten')->orderBy('nama')->get();
        $koordinators = $penanggungJawabs;

        return view('laboratorium.index', compact('laboratoriums', 'penanggungJawabs', 'koordinators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_laboratorium' => ['required', 'string', 'max:120'],
            'kode_laboratorium' => ['nullable', 'string', 'max:20'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'id_koordinator' => ['nullable', 'exists:users,id_user'],
            'id_penanggung_jawab' => ['nullable', 'array'],
            'id_penanggung_jawab.*' => ['exists:users,id_user'],
        ]);

        $pjIds = $validated['id_penanggung_jawab'] ?? [];
        unset($validated['id_penanggung_jawab']);

        if (empty($validated['id_koordinator']) && !empty($pjIds)) {
            $validated['id_koordinator'] = $pjIds[0];
        }

        $laboratorium = Laboratorium::create($validated);
        $laboratorium->penanggungJawabs()->sync($pjIds);

        return back()->with('success', 'Laboratorium baru berhasil ditambahkan.');
    }

    public function update(Request $request, Laboratorium $laboratorium)
    {
        $validated = $request->validate([
            'nama_laboratorium' => ['required', 'string', 'max:120'],
            'kode_laboratorium' => ['nullable', 'string', 'max:20'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'id_koordinator' => ['nullable', 'exists:users,id_user'],
            'id_penanggung_jawab' => ['nullable', 'array'],
            'id_penanggung_jawab.*' => ['exists:users,id_user'],
        ]);

        $pjIds = $validated['id_penanggung_jawab'] ?? [];
        unset($validated['id_penanggung_jawab']);

        if (empty($validated['id_koordinator']) && !empty($pjIds)) {
            $validated['id_koordinator'] = $pjIds[0];
        }

        $laboratorium->update($validated);
        $laboratorium->penanggungJawabs()->sync($pjIds);

        return back()->with('success', 'Data laboratorium berhasil diperbarui.');
    }

    public function destroy(Laboratorium $laboratorium): RedirectResponse
    {
        if ($laboratorium->fasilitas()->exists()) {
            return back()->withErrors([
                'laboratorium' => 'Laboratorium tidak dapat dihapus karena masih memiliki fasilitas.',
            ]);
        }

        $nama = $laboratorium->nama_laboratorium;
        $laboratorium->delete();

        return back()->with('success', "Laboratorium {$nama} berhasil dihapus.");
    }
}
