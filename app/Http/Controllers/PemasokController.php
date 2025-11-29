<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    public function index()
    {
        $pemasok = Pemasok::all();
        return view('pemasok.index', compact('pemasok'));
    }

    public function create()
    {
        return view('pemasok.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pemasok' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'saldo_awal_hutang' => 'required|numeric|min:0',
        ]);

        $validatedData['saldo_terkini_hutang'] = $validatedData['saldo_awal_hutang'];

        Pemasok::create($validatedData);

        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil ditambahkan.');
    }

    public function edit(Pemasok $pemasok)
    {
        return view('pemasok.edit', compact('pemasok'));
    }

    public function update(Request $request, Pemasok $pemasok)
    {
        $validatedData = $request->validate([
            'nama_pemasok' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'saldo_awal_hutang' => 'required|numeric|min:0',
        ]);

        $selisih = $validatedData['saldo_awal_hutang'] - $pemasok->saldo_awal_hutang;
        $validatedData['saldo_terkini_hutang'] = $pemasok->saldo_terkini_hutang + $selisih;

        $pemasok->update($validatedData);

        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil diperbarui.');
    }

    public function destroy(Pemasok $pemasok)
    {
        $pemasok->delete();
        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil dihapus.');
    }
}
