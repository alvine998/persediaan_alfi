<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\DB;

class ValidateExcel implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        // Pecah value berdasarkan tipe ( 1 | 2 | 3 | 4)
        // 1 = TAMBAH-BARANG-BARU 2 = TAMBAH-STOK-MASSAL 3 = TEMPLATE-DATA-EXPORT 4 = TEMPLATE-DATA-GUDANG
        $explodeValueAndType = explode(':', $value);

        if ($explodeValueAndType[0] == 1) {
            if ($explodeValueAndType[1] != 'TAMBAH-BARANG-BARU.xlsx') {
                $fail('Anda Tidak Memasukkan File Tambah Barang Baru Yang Benar!');
            }
        } elseif ($explodeValueAndType[0] == 2) {
            if ($explodeValueAndType[1] != 'TAMBAH-STOK-MASSAL.xlsx') {
                $fail('Anda Tidak Memasukkan File Tambah Stok Massal Yang Benar!');
            }
        } elseif ($explodeValueAndType[0] == 3) {
            if ($explodeValueAndType[1] != 'TEMPLATE-DATA-EXPORT.xlsx') {
                $fail('Anda Tidak Memasukkan File Data Export Yang Benar!');
            }
        } else {
            if ($explodeValueAndType[1] != 'TEMPLATE-DATA-GUDANG.xlsx') {
                $fail('Anda Tidak Memasukkan File Data Gudang Yang Benar!');
            }
        }
    }
}
