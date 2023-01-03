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
        Schema::create('product_history_input', function (Blueprint $table) {
            $table->id('product_history_input_id');
            $table->bigInteger('history_id');
            $table->bigInteger('reset');
            $table->string('brand_id', 200);
            $table->string('sku_id', 200);
            $table->string('nm_barang', 200);
            $table->string('kategori', 200);
            $table->string('ukuran', 200);
            $table->string('lokasi', 200);
            $table->double('berat');
            $table->double('panjang');
            $table->double('lebar');
            $table->double('tinggi');
            $table->double('harga_modal');
            $table->double('harga_jual');
            $table->double('margin');
            $table->text('link_photo');
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
        Schema::dropIfExists('product_history_input');
    }
};
