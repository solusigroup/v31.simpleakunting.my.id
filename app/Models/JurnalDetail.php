<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    use HasFactory;

    protected $table = 'jurnal_detail';
    protected $primaryKey = 'id_detail';
    protected $guarded = ['id_detail'];

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'kode_akun');
    }
}
