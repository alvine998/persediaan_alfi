<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductImportHistory implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Product([
            'brand_id'          => $row['brand'],
            'sku_id'            => $row['kode_sku'],
            'nm_barang'         => $row['nama_barang'],
            'kategori'          => $row['kategori'],
            'ukuran'            => $row['size_ukuran'],
            'lokasi'            => $row['lokasi'],
            'berat'             => $row['berat_kg'],
            'panjang'           => $row['panjang_cm'],
            'lebar'             => $row['lebar_cm'],
            'tinggi'            => $row['tinggi_cm'],
            'harga_modal'       => $row['harga_modal'],
            'harga_jual'        => $row['harga_jual'],
            'margin'            => $row['margin'],
            'link_photo'        => $row['link_foto'],
        ]);
    }
}
