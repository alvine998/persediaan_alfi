<?php

namespace App\Imports;

use App\Rules\ValidateProduct;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StockImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation,  SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Stock([
            'tanggal_transaksi'         => transformDate($row['tanggal']),
            'penginput'                 => $row['penginput'],
            'no_transaksi'              => $row['no_order_nota_no_po'],
            'sku_id'                    => $row['sku'],
            'qty'                       => $row['qty'],
            'tanggal_exp'               => transformDate($row['tanggal_exp']),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tanggal'       =>  'required',
            '*.sku'           =>  [new ValidateProduct]
        ];
    }
}
