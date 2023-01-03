<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\History;
use App\Http\Controllers\Home;
use App\Http\Controllers\Notification;
use App\Http\Controllers\Sync;
use App\Http\Controllers\Expired;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Start Program
// Home
Route::get('/', [Home::class, 'index']);
Route::get('/', [Home::class, 'index'])->name('home');
Route::get('/', [Home::class, 'index'])->name('home');
Route::get('/home/tambah-stok/{id}', [Home::class, 'tambahstok'])->name('home.tambah-stok');
Route::get('/home/kurang-stok/{id}', [Home::class, 'kurangstok'])->name('home.kurang-stok');
Route::post('/home/proses-tambah-stok/{id}', [Home::class, 'prosestambahstok'])->name('home.proses-tambah-stok');
Route::post('/home/proses-kurang-stok/{id}', [Home::class, 'proseskurangstok'])->name('home.proses-kurang-stok');
Route::get('/home/cetak-qrcode/{id}', [Home::class, 'cetakqrcode'])->name('home.cetak-qrcode');
Route::get('/master', [Home::class, 'master'])->name('home.master');

// Notification
Route::get('/notification', [Notification::class, 'index'])->name('notification');

// History
Route::get('/history', [History::class, 'index'])->name('history');

// Authentification
Route::get('/auth', [Auth::class, 'index'])->name('auth');
Route::get('/auth/logout', [Auth::class, 'logout'])->name('auth.logout');
Route::post('/', [Auth::class, 'login'])->name('auth.login');

// Sync
Route::get('/sync', [Sync::class, 'index'])->name('sync');
Route::get('/sync/product-export', [Sync::class, 'export'])->name('sync.product.export');
Route::get('/sync/product-template-export/{id}', [Sync::class, 'templateexport'])->name('sync.product.template-export');
Route::get('/sync/product-template-expired-export/{id}', [Sync::class, 'templateexpiredexport'])->name('sync.product.template-expired-export');


Route::get('/sync/product-export-stok', [Sync::class, 'exportstok'])->name('sync.product.export-stok');
Route::get('/sync/product-export-stoks', [Sync::class, 'exportstoks'])->name('sync.product.export-stoks');
Route::get('/sync/product-export-barcode', [Sync::class, 'exportbarcode'])->name('sync.product.export-barcode');
Route::get('/sync/{sub}', [Sync::class, 'index'])->name('sync-sub');

Route::get('/sync/delete-product/{id}', [Sync::class, 'deleteproduct'])->name("delete-product");

Route::post('/sync/product-import', [Sync::class, 'import'])->name('sync.product.import');
Route::post('/sync/product-tambah-stok-massal', [Sync::class, 'importtambahprodukmassal'])->name('sync.product.tambah-stok-massal');
Route::post('/sync/product-kurang-stok-massal', [Sync::class, 'importkurangprodukmassal'])->name('sync.product.kurang-stok-massal');

// Disable Notification
Route::post('/expired/disable-notification', [Expired::class, 'index'])->name('disable-notification');

// End Program
