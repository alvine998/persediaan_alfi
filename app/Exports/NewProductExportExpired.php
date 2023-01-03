<?php

namespace App\Exports;

use App\Models\ExpiredDetail;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewProductExportExpired implements FromQuery, WithHeadings
{
    use Exportable;

    protected $id;

    function __construct($id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return ExpiredDetail::query()->join('expired', 'expired_detail.expired_id', '=', 'expired.expired_id')->join('stock', 'expired_detail.stock_id', '=', 'stock.stock_id')->select('tanggal_rekam', 'expired_detail.sku_id', 'stock.qty', 'stock.tanggal_exp')->where('expired_detail.expired_id', '=', $this->id);
    }

    public function headings(): array
    {
        return ["TANGGAL REKAM", "KODE SKU", "QTY", "TANGGAL EXP"];
    }
}
