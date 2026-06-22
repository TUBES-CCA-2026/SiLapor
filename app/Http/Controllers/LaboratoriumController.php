<?php

namespace App\Http\Controllers;

use App\Models\Laboratorium;
use App\Models\User;
use Illuminate\Http\Request;

class LaboratoriumController extends Controller
{
    public function index()
    {
        $laboratoriums = Laboratorium::with('koordinator')
            ->orderBy('nama_laboratorium')
            ->get();

        // Hanya user dengan role koordinator_lab yang relevan dipilih jadi penanggung jawab
        $koordinators = User::where('role', 'koordinator_lab')->orderBy('nama')->get();

        return view('laboratorium.index', compact('laboratoriums', 'koordinators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_laboratorium' => ['required', 'string', 'max:120'],
            'kode_laboratorium' => ['nullable', 'string', 'max:20'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'id_koordinator' => ['nullable', 'exists:users,id_user'],
            'kapasitas' => ['nullable', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        Laboratorium::create($validated);

        return back()->with('success', 'Laboratorium baru berhasil ditambahkan.');
    }

    public function update(Request $request, Laboratorium $laboratorium)
    {
        $validated = $request->validate([
            'nama_laboratorium' => ['required', 'string', 'max:120'],
            'kode_laboratorium' => ['nullable', 'string', 'max:20'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'id_koordinator' => ['nullable', 'exists:users,id_user'],
            'kapasitas' => ['nullable', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $laboratorium->update($validated);

        return back()->with('success', 'Data laboratorium berhasil diperbarui.');
    }
}
