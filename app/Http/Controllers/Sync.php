<?php

namespace App\Http\Controllers;

use App\Exports\NewKurangStockExport;
use App\Exports\NewKurangStockManualExport;
use App\Exports\NewProductExport;
use App\Exports\NewProductExportExpired;
use App\Exports\NewProductExportHistory;
use App\Exports\NewTambahStockExport;
use App\Exports\ProductExport;
use App\Exports\ProductExportHistory;
use App\Exports\StockExport;
use App\Exports\StockExportss;
use App\Imports\NewKurangStockGudangImport;
use App\Imports\NewKurangStockImport;
use App\Imports\NewProductImport;
use App\Imports\NewTambahStockImport;
use App\Imports\ProductImport;
use App\Imports\ProductImportHistory;
use App\Imports\StockExports;
use App\Imports\StockGudang;
use App\Imports\StockImport;
use App\Imports\StockRealExports;
use App\Imports\StocksImport;
use App\Models\History;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Rules\ValidateExcel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDO;



class Sync extends Controller
{
    public function __construct()
    {
        // Check Expired
        checkExpired();
    }

    // Halaman Sync
    public function index($sub = '')
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        // Jika barang kosong maka larang masuk ke menu tambah stok massal
        if ($sub == 'tambah-produk-massal' || $sub == 'kurang-produk-massal') {
            $checkProduct = DB::select("SELECT * FROM product");

            if (empty($checkProduct)) {
                return redirect()->route('sync-sub', ['sub' => 'input-produk'])->with("messages", "Kamu Belum Memiliki Daftar Produk Yang Tersedia. Harap Input Produk Terlebih Dahulu!");
            }
        }

        // Data
        $data = [
            'title'     => 'Sync',
            'sub'       => $sub ? $sub : '',
            'product'   => Product::all(),
            'error'     => History::where('type', 1)->orderBy('history_id', 'DESC')->first(),
            'error2'    => History::where('type', 2)->orderBy('history_id', 'DESC')->first(),
            'error3'    => History::where('type', 3)->orderBy('history_id', 'DESC')->first(),
            'history'   => DB::select("SELECT * FROM history WHERE history.type <= 3 ORDER BY history.history_id DESC")
        ];


