<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerusahaanController extends Controller
{
    public function edit()
    {
        // Ambil data perusahaan (asumsi hanya ada 1 record dengan ID 1)
        $perusahaan = DB::table('perusahaan')->find(1);
        
        if (!$perusahaan) {
            // Jika belum ada, buat dummy object atau redirect untuk create (tapi di sini kita asumsi edit)
            // Sebaiknya di seeder sudah ada. Kita handle view agar tidak error jika null.
        }

        return view('perusahaan.edit', compact('perusahaan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nama_direktur' => 'nullable|string|max:255',
            'nama_akuntan' => 'nullable|string|max:255',
        ]);

        DB::table('perusahaan')->updateOrInsert(
            ['id' => 1],
            [
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'nama_direktur' => $request->nama_direktur,
                'nama_akuntan' => $request->nama_akuntan,
                'updated_at' => now(),
            ]
        );

        return redirect()->route('perusahaan.edit')->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}
