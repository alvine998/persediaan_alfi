<?php

namespace App\Imports;

use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date as FacadesDate;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;


class NewTambahStockImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    // public function map($map): array
    // {
    //     $dates = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($map['tanggal']);

    //     $newDates = $dates->format('Y-m-d');
    //     return [
    //         'no_order_nota_no_po'   => $newDates

    //         // $map['tanggal']date('m/d/Y')
    //         // transformDate($map['no_order_nota_no_po'])
    //     ];
    // }

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
}
