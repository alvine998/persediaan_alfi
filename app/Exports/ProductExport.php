<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */



    public function collection()
    {
        return Product::all('brand_id', 'sku_id', 'nm_barang', 'kategori', 'ukuran', 'berat', 'panjang', 'lebar', 'tinggi', 'tanggal_exp', 'harga_modal', 'harga_jual', 'margin', 'link_photo');
    }

    public function headings(): array
    {
        return ["BRAND", "KODE SKU", "NAMA BARANG", "KATEGORI", "SIZE/UKURAN", "BERAT (KG)", "PANJANG (CM)", "LEBAR (CM)", "TINGGI (CM)", "TANGGAL EXP", "HARGA MODAL", "HARGA JUAL", "MARGIN", "LINK FOTO"];
    }
}
