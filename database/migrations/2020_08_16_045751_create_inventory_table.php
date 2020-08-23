<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger('detail_product_id');
            $table->integer("importing_quantity")->default(0);
            $table->integer("exporting_quantity")->default(0);
            $table->integer("returning_quantity")->default(0);
            $table->integer("failed_quantity")->default(0);
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
        Schema::dropIfExists('inventories');
    }
}
