<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\DB;

class ValidateProduct implements InvokableRule
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
        $getData = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$value}'"))->first();

        if (empty($getData)) {
            $fail(':input Tidak Termasuk Dalam Kode SKU.');
        }
    }
}
