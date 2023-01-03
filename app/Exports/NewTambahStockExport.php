<?php

namespace App\Exports;

use App\Models\ProductHistoryTambah;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;


class NewTambahStockExport implements FromQuery, WithHeadings
{
    use Exportable;

    protected $id;

    function __construct($id)
    {
        $this->id = $id;
    }

    public function query()
    {
        // return ProductHistoryTambah::query()->select('tanggal_transaksi', 'penginput', 'no_transaksi', 'sku_id', 'qty', 'tanggal_exp', 'status', 'message')->where('history_id', '=', $this->id);

        return ProductHistoryTambah::query()->selectRaw("tanggal_transaksi, penginput, no_transaksi, sku_id, qty, IF(tanggal_exp = '1970-01-01','-',tanggal_exp), status, message")->where('history_id', '=', $this->id);

        // return ProductHistoryInput::query("SELECT * FROM product_history_input");
        // return ProductHistory::query("SELECT * FROM product_history INNER JOIN history ON product_history.history_id = history.history_id INNER JOIN product ON product_history.sku_id = product.sku_id WHERE product_history.sku_id='{$this->id}'");
    }

    public function headings(): array
    {
        return ["TANGGAL", "PENGINPUT", "NO ORDER / NOTA / NO PO", "SKU", "QTY", "TANGGAL EXP", "STATUS", "CATATAN"];
    }
}
