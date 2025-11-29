<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function index()
    {
        $akun = Akun::orderBy('kode_akun')->get();
        return view('akun.index', compact('akun'));
    }

    public function create()
    {
        return view('akun.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_akun' => 'required|unique:akun,kode_akun|max:20',
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string',
            'saldo_normal' => 'required|in:Debit,Kredit',
        ]);

        Akun::create($request->all());

        return redirect()->route('akun.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(Akun $akun)
    {
        return view('akun.edit', compact('akun'));
    }

    public function update(Request $request, Akun $akun)
    {
        $request->validate([
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string',
            'saldo_normal' => 'required|in:Debit,Kredit',
        ]);

        $akun->update($request->all());

        return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Akun $akun)
    {
        try {
            $akun->delete();
            return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus akun. Pastikan akun tidak digunakan dalam transaksi.');
        }
    }
}
