<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailwizzSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('mailwizz_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('subscription_id')->index()->nullable();
            $table->unsignedBigInteger('subscription_list_id')->nullable();

            $table->timestamps();
        });

        Schema::table('mailwizz_subscriptions', function ($table) {
            $table->foreign('subscription_list_id')->references('list_id')->on('mailwizz_lists');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mailwizz_subscriptions');
    }
}
