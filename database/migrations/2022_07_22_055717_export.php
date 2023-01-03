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
        Schema::create('export', function (Blueprint $table) {
            $table->id('export_id');
            $table->date('tanggal')->default('1970-01-01');
            $table->string('penginput', 200)->default('');
            $table->string('no_transaksi', 200)->default('');
            $table->string('nm_pembeli', 200)->default('');
            $table->text('alamat')->default('');
            $table->string('kota_kab', 200)->default('');
            $table->string('provinsi', 200)->default('');
            $table->string('no_hp', 200)->default('');
            $table->string('sku_id', 200)->default('');
            $table->string('nm_barang', 200)->default('');
            $table->string('kategori', 200)->default('');
            $table->string('size', 200)->default('');
            $table->double('qty')->default(0);
            $table->string('toko', 200)->default('');
            $table->text('keterangan')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('export');
    }
};
