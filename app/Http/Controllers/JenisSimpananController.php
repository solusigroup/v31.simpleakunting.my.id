<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JenisSimpanan;
use Illuminate\Http\Request;

class JenisSimpananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisSimpanan = JenisSimpanan::all();
        return view('jenis-simpanan.index', compact('jenisSimpanan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Simpanan bisa masuk ke Kewajiban (2-) atau Ekuitas (3-) untuk Simpanan Pokok
        $akunSimpanan = Akun::where(function($query) {
            $query->where('kode_akun', 'LIKE', '2-%')
                  ->orWhere('kode_akun', 'LIKE', '3-%');
        })->orderBy('kode_akun')->get();
        // Akun Biaya (5-)
        $akunBiaya = Akun::where('kode_akun', 'LIKE', '5-%')->orderBy('kode_akun')->get();
        return view('jenis-simpanan.create', compact('akunSimpanan', 'akunBiaya'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_simpanan' => 'required|string|max:20|unique:jenis_simpanan',
            'nama_simpanan' => 'required|string|max:100',
            'tipe' => 'required|string|in:pokok,wajib,sukarela,deposito',
            'bunga_pertahun' => 'nullable|numeric|min:0',
            'akun_simpanan' => 'required|exists:akun,kode_akun',
            'akun_bunga' => 'nullable|exists:akun,kode_akun',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        JenisSimpanan::create($validated);

        return redirect()->route('jenis-simpanan.index')
            ->with('success', 'Jenis Simpanan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jenisSimpanan = JenisSimpanan::findOrFail($id);
        // Simpanan bisa masuk ke Kewajiban (2-) atau Ekuitas (3-) untuk Simpanan Pokok
        $akunSimpanan = Akun::where(function($query) {
            $query->where('kode_akun', 'LIKE', '2-%')
                  ->orWhere('kode_akun', 'LIKE', '3-%');
        })->orderBy('kode_akun')->get();
        // Akun Biaya (5-)
        $akunBiaya = Akun::where('kode_akun', 'LIKE', '5-%')->orderBy('kode_akun')->get();
        return view('jenis-simpanan.edit', compact('jenisSimpanan', 'akunSimpanan', 'akunBiaya'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jenisSimpanan = JenisSimpanan::findOrFail($id);

        $validated = $request->validate([
            'kode_simpanan' => 'required|string|max:20|unique:jenis_simpanan,kode_simpanan,' . $id . ',id_jenis_simpanan',
            'nama_simpanan' => 'required|string|max:100',
            'tipe' => 'required|string|in:pokok,wajib,sukarela,deposito',
            'bunga_pertahun' => 'nullable|numeric|min:0',
            'akun_simpanan' => 'required|exists:akun,kode_akun',
            'akun_bunga' => 'nullable|exists:akun,kode_akun',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $jenisSimpanan->update($validated);

        return redirect()->route('jenis-simpanan.index')
            ->with('success', 'Jenis Simpanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jenisSimpanan = JenisSimpanan::findOrFail($id);
        
        // Check if any simpanan using this type
        if ($jenisSimpanan->simpanan()->exists()) {
            return redirect()->route('jenis-simpanan.index')
                ->with('error', 'Jenis Simpanan tidak dapat dihapus karena masih digunakan.');
        }

        $jenisSimpanan->delete();

        return redirect()->route('jenis-simpanan.index')
            ->with('success', 'Jenis Simpanan berhasil dihapus.');
    }
}
