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

class ProductImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation,  SkipsOnFailure
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

    public function customValidationAttributes()
    {
        return [
            'brand'  => 'Kode Brand',
            'sku_id'    => 'Kode SKU',
            'nm_barang' => 'Nama Barang',
            'kategori'  => 'Kategori',
            'ukuran'    => 'Ukuran',
            'berat'     => 'Berat',
            'panjang'   => 'Panjang',
            'lebar'     => 'Lebar',
            'tinggi'    => 'Tinggi',
            'harga_modal'   => 'Harga Modal',
            'harga_jual'    => 'Harga Jual',
            'margin'        => 'Margin',
            'link_photo'    => 'Link Photo',
            'lokasi'        => 'Lokasi'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'brand.required'  => 'Harap Isi Brand Id!',
            'kode_sku.unique' => 'SKU :input Ini Sudah Ada!',
            'kode_sku.max'      => 'Kode SKU Tidak Boleh Melebihi 40 Karakter',
            'nama_barang.required'         => 'Harap Isi Nama Barang',
            'kategori.required'          => 'Harap Isi Kategori',
            'size_ukuran.required'            => 'Harap Isi Size',
            'berat_kg.required'             => 'Harap Isi Berat',
            'panjang_cm.required'           => 'Harap Isi Panjang',
            'lebar_cm.required'             => 'Harap Isi Lebar',
            'tinggi_cm.required'            => 'Harap Isi Tinggi',
            'harga_modal.required'       => 'Harap Isi Harga Modal',
            'harga_jual.required'        => 'Harap Isi Harga Jual',
            'margin.required'            => 'Harap Isi Margin',
            'link_foto.required'        => 'Harap Isi Link Foto',
            'lokasi.required'        => 'Harap Isi Lokasi',
        ];
    }

    public function rules(): array
    {
        return [
            '*.brand'       =>  'required',
            '*.kode_sku'    =>  ['unique:product,sku_id', 'max:40'],
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
