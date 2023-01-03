<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';
    protected $fillable = ['tanggal_transaksi', 'penginput', 'no_transaksi', 'sku_id', 'qty', 'tanggal_exp', 'is_expired'];
    public $timestamps = false;
}
