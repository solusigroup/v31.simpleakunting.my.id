<?php

namespace App\Http\Controllers;

use App\Models\Persediaan;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersediaanController extends Controller
{
    public function index()
    {
        $persediaan = Persediaan::all();
        return view('persediaan.index', compact('persediaan'));
    }

    public function show($id)
    {
        // Redirect to edit page since there's no separate show view
        return redirect()->route('persediaan.edit', $id);
    }

    public function create()
    {
        // Ambil semua akun untuk dropdown
        $akun = Akun::all();
        // Ambil jenis usaha untuk conditional display
        $jenisUsaha = DB::table('perusahaan')->where('id', 1)->value('jenis_usaha') ?? 'dagang';
        return view('persediaan.create', compact('akun', 'jenisUsaha'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_barang' => 'required|string|unique:master_persediaan,kode_barang|max:255',
            'barcode' => 'nullable|string|unique:master_persediaan,barcode|max:255',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok_awal' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'akun_persediaan' => 'nullable|string|exists:akun,kode_akun',
            'akun_hpp' => 'nullable|string|exists:akun,kode_akun',
            'akun_penjualan' => 'nullable|string|exists:akun,kode_akun',
        ]);

        $validatedData['stok_saat_ini'] = $validatedData['stok_awal'];

        DB::transaction(function () use ($validatedData) {
            $barang = Persediaan::create($validatedData);

            // Jika ada stok awal, catat di kartu stok
            if ($validatedData['stok_awal'] > 0) {
                DB::table('kartu_stok')->insert([
                    'id_barang' => $barang->id_barang,
                    'tipe_transaksi' => 'IN',
                    'kuantitas' => $validatedData['stok_awal'],
                    'keterangan' => 'Stok Awal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('persediaan.index')->with('success', 'Data barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $persediaan = Persediaan::findOrFail($id);
        $akun = Akun::all();
        // Ambil jenis usaha untuk conditional display
        $jenisUsaha = DB::table('perusahaan')->where('id', 1)->value('jenis_usaha') ?? 'dagang';
        return view('persediaan.edit', compact('persediaan', 'akun', 'jenisUsaha'));
    }

    public function update(Request $request, $id)
    {
        $persediaan = Persediaan::findOrFail($id);

        $validatedData = $request->validate([
            'kode_barang' => 'required|string|max:255|unique:master_persediaan,kode_barang,' . $id . ',id_barang',
            'barcode' => 'nullable|string|max:255|unique:master_persediaan,barcode,' . $id . ',id_barang',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok_awal' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'akun_persediaan' => 'nullable|string|exists:akun,kode_akun',
            'akun_hpp' => 'nullable|string|exists:akun,kode_akun',
            'akun_penjualan' => 'nullable|string|exists:akun,kode_akun',
        ]);

        DB::transaction(function () use ($validatedData, $persediaan) {
            $stokAwalLama = $persediaan->stok_awal;
            $stokAwalBaru = $validatedData['stok_awal'];
            $selisih = $stokAwalBaru - $stokAwalLama;

            $validatedData['stok_saat_ini'] = $persediaan->stok_saat_ini + $selisih;

            $persediaan->update($validatedData);

            if ($selisih != 0) {
                DB::table('kartu_stok')->insert([
                    'id_barang' => $persediaan->id_barang,
                    'tipe_transaksi' => ($selisih > 0) ? 'IN' : 'OUT',
                    'kuantitas' => abs($selisih),
                    'keterangan' => 'Penyesuaian Stok Awal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('persediaan.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $persediaan = Persediaan::findOrFail($id);
        $persediaan->delete();
        return redirect()->route('persediaan.index')->with('success', 'Data barang berhasil dihapus.');
    }
}
