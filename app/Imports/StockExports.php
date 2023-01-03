<?php

namespace App\Imports;

use App\Rules\ValidateProduct;
use App\Models\Stock;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StockExports implements ToModel, WithHeadingRow, SkipsOnError, WithValidation,  SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // dd($row);
        return new Stock([]);
    }

    public function rules(): array
    {
        return [
            '*.penginput'       =>  'required',
            '*.no_order'       =>  'required',
            '*.nama_pembeli'       =>  'required',
            '*.alamat'       =>  'required',
            '*.kota_kab'       =>  'required',
            '*.provinsi'       =>  'required',
            '*.no_hp'       =>  'required',
            '*.sku'       =>  ['required', new ValidateProduct],
            '*.qty'       =>  'required',
            '*.toko'       =>  'required',
            '*.tanggal'       =>  'required',
        ];
    }

    // public function customValidationAttributes()
    // {
    //     return [
    //         'penginput' => 'Penginput',
    //         'no_order'  => 'no_order',
    //         'nama_pembeli'
    //     ];
    // }
}
