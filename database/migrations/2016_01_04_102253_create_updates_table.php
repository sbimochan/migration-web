<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'country_updates',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->text('description');
                $table->integer('country_id')->unsigned()->index();
                $table->foreign('country_id')->references('id')->on('country')->onDelete('cascade');
                $table->softDeletes();
                $table->timestamps();
            }
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('country_updates');
    }

}
