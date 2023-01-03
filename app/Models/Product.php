<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    protected $fillable = ['brand_id', 'sku_id', 'nm_barang', 'kategori', 'ukuran', 'berat', 'panjang', 'lebar', 'tinggi', 'lokasi', 'harga_modal', 'harga_jual', 'margin', 'link_photo'];
    public $timestamps = false;
    protected $primaryKey = 'product_id';
}
