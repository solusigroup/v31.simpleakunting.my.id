<?php

namespace App\Traits;

use App\Models\JurnalDetail;
use App\Models\Akun;

trait CheckSaldoTrait
{
    /**
     * Check if an account has sufficient balance for a credit transaction (Pengeluaran).
     * Only applies to 'Kas & Bank' accounts.
     * 
     * @param string $kodeAkun
     * @param float $amountPengeluaran
     * @return bool
     */
    public function checkSaldoCukup($kodeAkun, $amountPengeluaran)
    {
        $akun = Akun::where('kode_akun', $kodeAkun)->first();
        
        // Only check for Kas & Bank
        if (!$akun || $akun->tipe_akun != 'Kas & Bank') {
            return true;
        }

        $debit = JurnalDetail::where('kode_akun', $kodeAkun)->sum('debit');
        $kredit = JurnalDetail::where('kode_akun', $kodeAkun)->sum('kredit');
        $saldoSaatIni = $debit - $kredit;

        if (($saldoSaatIni - $amountPengeluaran) < 0) {
            return false;
        }

        return true;
    }
    
    public function getSaldoSaatIni($kodeAkun)
    {
        $debit = JurnalDetail::where('kode_akun', $kodeAkun)->sum('debit');
        $kredit = JurnalDetail::where('kode_akun', $kodeAkun)->sum('kredit');
        return $debit - $kredit;
    }
}
