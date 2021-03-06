<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_orders', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->foreignId("order_id")->constrained()->onDelete("cascade");
            $table->integer("quantity");
            $table->float("kg")->default(0.5);
            $table->integer("actually_collected");
            $table->integer("pick_money");

            $table->unsignedInteger('marketing_product_id')->nullable();
            $table->unsignedInteger('discount_id')->nullable();
            $table->unsignedInteger('product_category_id')->nullable();
            $table->unsignedInteger('detail_product_id')->nullable();

            $table->foreign("discount_id")->references('id')->on('discounts');
            $table->foreign("product_category_id")->references('id')->on('product_categories');
            $table->foreign("detail_product_id")->references('id')->on('detail_products');
            $table->foreign("marketing_product_id")->references('id')->on('marketing_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_orders');
    }
}
