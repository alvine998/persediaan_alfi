<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_history_kurang', function (Blueprint $table) {
            $table->id('product_history_kurang_id');
            $table->bigInteger('history_id');
            $table->bigInteger('reset');
            $table->date('tanggal');
            $table->string('penginput', 200);
            $table->string('no_transaksi', 200);
            $table->string('nm_pembeli', 200);
            $table->text('alamat');
            $table->string('kota_kab', 200);
            $table->string('provinsi', 200);
            $table->string('no_hp', 200);
            $table->string('sku_id', 200);
            $table->string('nm_barang', 200)->default('-');
            $table->string('kategori', 200)->default('-');
            $table->string('size', 200)->default('-');
            $table->double('qty');
            $table->string('toko', 200);
            $table->string('status', 200);
            $table->text('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_history_kurang');
    }
};
