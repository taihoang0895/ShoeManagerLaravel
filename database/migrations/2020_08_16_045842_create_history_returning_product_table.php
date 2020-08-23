<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryReturningProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_returning_products', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger('user_id');
            $table->integer("action");
            $table->string("returning_product");
            $table->dateTime("created");
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_returning_products');
    }
}
