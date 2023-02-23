<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailwizzListsTable extends Migration
{
    public function up()
    {
            Schema::create('mailwizz_lists', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('list_id')->index()->nullable();
            $table->unsignedBigInteger('list_name')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mailwizz_lists');
    }
}
