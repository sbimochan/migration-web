<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryAttributePostPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'category_attribute_post',
            function (Blueprint $table) {
                $table->integer('category_attribute_id')->unsigned()->index();
                $table->foreign('category_attribute_id')->references('id')->on('category_attributes')->onDelete(
                    'cascade'
                );
                $table->integer('post_id')->unsigned()->index();
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->primary(['category_attribute_id', 'post_id']);
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
        Schema::drop('category_attribute_post');
    }
}
