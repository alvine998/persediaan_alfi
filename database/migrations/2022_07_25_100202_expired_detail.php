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
        Schema::create('expired_detail', function (Blueprint $table) {
            $table->id('expired_detail_id');
            $table->bigInteger('expired_id')->unsigned();
            $table->foreign('expired_id')->references('expired_id')->on('expired');
            $table->bigInteger('stock_id')->unsigned();
            $table->foreign('stock_id')->references('stock_id')->on('stock');
            $table->string('sku_id', 40);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expired_detail');
    }
};
