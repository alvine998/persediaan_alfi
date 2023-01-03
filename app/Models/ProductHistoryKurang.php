<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHistoryKurang extends Model
{
    use HasFactory;

    protected $table = 'product_history_kurang';
    protected $fillable = ['history_id', 'reset', 'tanggal', 'penginput', 'no_transaksi', 'nm_pembeli', 'alamat', 'kota_kab', 'provinsi', 'no_hp', 'sku_id', 'nm_barang', 'kategori', 'size', 'qty', 'toko', 'status', 'message'];
    public $timestamps = false;
    protected $primaryKey = 'product_history_kurang_id';
}
