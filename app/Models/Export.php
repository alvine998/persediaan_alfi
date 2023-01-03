<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    use HasFactory;

    protected $table = 'export';
    protected $fillable = ['tanggal', 'penginput', 'no_transaksi', 'nm_pembeli', 'alamat', 'kota_kab', 'provinsi', 'no_hp', 'sku_id', 'nm_barang', 'kategori', 'size', 'qty', 'toko', 'keterangan'];
    public $timestamps = false;
}
