<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductHistory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NewProductImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation,  SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Product([
            'brand_id'          => $row['brand'],
            'sku_id'            => $row['kode_sku'],
            'nm_barang'         => $row['nama_barang'],
            'kategori'          => $row['kategori'],
            'ukuran'            => $row['size_ukuran'],
            'lokasi'            => $row['lokasi'],
            'berat'             => $row['berat_kg'],
            'panjang'           => $row['panjang_cm'],
            'lebar'             => $row['lebar_cm'],
            'tinggi'            => $row['tinggi_cm'],
            'harga_modal'       => $row['harga_modal'],
            'harga_jual'        => $row['harga_jual'],
            'margin'            => $row['margin'],
            'link_photo'        => $row['link_foto'],
        ]);
    }

    public function rules(): array
    {
        return [
            '*.brand'       =>  'required',
            '*.kode_sku'    =>  'required',
            '*.nama_barang'         => 'required',
            '*.kategori'          => 'required',
            '*.size_ukuran'            => 'required',
            '*.berat_kg'             => 'required',
            '*.panjang_cm'           => 'required',
            '*.lebar_cm'             => 'required',
            '*.tinggi_cm'            => 'required',
            '*.harga_modal'       => 'required',
            '*.harga_jual'        => 'required',
            '*.margin'            => 'required',
            '*.link_foto'        => 'required',
            '*.lokasi'        => 'required'
        ];
    }
}
