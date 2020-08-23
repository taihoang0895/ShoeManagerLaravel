<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailedProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_products', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('detail_product_id');
            $table->integer("quantity")->default(0);
            $table->string("note");
            $table->dateTime("created");
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('detail_product_id')->references('id')->on('detail_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('failed_products');
    }
}
