<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persediaan extends Model
{
    use HasFactory;

    protected $table = 'master_persediaan';
    protected $primaryKey = 'id_barang';
    protected $guarded = ['id_barang'];
}
