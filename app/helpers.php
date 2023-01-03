<?php

use Illuminate\Support\Facades\DB;

function transformDate($value, $format = 'Y-m-d')
{
    try {
        return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
    } catch (\ErrorException $e) {
        return \Carbon\Carbon::createFromFormat($format, $value);
    }
}

function changeMyIndoTimestamp($additional = true)
{
    $dt = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    if ($additional)
        return $dt->format('Y-m-d G:i:s');
    else
        return $dt->format('Y-m-d');
}

function checkExpired()
{
    // Date
    $getRealDate = date('Y-m-d');
    // $getRealDate = date('Y-m-d', strtotime('2022-08-19'));

    $getNextFiveMonth = date('Y-m-d', strtotime($getRealDate . ' + 5 months'));

    $getOnlyMonth = date('m', strtotime($getNextFiveMonth));
    $getOnlyYear = date('Y', strtotime($getNextFiveMonth));

    // Find Result Data
    $getInformation = DB::select("SELECT * FROM stock WHERE Month(tanggal_exp) = '{$getOnlyMonth}' AND Year(tanggal_exp) = '{$getOnlyYear}' AND qty != 0 AND is_expired != 1");

    // Kalau ada data expired, maka eksekusi
    if (!empty($getInformation)) {
        // Cek apakah tanggal sudah pernah ada dalam data sebelumnya
        $checkDateExist = DB::select("SELECT * FROM expired INNER JOIN expired_detail ON expired.expired_id = expired_detail.expired_id WHERE expired.tanggal_rekam='{$getRealDate}'");

        if (empty($checkDateExist)) {
            // Buat data expired baru dengan tanggal sistem
            DB::statement("INSERT INTO expired(tanggal_rekam) VALUES('{$getRealDate}')");

            // Dapatkan data expired terakhir
            $getLatestExpiredId = collect(DB::select("SELECT * FROM expired ORDER BY expired.expired_id DESC LIMIT 1"))->first();
        } else {
            // Dapatkan data expired terakhir
            $getLatestExpiredId = collect(DB::select("SELECT * FROM expired WHERE expired.tanggal_rekam='{$getRealDate}' LIMIT 1"))->first();
        }


        // UBah status is expired menjadi 1
        foreach ($getInformation as $getInfo) {
            DB::statement("INSERT INTO expired_detail(expired_id,stock_id,sku_id) VALUES('{$getLatestExpiredId->expired_id}','{$getInfo->stock_id}','{$getInfo->sku_id}')");

            DB::statement("UPDATE stock SET is_expired = 1 WHERE stock.sku_id='{$getInfo->sku_id}'");
        }
    }

    // dump($getInformation);
    // dump($getOnlyYear);
    // dump($getOnlyMonth);
    // dd($getNextFiveMonth);
}
