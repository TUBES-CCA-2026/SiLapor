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
        $laboratoriums = Laboratorium::with(['koordinator'])
            ->withCount(['fasilitas' => function ($query) {
                $query->activeQr();
            }])
            ->orderBy('nama_laboratorium')
            ->get();

        // Daftar asisten & koordinator untuk pilihan Koordinator Lab
        $asistenList = User::whereHas('roleData', function ($q) {
            $q->whereIn('nama_role', ['asisten', 'koordinator_lab']);
        })->orderBy('nama')->get();

        return view('laboratorium.index', compact('laboratoriums', 'asistenList'));
    }

    /**
     * Laboran menambahkan lab baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_laboratorium' => ['required', 'string', 'max:120'],
            'kode_laboratorium' => ['nullable', 'string', 'max:20'],
            'id_koordinator' => ['nullable', 'exists:users,id_user'],
        ]);

        $lab = Laboratorium::create($validated);

        if ($lab->id_koordinator) {
            $newKoordinator = User::find($lab->id_koordinator);
            if ($newKoordinator) {
                $newKoordinator->role = 'koordinator_lab';
                $newKoordinator->save();
            }
        }

        return back()->with('success', 'Laboratorium baru berhasil ditambahkan.');
    }

    /**
     * Laboran/admin memperbarui data laboratorium dan menunjuk koordinator.
     */
    public function update(Request $request, Laboratorium $laboratorium)
    {
        $validated = $request->validate([
            'nama_laboratorium' => ['required', 'string', 'max:120'],
            'kode_laboratorium' => ['nullable', 'string', 'max:20'],
            'id_koordinator' => ['nullable', 'exists:users,id_user'],
        ]);

        $oldKoordinatorId = $laboratorium->id_koordinator;

        $laboratorium->update($validated);

        $newKoordinatorId = $laboratorium->id_koordinator;

        // Jika koordinator berubah, sinkronisasi role
        if ($oldKoordinatorId != $newKoordinatorId) {
            if ($newKoordinatorId) {
                $newKoordinator = User::find($newKoordinatorId);
                if ($newKoordinator) {
                    $newKoordinator->role = 'koordinator_lab';
                    $newKoordinator->save();
                }
            }

            if ($oldKoordinatorId) {
                $stillCoordinates = Laboratorium::where('id_koordinator', $oldKoordinatorId)->exists();
                if (!$stillCoordinates) {
                    $oldKoordinator = User::find($oldKoordinatorId);
                    if ($oldKoordinator) {
                        $oldKoordinator->role = 'asisten';
                        $oldKoordinator->save();
                    }
                }
            }
        }

        return back()->with('success', 'Data laboratorium berhasil diperbarui.');
    }

    public function destroy(Laboratorium $laboratorium): RedirectResponse
    {
        $oldKoordinatorId = $laboratorium->id_koordinator;

        // Unlink related facilities (set id_laboratorium to null)
        $laboratorium->semuaFasilitas()->update(['id_laboratorium' => null]);

        $nama = $laboratorium->nama_laboratorium;
        $laboratorium->delete();

        if ($oldKoordinatorId) {
            $stillCoordinates = Laboratorium::where('id_koordinator', $oldKoordinatorId)->exists();
            if (!$stillCoordinates) {
                $oldKoordinator = User::find($oldKoordinatorId);
                if ($oldKoordinator) {
                    $oldKoordinator->role = 'asisten';
                    $oldKoordinator->save();
                }
            }
        }

        return back()->with('success', "Laboratorium {$nama} berhasil dihapus.");
    }
}
