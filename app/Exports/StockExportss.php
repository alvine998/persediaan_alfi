<?php

namespace App\Exports;

use App\Models\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExportss implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        return Export::all('tanggal', 'penginput', 'no_transaksi', 'nm_pembeli', 'alamat', 'kota_kab', 'provinsi', 'no_hp', 'sku_id', 'nm_barang', 'kategori', 'size', 'qty', 'toko');
    }

    public function headings(): array
    {
        return ["TANGGAL", "PENGINPUT", "NO TRANSAKSI", "NAMA PEMBELI", "ALAMAT", "KOTA / KABUPATEN", "PROVINSI", "NO HP", "KODE SKU", "NAMA BARANG", "KATEGORI", "SIZE", "QTY", "TOKO"];
    }
}
