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
        Schema::create('product_history_tambah', function (Blueprint $table) {
            $table->id('product_history_tambah_id');
            $table->bigInteger('history_id');
            $table->bigInteger('reset');
            $table->date('tanggal_transaksi');
            $table->string('no_transaksi', 200);
            $table->string('penginput', 200);
            $table->string('sku_id', 40);
            $table->double('qty');
            $table->date('tanggal_exp');
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
        Schema::dropIfExists('product_history_tambah');
    }
};
