<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JenisPinjaman;
use Illuminate\Http\Request;

class JenisPinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisPinjaman = JenisPinjaman::all();
        return view('jenis-pinjaman.index', compact('jenisPinjaman'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Akun Piutang (Aset = kode dimulai dengan 1)
        $akunPiutang = Akun::where('kode_akun', 'LIKE', '1-%')->orderBy('kode_akun')->get();
        // Akun Pendapatan (kode dimulai dengan 4)
        $akunPendapatan = Akun::where('kode_akun', 'LIKE', '4-%')->orderBy('kode_akun')->get();
        return view('jenis-pinjaman.create', compact('akunPiutang', 'akunPendapatan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pinjaman' => 'required|string|max:20|unique:jenis_pinjaman',
            'nama_pinjaman' => 'required|string|max:100',
            'kategori' => 'required|string|in:produktif,konsumtif,darurat',
            'bunga_pertahun' => 'required|numeric|min:0',
            'metode_bunga' => 'required|string|in:flat,anuitas,efektif',
            'tenor_max' => 'required|integer|min:1',
            'plafon_max' => 'required|numeric|min:0',
            'provisi_persen' => 'nullable|numeric|min:0',
            'admin_fee' => 'nullable|numeric|min:0',
            'akun_piutang_pinjaman' => 'required|exists:akun,kode_akun',
            'akun_pendapatan_bunga' => 'required|exists:akun,kode_akun',
            'akun_pendapatan_provisi' => 'nullable|exists:akun,kode_akun',
            'akun_pendapatan_admin' => 'nullable|exists:akun,kode_akun',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        JenisPinjaman::create($validated);

        return redirect()->route('jenis-pinjaman.index')
            ->with('success', 'Jenis Pinjaman berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jenisPinjaman = JenisPinjaman::findOrFail($id);
        // Akun Piutang (Aset = kode dimulai dengan 1)
        $akunPiutang = Akun::where('kode_akun', 'LIKE', '1-%')->orderBy('kode_akun')->get();
        // Akun Pendapatan (kode dimulai dengan 4)
        $akunPendapatan = Akun::where('kode_akun', 'LIKE', '4-%')->orderBy('kode_akun')->get();
        return view('jenis-pinjaman.edit', compact('jenisPinjaman', 'akunPiutang', 'akunPendapatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jenisPinjaman = JenisPinjaman::findOrFail($id);

        $validated = $request->validate([
            'kode_pinjaman' => 'required|string|max:20|unique:jenis_pinjaman,kode_pinjaman,' . $id . ',id_jenis_pinjaman',
            'nama_pinjaman' => 'required|string|max:100',
            'kategori' => 'required|string|in:produktif,konsumtif,darurat',
            'bunga_pertahun' => 'required|numeric|min:0',
            'metode_bunga' => 'required|string|in:flat,anuitas,efektif',
            'tenor_max' => 'required|integer|min:1',
            'plafon_max' => 'required|numeric|min:0',
            'provisi_persen' => 'nullable|numeric|min:0',
            'admin_fee' => 'nullable|numeric|min:0',
            'akun_piutang_pinjaman' => 'required|exists:akun,kode_akun',
            'akun_pendapatan_bunga' => 'required|exists:akun,kode_akun',
            'akun_pendapatan_provisi' => 'nullable|exists:akun,kode_akun',
            'akun_pendapatan_admin' => 'nullable|exists:akun,kode_akun',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $jenisPinjaman->update($validated);

        return redirect()->route('jenis-pinjaman.index')
            ->with('success', 'Jenis Pinjaman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jenisPinjaman = JenisPinjaman::findOrFail($id);
        
        // Check if any pinjaman using this type
        if ($jenisPinjaman->pinjaman()->exists()) {
            return redirect()->route('jenis-pinjaman.index')
                ->with('error', 'Jenis Pinjaman tidak dapat dihapus karena masih digunakan.');
        }

        $jenisPinjaman->delete();

        return redirect()->route('jenis-pinjaman.index')
            ->with('success', 'Jenis Pinjaman berhasil dihapus.');
    }
}
