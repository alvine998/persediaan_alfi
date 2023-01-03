<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewProductExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::all('brand_id', 'sku_id', 'nm_barang', 'kategori', 'ukuran', 'lokasi', 'berat', 'panjang', 'lebar', 'tinggi', 'harga_modal', 'harga_jual', 'margin', 'link_photo');
    }

    public function headings(): array
    {
        return ["BRAND", "KODE SKU", "NAMA BARANG", "KATEGORI", "SIZE/UKURAN","LOKASI", "BERAT (KG)", "PANJANG (CM)", "LEBAR (CM)", "TINGGI (CM)", "HARGA MODAL", "HARGA JUAL", "MARGIN", "LINK FOTO"];
    }
}
