<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketingProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_products', function (Blueprint $table) {
            $table->increments("id");
            $table->string("code")->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('marketing_source_id');
            $table->string('product_code');

            $table->integer("total_comment")->default(0);
            $table->integer("total_budget")->default(0);
            $table->dateTime("created");
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('marketing_source_id')->references('id')->on('marketing_sources');
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
        Schema::dropIfExists('marketing_products');
    }
}
