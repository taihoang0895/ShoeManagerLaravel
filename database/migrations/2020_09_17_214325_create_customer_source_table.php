<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_sources', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedBigInteger('customer_id');
            $table->unsignedInteger('marketing_product_id')->nullable();
            $table->string('product_code');

            $table->dateTime('created');

            $table->foreign("customer_id")->references('id')->on('customers');
            $table->foreign('marketing_product_id')->references('id')->on('marketing_products');
            $table->foreign('product_code')->references('code')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_sources');
    }
}
