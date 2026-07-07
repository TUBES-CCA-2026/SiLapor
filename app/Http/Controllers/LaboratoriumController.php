<?php

namespace App\Http\Controllers;

use App\Models\Laboratorium;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaboratoriumController extends Controller
{
    public function index()
    {
        $laboratoriums = Laboratorium::with(['koordinator', 'penanggungJawabUser', 'pendampingUser'])
            ->withCount(['fasilitas' => function ($query) {
                $query->activeQr();
            }])
            ->orderBy('nama_laboratorium')
            ->get();

        // Daftar asisten untuk pilihan PJ & Pendamping (koordinator)
        $asistenList = User::role('asisten')->orderBy('nama')->get();

        return view('laboratorium.index', compact('laboratoriums', 'asistenList'));
    }

    /**
     * Laboran menambahkan lab baru (tanpa menentukan PJ/Pendamping).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_laboratorium' => ['required', 'string', 'max:120'],
            'kode_laboratorium' => ['nullable', 'string', 'max:20'],
        ]);

        Laboratorium::create($validated);

        return back()->with('success', 'Laboratorium baru berhasil ditambahkan.');
    }

    /**
     * Koordinator menentukan PJ & Pendamping lab.
     */
    public function update(Request $request, Laboratorium $laboratorium)
    {
        $user = Auth::user();

        if ($user && $user->role === 'koordinator_lab') {
            // Koordinator hanya bisa set PJ & Pendamping (array)
            $validated = $request->validate([
                'id_penanggung_jawab' => ['nullable', 'exists:users,id_user'],
                'id_pendamping' => ['nullable', 'array'],
                'id_pendamping.*' => ['exists:users,id_user'],
            ]);

            $laboratorium->update($validated);

            return back()->with('success', 'Penanggung jawab & pendamping berhasil diperbarui.');
        }

        // Laboran/admin bisa edit detail lab
        $validated = $request->validate([
            'nama_laboratorium' => ['required', 'string', 'max:120'],
            'kode_laboratorium' => ['nullable', 'string', 'max:20'],
        ]);

        $laboratorium->update($validated);

        return back()->with('success', 'Data laboratorium berhasil diperbarui.');
    }

    public function destroy(Laboratorium $laboratorium): RedirectResponse
    {
        // Unlink related facilities (set id_laboratorium to null)
        $laboratorium->semuaFasilitas()->update(['id_laboratorium' => null]);

        $nama = $laboratorium->nama_laboratorium;
        $laboratorium->delete();

        return back()->with('success', "Laboratorium {$nama} berhasil dihapus.");
    }
}
