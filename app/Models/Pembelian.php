<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';
    protected $primaryKey = 'id_pembelian';
    protected $guarded = ['id_pembelian'];

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok');
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class, 'id_pembelian');
    }

    public function getNoFakturAttribute()
    {
        return $this->attributes['no_faktur_pembelian'];
    }
}
