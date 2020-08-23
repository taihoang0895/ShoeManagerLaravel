<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_managers', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger('user_id');
            $table->boolean("has_notification")->default(false);
            $table->integer("total")->default(0);
            $table->integer("unread_count")->default(0);
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
        Schema::dropIfExists('notification_managers');
    }
}
