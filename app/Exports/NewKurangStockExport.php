<?php

namespace App\Exports;

use App\Models\ProductHistoryKurang;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewKurangStockExport implements FromQuery, WithHeadings
{
    use Exportable;

    protected $id;

    function __construct($id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return ProductHistoryKurang::query()->select('tanggal', 'penginput', 'no_transaksi', 'nm_pembeli', 'alamat', 'kota_kab', 'provinsi', 'no_hp', 'sku_id', 'nm_barang', 'kategori', 'size', 'qty', 'toko', 'status', 'message')->where('history_id', '=', $this->id);
        // return ProductHistoryInput::query("SELECT * FROM product_history_input");
        // return ProductHistory::query("SELECT * FROM product_history INNER JOIN history ON product_history.history_id = history.history_id INNER JOIN product ON product_history.sku_id = product.sku_id WHERE product_history.sku_id='{$this->id}'");
    }

    public function headings(): array
    {
        return ["TANGGAL", "PENGINPUT", "NO TRANSAKSI", "NAMA PEMBELI", "ALAMAT", "KOTA / KABUPATEN", "PROVINSI", "NO HP", "KODE SKU", "NAMA BARANG", "KATEGORI", "SIZE", "QTY", "TOKO", "STATUS", "PESAN"];
    }
}
