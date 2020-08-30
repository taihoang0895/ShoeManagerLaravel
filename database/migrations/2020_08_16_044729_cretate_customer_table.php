<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CretateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string('code');
            $table->string('name');
            $table->string('address')->nullable();
            $table->integer('customer_state');
            $table->unsignedInteger('street_id')->nullable();
            $table->unsignedInteger('landing_page_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->string("phone_number", 24);
            $table->date("birthday")->nullable();
            $table->boolean("public_phone_number");
            $table->dateTime("created");

            $table->foreign('landing_page_id')->references('id')->on('landing_pages');
            $table->foreign('street_id')->references('id')->on('streets');
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
        Schema::dropIfExists('customers');
    }
}
