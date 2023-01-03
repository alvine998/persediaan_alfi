<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHistoryInput extends Model
{
    use HasFactory;

    protected $table = 'product_history_input';
    protected $fillable = ['history_id', 'reset', 'brand_id', 'sku_id', 'nm_barang', 'kategori', 'ukuran', 'berat', 'panjang', 'lebar', 'tinggi', 'lokasi', 'harga_modal', 'harga_jual', 'margin', 'link_photo', 'status', 'message'];
    public $timestamps = false;
    protected $primaryKey = 'product_history_input_id';
}
