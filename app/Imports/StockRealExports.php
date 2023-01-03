<?php

namespace App\Imports;

use App\Models\Export;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StockRealExports implements ToModel, WithHeadingRow
{
    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $getData = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$row['sku']}'"))->first();
        // dd($row);
        return new Export([
            'penginput'          => $row['penginput'],
            'nm_pembeli'          => $row['nama_pembeli'],
            'alamat'          => $row['alamat'],
            'kota_kab'          => $row['kota_kab'],
            'provinsi'          => $row['provinsi'],
            'no_hp'          => $row['no_hp'],
            'sku_id'          => $row['sku'],
            'qty'          => $row['qty'],
            'toko'          => $row['toko'],
            'tanggal'          => transformDate($row['tanggal']),
            'no_transaksi'  => $row['no_order'],
            'nm_barang'     => $getData->nm_barang,
            'kategori'      => $getData->kategori,
            'size'          => $getData->ukuran,
        ]);
    }
}
