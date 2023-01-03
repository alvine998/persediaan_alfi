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

class StockGudang implements ToModel, WithHeadingRow, SkipsOnError, WithValidation,  SkipsOnFailure
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
            '*.kode_sku'       =>  ['required', new ValidateProduct],
            '*.qty'       =>  'required',
        ];
    }
}