        return view('sync.index', $data);
    }

    // Export Product Barcode 
    public function exportbarcode()
    {
        // Validasi Data
        $getData = collect(DB::select("SELECT * FROM history INNER JOIN product_history ON history.history_id = product_history.history_id WHERE history.type=1 AND product_history.status != 'Gagal' ORDER BY product_history.history_id DESC LIMIT 1"))->first();

        // Dapatkan seluruh history data
        $getAllData = DB::select("SELECT * FROM product_history INNER JOIN product ON product_history.sku_id = product.sku_id WHERE type=1 AND status != 'Gagal' AND history_id='{$getData->history_id}'");

        $data = [
            'title'     => 'Qr Code',
            'product'   => $getAllData
        ];

        // DOMPDF
        $pdf = new Dompdf();
        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', true);
        // $options->set('chroot', base_url('assets/img/'));
        $pdf = new Dompdf($options);

        $pdf = new Dompdf();

        $pdf->loadHtml(view('sync/product-qrcode', $data));
        $pdf->setPaper('A4', 'landscape');

        $pdf->render();
        $pdf->stream(hash('ripemd160', 'Data-Product'), array("Attachment" => false));
    }

    // Export template expired product
    public function templateexpiredexport($id = '')
    {
        $data = [
            'title' => 'Product Expired',
            'data'  => DB::table('expired_detail')->join('expired', 'expired_detail.expired_id', '=', 'expired.expired_id')->join('stock', 'expired_detail.stock_id', '=', 'stock.stock_id')->select('tanggal_rekam', 'expired_detail.sku_id', 'stock.qty', 'stock.tanggal_exp')->where('expired_detail.expired_id', '=', $id)->get()
        ];

        // DOMPDF
        $pdf = new Dompdf();
        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', true);
        // $options->set('chroot', base_url('assets/img/'));
        $pdf = new Dompdf($options);

        $pdf = new Dompdf();

        $pdf->loadHtml(view('sync/product-expired', $data));
        $pdf->setPaper('A4', 'landscape');

        $pdf->render();
        $pdf->stream(hash('ripemd160', 'ProductExpired'), array("Attachment" => false));

        // return Excel::download(new NewProductExportExpired($id), 'ProductExpired.xlsx');
    }

    // Export template export Product
    public function templateexport($id = '')
    {
        // Pilih typenya itu apa
        $getHistory = collect(DB::select("SELECT * FROM history WHERE history.history_id='{$id}' LIMIT 1"))->first();

        if ($getHistory->type == 1) {
            return Excel::download(new NewProductExportHistory($id), 'ProductHistory.xlsx');
        } elseif ($getHistory->type == 2) {
            return Excel::download(new NewTambahStockExport($id), 'TambahStokHistory.xlsx');
        } elseif ($getHistory->type == 3) {
            return Excel::download(new NewKurangStockExport($id), 'KurangStokHistory.xlsx');
        }
    }

    // Export Product
    public function export()
    {
        return Excel::download(new NewProductExport, 'Product.xlsx');
    }

    // Import Product ( Progressive )
    public function import(Request $request)
    {
        // Validasi
        $request->validate([
            'my-file'              => ['required']
        ]);

        if ($request->file('my-file')->getClientOriginalName() != 'TAMBAH-BARANG-BARU.xlsx') {
            return redirect()->route('sync-sub', ['sub' => 'input-produk'])->with('messages', 'Mohon Untuk Memasukkan File Tambah Barang Baru Yang Benar Dengan Format ( TAMBAH-BARANG-BARU.xlsx )');
        }

        $rowProductImport = Excel::toArray(new NewProductImport, $request->file('my-file'));

        $checkSuccess = false;
        $setErrorMessage = [];

        // Excel Row Bug Blocking
        $checkRow = 0;
        $checkRowNull = 0;
        for ($checkIndex = 0; $checkIndex < count($rowProductImport[0]); $checkIndex++) {
            if ($rowProductImport[0][$checkIndex]['kode_sku'] == null) {
                ++$checkRowNull;
            }
        }

        $checkRow = $checkRowNull != 0 ? count($rowProductImport[0]) - $checkRowNull : count($rowProductImport[0]);

        for ($i = 0; $i < $checkRow; $i++) {
            $kodeSKU = $rowProductImport[0][$i]['kode_sku'];

            $brand_id = $rowProductImport[0][$i]['brand'];
            $sku_id = $kodeSKU;
            $nm_barang = $rowProductImport[0][$i]['nama_barang'];
            $kategori = $rowProductImport[0][$i]['kategori'];
            $ukuran = $rowProductImport[0][$i]['size_ukuran'];
            $lokasi = $rowProductImport[0][$i]['lokasi'];
            $berat = $rowProductImport[0][$i]['berat_kg'];
            $panjang = $rowProductImport[0][$i]['panjang_cm'];
            $lebar = $rowProductImport[0][$i]['lebar_cm'];
            $tinggi = $rowProductImport[0][$i]['tinggi_cm'];
            $harga_modal = $rowProductImport[0][$i]['harga_modal'];
            $harga_jual = $rowProductImport[0][$i]['harga_jual'];
            $margin = $rowProductImport[0][$i]['margin'];
            $link_photo = $rowProductImport[0][$i]['link_foto'];

            // INCREMENT ERROR
            $incError = false;

            $checkSKU = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$kodeSKU}'"))->first();

            // Cek Kode SKU lebih dari 40 karakter atau tidak
            if (strlen($kodeSKU) > 40) {
                array_push($setErrorMessage, [
                    'row'   => $i + 1,
                    'error' => "SKU <span class='fw-bold text-danger'>( {$kodeSKU} )</span> Ini Memiliki Lebih Dari 40 Karakter."
                ]);

                // Product History Input ( Semua Data Lengkap )
                DB::statement('INSERT INTO product_history_input(history_id,reset,brand_id,sku_id,nm_barang,kategori,ukuran,lokasi,berat,panjang,lebar,tinggi,harga_modal,harga_jual,margin,link_photo,status,message) VALUES("0","0","' . $brand_id . '","' . $sku_id . '","' . $nm_barang . '","' . $kategori . '","' . $ukuran . '","' . $lokasi . '","' . $berat . '","' . $panjang . '","' . $lebar . '","' . $tinggi . '","' . $harga_modal . '","' . $harga_jual . '","' . $margin . '","' . $link_photo . '","Gagal","SKU ( ' . $kodeSKU . ' ) Ini Memiliki Lebih Dari 40 Karakter.")');

                DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$rowProductImport[0][$i]['kode_sku']}','1','Gagal','0','SKU ( {$kodeSKU} ) Ini Memiliki Lebih Dari 40 Karakter.')");

                $incError = true;
            }

            //  Cek Kode Sku sudah terdaftar atau belum di database
            if (!empty($checkSKU)) {
                array_push($setErrorMessage, [
                    'row'   => $i + 1,
                    'error' => "SKU <span class='fw-bold text-danger'>( {$kodeSKU} )</span> Ini Sudah Terdaftar Di Dalam Data Produk."
                ]);

                // Product History Input ( Semua Data Lengkap )
                DB::statement('INSERT INTO product_history_input(history_id,reset,brand_id,sku_id,nm_barang,kategori,ukuran,lokasi,berat,panjang,lebar,tinggi,harga_modal,harga_jual,margin,link_photo,status,message) VALUES("0","0","' . $brand_id . '","' . $sku_id . '","' . $nm_barang . '","' . $kategori . '","' . $ukuran . '","' . $lokasi . '","' . $berat . '","' . $panjang . '","' . $lebar . '","' . $tinggi . '","' . $harga_modal . '","' . $harga_jual . '","' . $margin . '","' . $link_photo . '","Gagal","SKU ( ' . $kodeSKU . ' ) Ini Sudah Terdaftar Di Dalam Data Produk.")');

                DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$rowProductImport[0][$i]['kode_sku']}','1','Gagal','0','SKU ( {$kodeSKU} ) Ini Sudah Terdaftar Di Dalam Data Produk.')");

                $incError = true;
            }

            // Jika True, maka jalankan query untuk menambahkan data produk ke dalam database
            if ($incError == false) {
                $checkSuccess = true;

                DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$sku_id}','1','Sukses','0','Produk Sukses Ditambahkan.')");

                // Product History Input ( Semua Data Lengkap )
                DB::statement('INSERT INTO product_history_input(history_id,reset,brand_id,sku_id,nm_barang,kategori,ukuran,lokasi,berat,panjang,lebar,tinggi,harga_modal,harga_jual,margin,link_photo,status,message) VALUES("0","0","' . $brand_id . '","' . $sku_id . '","' . $nm_barang . '","' . $kategori . '","' . $ukuran . '","' . $lokasi . '","' . $berat . '","' . $panjang . '","' . $lebar . '","' . $tinggi . '","' . $harga_modal . '","' . $harga_jual . '","' . $margin . '","' . $link_photo . '","Sukses","Produk Sukses Ditambahkan.")');

                DB::statement('INSERT INTO product(brand_id,sku_id,nm_barang,kategori,ukuran,lokasi,berat,panjang,lebar,tinggi,harga_modal,harga_jual,margin,link_photo) VALUES("' . $brand_id . '","' . $sku_id . '","' . $nm_barang . '","' . $kategori . '","' . $ukuran . '","' . $lokasi . '","' . $berat . '","' . $panjang . '","' . $lebar . '","' . $tinggi . '","' . $harga_modal . '","' . $harga_jual . '","' . $margin . '","' . $link_photo . '")');
            }

            $incError = false;
        }

        // Cetak History
        $history = new History;
        $history->type = '1';
        $history->created_at = changeMyIndoTimestamp();
        $history->updated_at = changeMyIndoTimestamp();
        $history->status = (count($setErrorMessage) == 0) || ($checkSuccess == true && count($setErrorMessage) > 0) ? '1' : '0';

        // Cek setErrorMessage
        if (count($setErrorMessage) > 0) {
            $history->error_count = count($setErrorMessage);
            $history->save();

            $sessionData = [
                'my-status-input'  => $setErrorMessage
            ];

            session()->put('my-status-input', $sessionData);
        } else {
            $history->error_count = '0';
            $history->save();
            session()->forget('my-status-input');
        }

        // Set Product Information History
        // Dapatkan data history id terakhir yang ada 
        $getLatestHistoryId = collect(DB::select("SELECT * FROM history ORDER BY history.history_id DESC LIMIT 1"))->first();

        if (empty($getLatestHistoryId)) {
            $number = 1;
        } else {
            $number = $getLatestHistoryId->history_id;
        }

        // Ubah semua product historynya menggunakan history id yang benar
        DB::statement("UPDATE product_history SET history_id='{$number}' WHERE reset='0'");
        DB::statement("UPDATE product_history_input SET history_id='{$number}' WHERE reset='0'");
        // change resetnya ke 1
        DB::statement("UPDATE product_history SET reset='1'");
        DB::statement("UPDATE product_history_input SET reset='1'");

        return redirect()->route('sync-sub', ['sub' => 'input-produk']);
    }

    // Export Product
    public function exportstok()
    {
        return Excel::download(new StockExport, 'StockProduct.xlsx');
    }

    // Export Product
    public function exportstoks()
    {
        return Excel::download(new StockExportss, 'StockProductExport.xlsx');
    }

    // Proses import kurang produk massal
    public function importkurangprodukmassal(Request $request)
    {
        // Validasi
        $request->validate([
            'stock_gudang'              => 'required',
            'stock_export'              => 'required'
        ]);

        if ($request->file('stock_export')->getClientOriginalName() != 'TEMPLATE-DATA-EXPORT.xlsx') {
            return redirect()->route('sync-sub', ['sub' => 'kurang-produk-massal'])->with('messages', 'Mohon Untuk Memasukkan File Data Export Yang Benar Dengan Format ( TEMPLATE-DATA-EXPORT.xlsx )');
        }

        if ($request->file('stock_gudang')->getClientOriginalName() != 'TEMPLATE-DATA-GUDANG.xlsx') {
            return redirect()->route('sync-sub', ['sub' => 'kurang-produk-massal'])->with('messages', 'Mohon Untuk Memasukkan File Data Gudang Yang Benar Dengan Format ( TEMPLATE-DATA-GUDANG.xlsx )');
        }

        $rowKurangProductExport = Excel::toArray(new NewKurangStockImport, $request->file('stock_export'));
        // Kumpulkan semua data export menjadi satu sku dengan total quantity

        $checkSuccess = false;
        $setErrorMessage = [];

        // Excel Row Bug Blocking
        $checkRowExport = 0;
        $checkRowExportNull = 0;
        for ($checkIndex = 0; $checkIndex < count($rowKurangProductExport[0]); $checkIndex++) {
            if ($rowKurangProductExport[0][$checkIndex]['tanggal'] == null) {
                ++$checkRowExportNull;
            }
        }

        $checkRowExport = $checkRowExportNull != 0 ? count($rowKurangProductExport[0]) - $checkRowExportNull : count($rowKurangProductExport[0]);

        $storeExport = [];

        // Membuat unik sku export
        for ($i = 0; $i < $checkRowExport; $i++) {
            if (count($storeExport) == 0) {
                array_push($storeExport, $rowKurangProductExport[0][$i]['sku']);
            } else {
                if (!in_array($rowKurangProductExport[0][$i]['sku'], $storeExport)) {
                    array_push($storeExport, $rowKurangProductExport[0][$i]['sku']);
                }
            }
        }

        // Mengelola qty sku export
        $storeTemporary = [];

        for ($i = 0; $i <  count($storeExport); $i++) {
            $storeTemporaries = [];
            $countQty = 0;
            for ($j = 0; $j < count($rowKurangProductExport[0]); $j++) {
                if ($storeExport[$i] == $rowKurangProductExport[0][$j]['sku']) {
                    $countQty += $rowKurangProductExport[0][$j]['qty'];
                    array_push($storeTemporaries, [
                        'sku_id'    => $rowKurangProductExport[0][$j]['sku'],
                        'tanggal'    => $rowKurangProductExport[0][$j]['tanggal'],
                        'penginput'    => $rowKurangProductExport[0][$j]['penginput'],
                        'no_order'    => $rowKurangProductExport[0][$j]['no_order'],
                        'nama_pembeli'    => $rowKurangProductExport[0][$j]['nama_pembeli'],
                        'alamat'    => $rowKurangProductExport[0][$j]['alamat'],
                        'kota_kab'    => $rowKurangProductExport[0][$j]['kota_kab'],
                        'provinsi'    => $rowKurangProductExport[0][$j]['provinsi'],
                        'no_hp'    => $rowKurangProductExport[0][$j]['no_hp'],
                        'qty'    => $rowKurangProductExport[0][$j]['qty'],
                        'toko'    => $rowKurangProductExport[0][$j]['toko']
                    ]);
                }
            }

            array_push($storeTemporary, [
                'sku_id'    => $storeExport[$i],
                'qty'       => $countQty,
                'statusError' => 0,
                'item'      => $storeTemporaries

            ]);

            $countQty = 0;
        }


        // Bagian Gudang
        $rowKurangProductGudang = Excel::toArray(new NewKurangStockGudangImport, $request->file('stock_gudang'));

        // Excel Row Bug Blocking
        $checkRowGudang = 0;
        $checkRowGudangNull = 0;
        for ($checkIndex = 0; $checkIndex < count($rowKurangProductGudang[0]); $checkIndex++) {
            if ($rowKurangProductGudang[0][$checkIndex]['kode_sku'] == null) {
                ++$checkRowGudangNull;
            }
        }

        $checkRowGudang = $checkRowGudangNull != 0 ? count($rowKurangProductGudang[0]) - $checkRowGudangNull : count($rowKurangProductGudang[0]);


        // Cek validasi kode sku export dan gudang
        for ($m = 0; $m < count($storeTemporary); $m++) {


            for ($t = 0; $t < $checkRowGudang; $t++) {

                // Jika sku antara export dan gudang valid, maka lulus
                if ($storeTemporary[$m]['sku_id'] == $rowKurangProductGudang[0][$t]['kode_sku']) {
                    $storeTemporary[$m]['statusError'] = 1;

                    // Jika quantity antara export dan gudang valid, maka lulus
                    if ($storeTemporary[$m]['qty'] == $rowKurangProductGudang[0][$t]['qty']) {
                        $storeTemporary[$m]['statusError'] = 1;
                        break;
                    } else {
                        // Jika tidak match, maka siap-siap buat history seperti inin
                        $storeTemporary[$m]['statusError'] = 0;

                        // Loop setiap barang yang error karena quantity berbeda
                        for ($bb = 0; $bb < count($storeTemporary[$m]['item']); $bb++) {
                            // Tidak Lulus
                            $sku_id = $storeTemporary[$m]['item'][$bb]['sku_id'];

                            $getProduct = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$sku_id}' LIMIT 1"))->first();

                            if (empty($getProduct)) {
                                $nm_barangs = 'Tidak Ada Dalam Database';
                                $kategori = 'Tidak Ada Dalam Database';
                                $size = 'Tidak Ada Dalam Database';
                            } else {
                                $nm_barangs = $getProduct->nm_barang;
                                $kategori = $getProduct->kategori;
                                $size = $getProduct->ukuran;
                            }

                            $penginput = $storeTemporary[$m]['item'][$bb]['penginput'];
                            $no_transaksi = $storeTemporary[$m]['item'][$bb]['no_order'];
                            $nm_pembeli = $storeTemporary[$m]['item'][$bb]['nama_pembeli'];
                            $alamat = $storeTemporary[$m]['item'][$bb]['alamat'];
                            $kota_kab = $storeTemporary[$m]['item'][$bb]['kota_kab'];
                            $provinsi = $storeTemporary[$m]['item'][$bb]['provinsi'];
                            $no_hp = $storeTemporary[$m]['item'][$bb]['no_hp'];
                            $qty = $storeTemporary[$m]['item'][$bb]['qty'];
                            $toko = $storeTemporary[$m]['item'][$bb]['toko'];
                            $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($storeTemporary[$m]['item'][$bb]['tanggal'])->format('Y-m-d');

                            array_push($setErrorMessage, [
                                'row'   => $i + 1,
                                'error' => "SKU <span class='fw-bold text-danger'>( {$sku_id} )</span> Quantity Produk Ini Tidak Sesuai Dengan Gudang."
                            ]);

                            // Product History Input ( Semua Data Lengkap )
                            DB::statement('INSERT INTO product_history_kurang(history_id,reset,tanggal,penginput,no_transaksi,nm_pembeli,alamat,kota_kab,provinsi,no_hp,sku_id,nm_barang,kategori,size,qty,toko,status,message) VALUES("0","0","' . $tanggal . '","' . $penginput . '","' . $no_transaksi . '","' . $nm_pembeli . '","' . $alamat . '","' . $kota_kab . '","' . $provinsi . '","' . $no_hp . '","' . $sku_id . '","' . $nm_barangs . '","' . $kategori . '","' . $size . '","' . $qty . '","' . $toko . '","Gagal","Quantity Produk Ini Tidak Sesuai Dengan Gudang")');

                            DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$sku_id}','3','Gagal','0','SKU ( {$sku_id} ) Quantity Produk Ini Tidak Sesuai Dengan Gudang.')");
                        }
                        break;
                    }
                }

                // Jika sampai diakhir sku masih tidak ditemukan, berarti memang sku tidak bisa dilacak di dalam stok gudang yang tersedia sebelumnya
                if ($t == $checkRowGudang - 1 && $storeTemporary[$m]['sku_id'] != $rowKurangProductGudang[0][$t]['kode_sku']) {
                    $storeTemporary[$m]['statusError'] = 0;

                    // Tidak Lulus
                    // Loop setiap barang yang error karena sku tidak ditemukan
                    for ($bb = 0; $bb < count($storeTemporary[$m]['item']); $bb++) {
                        // Tidak Lulus
                        $sku_id = $storeTemporary[$m]['item'][$bb]['sku_id'];

                        $penginput = $storeTemporary[$m]['item'][$bb]['penginput'];
                        $no_transaksi = $storeTemporary[$m]['item'][$bb]['no_order'];
                        $nm_pembeli = $storeTemporary[$m]['item'][$bb]['nama_pembeli'];
                        $alamat = $storeTemporary[$m]['item'][$bb]['alamat'];
                        $kota_kab = $storeTemporary[$m]['item'][$bb]['kota_kab'];
                        $provinsi = $storeTemporary[$m]['item'][$bb]['provinsi'];
                        $no_hp = $storeTemporary[$m]['item'][$bb]['no_hp'];
                        $qty = $storeTemporary[$m]['item'][$bb]['qty'];
                        $toko = $storeTemporary[$m]['item'][$bb]['toko'];
                        $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($storeTemporary[$m]['item'][$bb]['tanggal'])->format('Y-m-d');

                        array_push($setErrorMessage, [
                            'row'   => $i + 1,
                            'error' => "SKU <span class='fw-bold text-danger'>( {$sku_id} )</span> Tidak Ditemukan Di Gudang"
                        ]);

                        // Product History Input ( Semua Data Lengkap )
                        DB::statement('INSERT INTO product_history_kurang(history_id,reset,tanggal,penginput,no_transaksi,nm_pembeli,alamat,kota_kab,provinsi,no_hp,sku_id,qty,toko,status,message) VALUES("0","0","' . $tanggal . '","' . $penginput . '","' . $no_transaksi . '","' . $nm_pembeli . '","' . $alamat . '","' . $kota_kab . '","' . $provinsi . '","' . $no_hp . '","' . $sku_id . '","' . $qty . '","' . $toko . '","Gagal","Tidak Ditemukan Di Gudang")');

                        DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$sku_id}','3','Gagal','0','SKU ( {$sku_id} ) Tidak Ditemukan Di Gudang.')");
                    }
                    break;
                }
            }
        }

        // Filter Export Data Element
        $fixedStoreTemporarySize = count($storeTemporary);
        for ($j = 0; $j < $fixedStoreTemporarySize; $j++) {
            if ($storeTemporary[$j]['statusError'] == 0) {
                unset($storeTemporary[$j]);
            }
        }

        // Rearrange Array Index
        $arrangeStoreTemporary = array_values($storeTemporary);
        // dd($arrangeStoreTemporary);

        // Lakukan Looping Data Sebenarnya
        for ($b = 0; $b < count($arrangeStoreTemporary); $b++) {
            for ($bb = 0; $bb < count($arrangeStoreTemporary[$b]['item']); $bb++) {
                $sku_id = $arrangeStoreTemporary[$b]['item'][$bb]['sku_id'];

                $getProduct = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$sku_id}' LIMIT 1"))->first();

                if (empty($getProduct)) {
                    $nm_barangs = 'No Data';
                    $kategori = 'No Data';
                    $size = 'No Data';
                } else {
                    $nm_barangs = $getProduct->nm_barang;
                    $kategori = $getProduct->kategori;
                    $size = $getProduct->ukuran;
                }

                $penginput = $arrangeStoreTemporary[$b]['item'][$bb]['penginput'];
                $no_transaksi = $arrangeStoreTemporary[$b]['item'][$bb]['no_order'];
                $nm_pembeli = $arrangeStoreTemporary[$b]['item'][$bb]['nama_pembeli'];
                $alamat = $arrangeStoreTemporary[$b]['item'][$bb]['alamat'];
                $kota_kab = $arrangeStoreTemporary[$b]['item'][$bb]['kota_kab'];
                $provinsi = $arrangeStoreTemporary[$b]['item'][$bb]['provinsi'];
                $no_hp = $arrangeStoreTemporary[$b]['item'][$bb]['no_hp'];
                $qty = $arrangeStoreTemporary[$b]['item'][$bb]['qty'];
                $toko = $arrangeStoreTemporary[$b]['item'][$bb]['toko'];
                $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($arrangeStoreTemporary[$b]['item'][$bb]['tanggal'])->format('Y-m-d');

                // INCREMENT ERROR
                $incError = false;

                // Lacak Jika Error terjadi karena tidak ada sku di dalam database
                if (empty($getProduct) && $incError != true) {
                    array_push($setErrorMessage, [
                        'row'   => $i + 1,
                        'error' => "SKU <span class='fw-bold text-danger'>( {$sku_id} )</span> Match Dengan Bagian Gudang, Tetapi Hal Demikian Tidak Dapat Ditemukan Di Sistem Kami."
                    ]);

                    // Product History Input ( Semua Data Lengkap )
                    DB::statement('INSERT INTO product_history_kurang(history_id,reset,tanggal,penginput,no_transaksi,nm_pembeli,alamat,kota_kab,provinsi,no_hp,sku_id,nm_barang,kategori,size,qty,toko,status,message) VALUES("0","0","' . $tanggal . '","' . $penginput . '","' . $no_transaksi . '","' . $nm_pembeli . '","' . $alamat . '","' . $kota_kab . '","' . $provinsi . '","' . $no_hp . '","' . $sku_id . '","No Data","No Data","No Data","' . $qty . '","' . $toko . '","Gagal","SKU ( ' . $sku_id . ' ) Match Dengan Bagian Gudang, Tetapi Hal Demikian Tidak Dapat Ditemukan Di Sistem Kami")');

                    DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$sku_id}','3','Gagal','0','SKU ( {$sku_id} ) Match Dengan Bagian Gudang, Tetapi Hal Demikian Tidak Dapat Ditemukan Di Sistem Kami.')");

                    $incError = true;
                }
                // End Validasi Lacak Jika Error terjadi karena tidak ada sku di dalam database


                // Periksa total stok 
                $getStock = DB::select("SELECT * FROM stock WHERE stock.sku_id='{$sku_id}' ORDER BY stock.tanggal_transaksi ASC");

                // Jika stok export lebih besar dari stok yang tersimpan di sistem maka tolak
                // Jika Kosong Maka Tolak
                if (empty($getStock) && $incError != true) {
                    array_push($setErrorMessage, [
                        'row'   => $i + 1,
                        'error' => "SKU <span class='fw-bold text-danger'>( {$sku_id} )</span> Ini Sama Sekali Belum Memiliki Stok! Anda Tidak Bisa Melakukan Pengurangan Stok Karenanya"
                    ]);

                    // Product History Input ( Semua Data Lengkap )
                    DB::statement('INSERT INTO product_history_kurang(history_id,reset,tanggal,penginput,no_transaksi,nm_pembeli,alamat,kota_kab,provinsi,no_hp,sku_id,nm_barang,kategori,size,qty,toko,status,message) VALUES("0","0","' . $tanggal . '","' . $penginput . '","' . $no_transaksi . '","' . $nm_pembeli . '","' . $alamat . '","' . $kota_kab . '","' . $provinsi . '","' . $no_hp . '","' . $sku_id . '","No Data","No Data","No Data","' . $qty . '","' . $toko . '","Gagal","SKU ( ' . $sku_id . ' ) Ini Sama Sekali Belum Memiliki Stok! Anda Tidak Bisa Melakukan Pengurangan Stok Karenanya")');

                    DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$sku_id}','3','Gagal','0','SKU ( {$sku_id} ) Ini Sama Sekali Belum Memiliki Stok! Anda Tidak Bisa Melakukan Pengurangan Stok Karenanya.')");

                    $incError = true;
                } elseif (!empty($getStock) && $incError != true) {

                    // $qtyKurangExport = $rowStockGudang[0][$i]['qty'];

                    $getTotalStock = 0;
                    foreach ($getStock as $checkP) {
                        $getTotalStock += $checkP->qty;
                    }

                    // Jika total stock dari semua barang per tanggal 0, maka langsung kembalikan pesan
                    if ($getTotalStock == 0 && $incError != true) {
                        array_push($setErrorMessage, [
                            'row'   => $i + 1,
                            'error' => "Semua Stok Transaksi Yang Berkaitan Dengan SKU <span class='text-danger fw-bold'>( {$sku_id} )</span> Ini Telah Habis"
                        ]);

                        // Product History Input ( Semua Data Lengkap )
                        DB::statement('INSERT INTO product_history_kurang(history_id,reset,tanggal,penginput,no_transaksi,nm_pembeli,alamat,kota_kab,provinsi,no_hp,sku_id,nm_barang,kategori,size,qty,toko,status,message) VALUES("0","0","' . $tanggal . '","' . $penginput . '","' . $no_transaksi . '","' . $nm_pembeli . '","' . $alamat . '","' . $kota_kab . '","' . $provinsi . '","' . $no_hp . '","' . $sku_id . '","No Data","No Data","No Data","' . $qty . '","' . $toko . '","Gagal","Semua Stok Transaksi Yang Berkaitan Dengan SKU ( ' . $sku_id . ' ) Ini Telah Habis")');

                        DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$sku_id}','3','Gagal','0','Semua Stok Transaksi Yang Berkaitan Dengan SKU ( {$sku_id} ) Ini Telah Habis')");

                        $incError = true;
                    }

                    // Sebelum loop barang per tanggal dilakukan, cek dulu apakah stok export tersebut lebih besar dari keseluruhan stok yang dimiliki di database atau tidak
                    if ($qty > $getTotalStock && $incError != true) {
                        // Cek masih ada sisa atau tidak di dalam stoknya
                        array_push($setErrorMessage, [
                            'row'   => $i + 1,
                            'error' => "Stok Export SKU <span class='text-danger fw-bold'>{$sku_id}</span>: <span class='text-danger fw-bold'>{$qty}</span>, Stok Yang Tersisa Di Dalam Sistem Hanya Berjumlah <span class='text-danger fw-bold'>{$getTotalStock}</span> Dan Tidak Bisa Dilakukan Pengurangan."
                        ]);

                        // Product History Input ( Semua Data Lengkap )
                        DB::statement('INSERT INTO product_history_kurang(history_id,reset,tanggal,penginput,no_transaksi,nm_pembeli,alamat,kota_kab,provinsi,no_hp,sku_id,nm_barang,kategori,size,qty,toko,status,message) VALUES("0","0","' . $tanggal . '","' . $penginput . '","' . $no_transaksi . '","' . $nm_pembeli . '","' . $alamat . '","' . $kota_kab . '","' . $provinsi . '","' . $no_hp . '","' . $sku_id . '","No Data","No Data","No Data","' . $qty . '","' . $toko . '","Stok Export SKU ' . $sku_id . ': ' . $qty . ', Stok Yang Tersisa Di Dalam Sistem Hanya Berjumlah ' . $getTotalStock . ' Dan Tidak Bisa Dilakukan Pengurangan")');

                        DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$sku_id}','3','Gagal','0','Stok Export SKU {$sku_id}: {$qty}, Stok Yang Tersisa Di Dalam Sistem Hanya Berjumlah {$getTotalStock} Dan Tidak Bisa Dilakukan Pengurangan')");

                        $incError = true;
                    }
                }
                // End jika stok export lebih besar dari stok yang tersimpan di sistem maka tolak


                // Jika True, maka jalankan query untuk menambahkan data produk ke dalam database
                if ($incError == false) {
                    $checkSuccess = true;



                    foreach ($getStock as $p) {
                        // Cek apakah qty database ini kosong atau tidak
                        if ($p->qty != 0) {
                            // Cek apakah stok export lebih kecil dari stok qty per periode atau tidak
                            if ($qty < $p->qty) {
                                DB::statement("UPDATE stock SET stock.qty = stock.qty - {$qty} WHERE stock.sku_id='{$sku_id}' AND stock.tanggal_transaksi='{$p->tanggal_transaksi}'");
                                break;
                            } else {
                                $qty -= $p->qty;
                                DB::statement("UPDATE stock SET stock.qty = 0 WHERE stock.sku_id='{$sku_id}' AND stock.tanggal_transaksi='{$p->tanggal_transaksi}'");
                            }
                        }
                    }


                    DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$sku_id}','3','Sukses','0','Stok Produk Sukses Dikurangi.')");

                    // Product History Input ( Semua Data Lengkap )
                    DB::statement('INSERT INTO product_history_kurang(history_id,reset,tanggal,penginput,no_transaksi,nm_pembeli,alamat,kota_kab,provinsi,no_hp,sku_id,nm_barang,kategori,size,qty,toko,status,message) VALUES("0","0","' . $tanggal . '","' . $penginput . '","' . $no_transaksi . '","' . $nm_pembeli . '","' . $alamat . '","' . $kota_kab . '","' . $provinsi . '","' . $no_hp . '","' . $sku_id . '","' . $nm_barangs . '","' . $kategori . '","' . $size . '","' . $qty . '","' . $toko . '","Sukses","Stok Produk Berhasil Dikurangi")');

                    DB::statement('INSERT INTO export(tanggal,penginput,no_transaksi,nm_pembeli,alamat,kota_kab,provinsi,no_hp,sku_id,nm_barang,kategori,size,qty,toko) VALUES ("' . $tanggal . '","' . $penginput . '","' . $no_transaksi . '","' . $nm_pembeli . '","' . $alamat . '","' . $kota_kab . '","' . $provinsi . '","' . $no_hp . '","' . $sku_id . '","' . $nm_barangs . '","' . $kategori . '","' . $size . '","' . $qty . '","' . $toko . '")');
                }

                $incError = false;
            }
        }

        // Cetak History
        $history = new History;
        $history->type = '3';
        $history->created_at = changeMyIndoTimestamp();
        $history->updated_at = changeMyIndoTimestamp();
        $history->status = (count($setErrorMessage) == 0) || ($checkSuccess == true && count($setErrorMessage) > 0) ? '1' : '0';

        // Cek setErrorMessage
        if (count($setErrorMessage) > 0) {
            $history->error_count = count($setErrorMessage);
            $history->save();

            $sessionData = [
                'my-status-kurang'  => $setErrorMessage
            ];

            session()->put('my-status-kurang', $sessionData);
        } else {
            $history->error_count = '0';
            $history->save();
            session()->forget('my-status-kurang');
        }

        // Set Product Information History
        // Dapatkan data history id terakhir yang ada 
        $getLatestHistoryId = collect(DB::select("SELECT * FROM history ORDER BY history.history_id DESC LIMIT 1"))->first();

        if (empty($getLatestHistoryId)) {
            $number = 1;
        } else {
            $number = $getLatestHistoryId->history_id;
        }

        // Ubah semua product historynya menggunakan history id yang benar
        DB::statement("UPDATE product_history SET history_id='{$number}' WHERE reset='0'");
        DB::statement("UPDATE product_history_kurang SET history_id='{$number}' WHERE reset='0'");
        // change resetnya ke 1
        DB::statement("UPDATE product_history SET reset='1'");
        DB::statement("UPDATE product_history_kurang SET reset='1'");

        return redirect()->route('sync-sub', ['sub' => 'kurang-produk-massal']);

        dd('Block By System');







        $stockExport = $request->file('stock_export')->store('ExcelExport');
        $stockGudang = $request->file('stock_gudang')->store('ExcelGudang');

        $importGudang = new StockGudang;
        $importGudang->import($stockGudang);

        $importExport = new StockExports;
        $importExport->import($stockExport);

        // dd($importGudang->failures());
        if ($importExport->failures()->isNotEmpty()) {
            return redirect()->route('sync-sub', ['sub' => 'kurang-produk-massal'])->withFailures($importExport->failures());
        }

        if ($importGudang->failures()->isNotEmpty()) {
            return redirect()->route('sync-sub', ['sub' => 'kurang-produk-massal'])->withFailures($importGudang->failures());
        }

        $rowStockExport = Excel::toArray(new StocksImport, $request->file('stock_export'));
        $rowStockGudang = Excel::toArray(new StocksImport, $request->file('stock_gudang'));
        // dump($rowStockExport[0]);
        // dd($rowStockGudang[0]);

        // Pencocokan Starts Now
        $getError = 0;
        $getErrorMessage = [];


        for ($i = 0; $i < count($rowStockGudang[0]); $i++) {
            // Cek kesamaan kode sku
            if ($rowStockGudang[0][$i]['kode_sku'] != $rowStockExport[0][$i]['sku']) {
                array_push($getErrorMessage, [
                    'row'   => $i + 1,
                    'error' => "Pada Baris Ini, Kode SKU ( {$rowStockGudang[0][$i]['kode_sku']} ) Di Bagian Gudang Dan Kode SKU ( {$rowStockExport[0][$i]['sku']} ) Di Bagian Export Tidak Sama!"
                ]);
            }

            // Cek apakah stoknya sama atau tidak
            if ($rowStockGudang[0][$i]['qty'] != $rowStockExport[0][$i]['qty']) {
                array_push($getErrorMessage, [
                    'row'   => $i + 1,
                    'error' => "Stok Di Bagian Gudang ( {$rowStockGudang[0][$i]['qty']} ) Dengan Stok Di Bagian Export ( {$rowStockExport[0][$i]['qty']} ) Pada Baris Ini Berbeda!"
                ]);
            }

            // Periksa total stok 
            $getProduct = DB::select("SELECT * FROM stock WHERE stock.sku_id='{$rowStockGudang[0][$i]['kode_sku']}' ORDER BY stock.tanggal_transaksi ASC");
            // Dapatkan Nama produk pribadi
            $getSingleProduct = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$rowStockGudang[0][$i]['kode_sku']}'"))->first();

            // INCREMENT
            $inc = 0;

            // Jika Kosong Maka Tolak
            if (empty($getProduct)) {
                array_push($getErrorMessage, [
                    'row'   => $i + 1,
                    'error' => "Produk ( {$getSingleProduct->nm_barang} ) Ini Sama Sekali Belum Memiliki Stok! Anda Tidak Bisa Melakukan Pengurangan Stok Karenanya"
                ]);
                $inc = 1;
            } elseif (!empty($getProduct)) {
                $qtyKurangExport = $rowStockGudang[0][$i]['qty'];

                $getTotalStock = 0;
                foreach ($getProduct as $checkP) {
                    $getTotalStock += $checkP->qty;
                }

                // Jika total stock dari semua barang per tanggal 0, maka langsung kembalikan pesan
                if ($getTotalStock == 0 && $inc != 1) {
                    array_push($getErrorMessage, [
                        'row'   => $i + 1,
                        'error' => "Semua Stok Transaksi Yang Berkaitan Dengan Produk ( {$getSingleProduct->nm_barang} ) Ini Telah Habis"
                    ]);
                    $inc = 2;
                }

                // Sebelum loop barang per tanggal dilakukan, cek dulu apakah stok export tersebut lebih besar dari keseluruhan stok yang dimiliki di database atau tidak
                if ($qtyKurangExport > $getTotalStock && $inc != 2 && $inc != 1) {
                    array_push($getErrorMessage, [
                        'row'   => $i + 1,
                        'error' => "Stok Export Produk ( {$getSingleProduct->nm_barang} ) Melebihi Kapasitas Stok Sistem Yang Telah Tercatat"
                    ]);

                    $inc = 3;
                }

                if ($inc != 1 && $inc != 2 && $inc != 3) {


                    foreach ($getProduct as $p) {
                        // Cek apakah qty database ini kosong atau tidak
                        if ($p->qty != 0) {
                            // Cek apakah stok export lebih kecil dari stok qty per periode atau tidak
                            if ($qtyKurangExport < $p->qty) {
                                DB::statement("UPDATE stock SET stock.qty = stock.qty - {$qtyKurangExport} WHERE stock.sku_id='{$rowStockGudang[0][$i]['kode_sku']}' AND stock.tanggal_transaksi='{$p->tanggal_transaksi}'");
                                break;
                            } else {
                                $qtyKurangExport -= $p->qty;
                                DB::statement("UPDATE stock SET stock.qty = 0 WHERE stock.sku_id='{$rowStockGudang[0][$i]['kode_sku']}' AND stock.tanggal_transaksi='{$p->tanggal_transaksi}'");
                            }
                        }
                    }
                }
            }
        }


        $history = new History;
        $history->type = '3';
        $history->created_at = changeMyIndoTimestamp();
        $history->updated_at = changeMyIndoTimestamp();

        if (count($getErrorMessage) != 0) {
            $sessionData = [
                'my-error'  => $getErrorMessage
            ];

            $history->status = '0';
            $history->error_count = count($getErrorMessage);

            $history->save();

            session()->put('my-error', $sessionData);
            return redirect()->route('sync-sub', ['sub' => 'kurang-produk-massal']);
        } else {
            $history->status = '1';
            $history->error_count = '0';

            $history->save();

            $realExport = new StockRealExports;
            $realExport->import($stockExport);

            session()->forget('my-error');
            return redirect()->route('sync-sub', ['sub' => 'kurang-produk-massal']);
        }

        // $import = new StocksImport;
        // dd($rows[0]);
    }




    // Proses import tambah produk massal
    public function importtambahprodukmassal(Request $request)
    {
        // Validasi
        $request->validate([
            'my-files'              => 'required'
        ]);

        if ($request->file('my-files')->getClientOriginalName() != 'TAMBAH-STOK-MASSAL.xlsx') {
            return redirect()->route('sync-sub', ['sub' => 'tambah-produk-massal'])->with('messages', 'Mohon Untuk Memasukkan File Tambah Stok Massal Yang Benar Dengan Format ( TAMBAH-STOK-MASSAL.xlsx )');
        }

        $rowTambahProduct = Excel::toArray(new NewTambahStockImport, $request->file('my-files'));

        $checkSuccess = false;
        $setErrorMessage = [];

        // Excel Row Bug Blocking
        $checkRow = 0;
        $checkRowNull = 0;
        for ($checkIndex = 0; $checkIndex < count($rowTambahProduct[0]); $checkIndex++) {
            if ($rowTambahProduct[0][$checkIndex]['tanggal'] == null) {
                ++$checkRowNull;
            }
        }

        $checkRow = $checkRowNull != 0 ? count($rowTambahProduct[0]) - $checkRowNull : count($rowTambahProduct[0]);

        for ($i = 0; $i < $checkRow; $i++) {
            $kodeSKU = $rowTambahProduct[0][$i]['sku'];

            $tanggalExpModified = $rowTambahProduct[0][$i]['tanggal_exp'];
            if ($rowTambahProduct[0][$i]['tanggal_exp'] == '' || $rowTambahProduct[0][$i]['tanggal_exp'] == '-') {
                $tanggalExpModified = '1970-01-01';
            } else {
                $tanggalExpModified = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowTambahProduct[0][$i]['tanggal_exp'])->format('Y-m-d');
            }

            $tanggal_transaksi = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowTambahProduct[0][$i]['tanggal'])->format('Y-m-d');
            $penginput = $rowTambahProduct[0][$i]['penginput'];
            $no_transaksi = $rowTambahProduct[0][$i]['no_order_nota_no_po'];
            $tanggal_exp = $tanggalExpModified;
            $qty = $rowTambahProduct[0][$i]['qty'];
            // $tanggal_exp = $rowTambahProduct[0][$i]['tanggal_exp'];

            // INCREMENT ERROR
            $incError = false;

            $checkSKU = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$kodeSKU}'"))->first();
            // dd($checkSKU);

            //  Cek Kode Sku sudah ada atau belum di database sebelum stoknya ditambahkan
            if (empty($checkSKU)) {
                array_push($setErrorMessage, [
                    'row'   => $i + 1,
                    'error' => "SKU <span class='fw-bold text-danger'>( {$kodeSKU} )</span> Tidak Valid."
                ]);

                // Product History Tambah ( Semua Data Lengkap )
                DB::statement('INSERT INTO product_history_tambah(history_id,reset,tanggal_transaksi,no_transaksi,penginput,sku_id,qty,tanggal_exp,status,message) VALUES("0","0","' . $tanggal_transaksi . '","' . $no_transaksi . '","' . $penginput . '","' . $kodeSKU . '","' . $qty . '","' . $tanggal_exp . '","Gagal","SKU ( ' . $kodeSKU . ' ) Tidak Valid.")');

                DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$rowTambahProduct[0][$i]['sku']}','2','Gagal','0','SKU ( {$kodeSKU} ) Tidak Valid.')");

                $incError = true;
            }

            // Jika True, maka jalankan query untuk menambahkan data produk ke dalam database
            if ($incError == false) {
                $checkSuccess = true;

                DB::statement("INSERT INTO product_history(history_id,sku_id,type,status,reset,message) VALUES('0','{$kodeSKU}','2','Sukses','0','Stok Produk Sukses Ditambahkan.')");

                // Product History Input ( Semua Data Lengkap )
                DB::statement('INSERT INTO product_history_tambah(history_id,reset,tanggal_transaksi,no_transaksi,penginput,sku_id,qty,tanggal_exp,status,message) VALUES("0","0","' . $tanggal_transaksi . '","' . $no_transaksi . '","' . $penginput . '","' . $kodeSKU . '","' . $qty . '","' . $tanggal_exp . '","Sukses","Stok Produk Sukses Ditambahkan")');

                DB::statement('INSERT INTO stock(tanggal_transaksi,no_transaksi,penginput,sku_id,qty,tanggal_exp) VALUES("' . $tanggal_transaksi . '","' . $no_transaksi . '","' . $penginput . '","' . $kodeSKU . '","' . $qty . '","' . $tanggal_exp . '")');
            }

            $incError = false;
        }

        // Cetak History
        $history = new History;
        $history->type = '2';
        $history->created_at = changeMyIndoTimestamp();
        $history->updated_at = changeMyIndoTimestamp();
        $history->status = (count($setErrorMessage) == 0) || ($checkSuccess == true && count($setErrorMessage) > 0) ? '1' : '0';

        // Cek setErrorMessage
        if (count($setErrorMessage) > 0) {
            $history->error_count = count($setErrorMessage);
            $history->save();

            $sessionData = [
                'my-status-tambah'  => $setErrorMessage
            ];

            session()->put('my-status-tambah', $sessionData);
        } else {
            $history->error_count = '0';
            $history->save();
            session()->forget('my-status-tambah');
        }

        // Set Product Information History
        // Dapatkan data history id terakhir yang ada 
        $getLatestHistoryId = collect(DB::select("SELECT * FROM history ORDER BY history.history_id DESC LIMIT 1"))->first();

        if (empty($getLatestHistoryId)) {
            $number = 1;
        } else {
            $number = $getLatestHistoryId->history_id;
        }

        // Ubah semua product historynya menggunakan history id yang benar
        DB::statement("UPDATE product_history SET history_id='{$number}' WHERE reset='0'");
        DB::statement("UPDATE product_history_tambah SET history_id='{$number}' WHERE reset='0'");
        // change resetnya ke 1
        DB::statement("UPDATE product_history SET reset='1'");
        DB::statement("UPDATE product_history_tambah SET reset='1'");

        return redirect()->route('sync-sub', ['sub' => 'tambah-produk-massal']);
    }

    // Delete product
    public function deleteproduct($id = 0)
    {
        // Delete dari expired
        $getExpired = DB::select("SELECT * FROM expired_detail WHERE sku_id='{$id}'");

        foreach ($getExpired as $expired) {
            $expiredID = $expired->expired_id;

            DB::statement("DELETE FROM expired_detail WHERE expired_id='{$expiredID}' AND sku_id='{$id}'");

            // Cek apakah isi dari expired data per tanggal itu masih ada atau tidak
            $checkExpired = DB::select("SELECT * FROM expired INNER JOIN expired_detail ON expired.expired_id = expired_detail.expired_id WHERE expired.expired_id='{$expiredID}' AND expired_detail.sku_id='{$id}'");

            // Jika true, hapus expired id tersebut
            if (empty($checkExpired)) {
                DB::statement("DELETE FROM expired WHERE expired.expired_id='{$expiredID}'");
            }
        }

        // Delete export
        DB::statement("DELETE FROM export WHERE sku_id='{$id}'");
        // Delete stock
        DB::statement("DELETE FROM stock WHERE sku_id='{$id}'");

        // delete product
        DB::statement("DELETE FROM product WHERE sku_id='{$id}'");

        return redirect()->route('home.master')->with('messages', 'Produk Ini Berhasil Dihapus!');
    }
}
