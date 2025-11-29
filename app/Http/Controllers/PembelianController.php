<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Pemasok;
use App\Models\Persediaan;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelian = Pembelian::with('pemasok')->orderBy('tanggal_faktur', 'desc')->get();
        return view('pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        $pemasok = Pemasok::all();
        $barang = Persediaan::all();
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->get();
        
        // Generate No Faktur Otomatis (Simple)
        $lastFaktur = Pembelian::orderBy('id_pembelian', 'desc')->first();
        // Use no_faktur_pembelian column
        $nextNo = $lastFaktur ? (int)substr($lastFaktur->no_faktur_pembelian, 4) + 1 : 1;
        $noFaktur = 'PUR-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        return view('pembelian.create', compact('pemasok', 'barang', 'akunKas', 'noFaktur'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pemasok' => 'required|exists:pemasok,id_pemasok',
            'no_faktur' => 'required|unique:pembelian,no_faktur_pembelian',
            'tanggal_faktur' => 'required|date',
            'metode_pembayaran' => 'required|in:Tunai,Kredit',
            'akun_kas_bank' => 'required_if:metode_pembayaran,Tunai',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalPembelian = 0;
            $detailsData = [];
            
            foreach ($request->details as $item) {
                $barang = Persediaan::findOrFail($item['id_barang']);
                $subtotal = $item['harga_beli'] * $item['kuantitas'];
                $totalPembelian += $subtotal;

                $detailsData[] = [
                    'barang' => $barang,
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ];
            }

            // 2. Buat Jurnal
            $akunUtang = '2-10100'; // Contoh kode akun Utang Usaha
            $akunPersediaanDefault = '1-10300'; // Contoh kode akun Persediaan

            $jurnal = Jurnal::create([
                'no_transaksi' => $request->no_faktur,
                'tanggal' => $request->tanggal_faktur,
                'deskripsi' => "Pembelian Faktur #{$request->no_faktur}",
                'sumber_jurnal' => 'Pembelian',
                'is_locked' => 1
            ]);

            // Debit: Persediaan (Per Barang)
            foreach ($detailsData as $data) {
                $akunDebit = $data['barang']->akun_persediaan ?? $akunPersediaanDefault;
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $akunDebit,
                    'debit' => $data['subtotal'],
                    'kredit' => 0
                ]);
            }

            // Kredit: Kas (Tunai) atau Utang (Kredit)
            $akunKredit = ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : $akunUtang;
            
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $akunKredit,
                'debit' => 0,
                'kredit' => $totalPembelian
            ]);

            // 3. Simpan Pembelian
            $pembelian = Pembelian::create([
                'id_pemasok' => $request->id_pemasok,
                'id_jurnal' => $jurnal->id_jurnal,
                'no_faktur_pembelian' => $request->no_faktur, // Map to correct column
                'tanggal_faktur' => $request->tanggal_faktur,
                'total' => $totalPembelian,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'akun_kas_bank' => ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : null,
                'sisa_tagihan' => ($request->metode_pembayaran == 'Kredit') ? $totalPembelian : 0,
                'status_pembayaran' => ($request->metode_pembayaran == 'Kredit') ? 'Belum Lunas' : 'Lunas',
            ]);

            // 4. Simpan Detail & Update Stok
            foreach ($detailsData as $data) {
                PembelianDetail::create([
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $data['barang']->id_barang,
                    'kuantitas' => $data['kuantitas'],
                    'harga' => $data['harga'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Tambah Stok & Update Harga Beli Terakhir
                $data['barang']->increment('stok_saat_ini', $data['kuantitas']);
                $data['barang']->update(['harga_beli' => $data['harga']]);

                // Catat Kartu Stok
                DB::table('kartu_stok')->insert([
                    'id_barang' => $data['barang']->id_barang,
                    'tipe_transaksi' => 'IN',
                    'kuantitas' => $data['kuantitas'],
                    'keterangan' => "Pembelian #{$request->no_faktur}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 5. Update Saldo Pemasok (Jika Kredit)
            if ($request->metode_pembayaran == 'Kredit') {
                Pemasok::where('id_pemasok', $request->id_pemasok)
                    ->increment('saldo_terkini_hutang', $totalPembelian);
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['details.barang', 'pemasok']);
        return view('pembelian.show', compact('pembelian'));
    }

    public function destroy(Pembelian $pembelian)
    {
        return back()->with('error', 'Fitur hapus pembelian belum diimplementasikan demi keamanan data akuntansi.');
    }
}
