<?php

namespace App\Exports;

use App\Models\ProductHistory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExportHistory implements FromQuery, WithHeadings
{
    use Exportable;

    protected $id;

    function __construct($id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return ProductHistory::query()->join('history', 'product_history.history_id', '=', 'history.history_id')->join('product', 'product_history.sku_id', '=', 'product.sku_id')->select('brand_id', 'product.sku_id', 'nm_barang', 'kategori', 'ukuran', 'lokasi', 'berat', 'panjang', 'lebar', 'tinggi', 'harga_modal', 'harga_jual', 'margin', 'link_photo')->where('product_history.history_id', '=', $this->id);
        // return ProductHistory::query("SELECT * FROM product_history INNER JOIN history ON product_history.history_id = history.history_id INNER JOIN product ON product_history.sku_id = product.sku_id WHERE product_history.sku_id='{$this->id}'");
    }

    public function headings(): array
    {
        return ["BRAND", "KODE SKU", "NAMA BARANG", "KATEGORI", "UKURAN", "LOKASI", "BERAT", "PANJANG", "LEBAR", "TINGGI", "HARGA MODAL", "HARGA JUAL", "MARGIN", "LINK FOTO"];
    }
}
