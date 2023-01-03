<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHistoryTambah extends Model
{
    use HasFactory;

    protected $table = 'product_history_tambah';
    protected $fillable = ['history_id', 'reset', 'tanggal_transaksi', 'no_transaksi', 'penginput', 'sku_id', 'qty', 'tanggal_exp', 'status', 'message'];
    public $timestamps = false;
    protected $primaryKey = 'product_history_tambah_id';
}
