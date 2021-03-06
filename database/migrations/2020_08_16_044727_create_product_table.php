<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string("code")->primary();
            $table->string("name");
            $table->integer("price");
            $table->integer('storage_id')->default(1);
            $table->integer("historical_cost");
            $table->boolean("is_active")->default(true);
            $table->boolean("is_test")->default(false);
            $table->dateTime("created");
            $table->foreign('storage_id')->references('id')->on('storages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
