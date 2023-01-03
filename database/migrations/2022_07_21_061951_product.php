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
        Schema::create('product', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('brand_id', 200);
            $table->string('sku_id', 40)->unique();
            $table->string('nm_barang', 200);
            $table->string('kategori', 200);
            $table->string('ukuran', 200);
            $table->string('lokasi', 200);
            $table->string('berat', 200);
            $table->string('panjang', 200);
            $table->string('lebar', 200);
            $table->string('tinggi', 200);
            $table->double('harga_modal');
            $table->double('harga_jual');
            $table->double('margin');
            $table->text('link_photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
};
