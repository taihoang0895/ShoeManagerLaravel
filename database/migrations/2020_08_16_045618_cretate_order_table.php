<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CretateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("code");
            $table->unsignedBigInteger('customer_id');
            $table->integer("order_state");
            $table->unsignedInteger('order_fail_reason_id');
            $table->unsignedInteger('user_id');
            $table->integer("replace_order_id")->nullable();
            $table->string("note");
            $table->boolean("is_test");
            $table->dateTime("delivery_time")->nullable();
            $table->dateTime("created");

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('order_fail_reason_id')->references('id')->on('order_fail_reasons');
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
        Schema::dropIfExists('orders');
    }
}
