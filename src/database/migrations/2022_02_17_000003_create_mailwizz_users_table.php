<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailwizzUsersTable extends Migration
{
    public function up()
    {
        Schema::create('mailwizz_users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();

            $table->timestamps();
        });


        Schema::table('mailwizz_users', function($table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('subscription_id')->references('subscription_id')->on('mailwizz_subscriptions');
        });

    }

    public function down()
    {
        Schema::dropIfExists('mailwizz_users');
    }
}
