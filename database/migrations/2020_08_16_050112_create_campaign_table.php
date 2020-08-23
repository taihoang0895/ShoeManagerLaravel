<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger('marketing_product_id');
            $table->unsignedInteger('bank_account_id');
            $table->unsignedInteger('campaign_name_id');
            $table->integer("budget");
            $table->integer("total_comment");

            $table->foreign('marketing_product_id')->references('id')->on('marketing_products');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->foreign('campaign_name_id')->references('id')->on('campaign_names');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
