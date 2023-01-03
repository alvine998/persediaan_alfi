<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        return Stock::all('tanggal_transaksi', 'no_transaksi', 'penginput', 'sku_id', 'qty', 'tanggal_exp');
    }

    public function headings(): array
    {
        return ["TANGGAL TRANSAKSI", "NO TRANSAKSI", "PENGINPUT", "KODE SKU", "QTY", "TANGGAL EXP"];
    }
}
