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
        Schema::create('stock', function (Blueprint $table) {
            $table->id('stock_id');
            $table->date('tanggal_transaksi');
            $table->string('no_transaksi', 200);
            $table->string('penginput', 200);
            $table->string('sku_id', 40);
            $table->double('qty');
            $table->date('tanggal_exp');
            $table->bigInteger('is_expired')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock');
    }
};
