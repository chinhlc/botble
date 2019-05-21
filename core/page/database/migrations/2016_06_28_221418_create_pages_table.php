<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 120);
            $table->string('slug', 120);
            $table->text('content');
            $table->tinyInteger('status')->unsigned()->default(1);
            $table->integer('user_id')->references('id')->on('users');
            $table->string('image', 255)->nullable();
            $table->string('template', 60)->nullable();
            $table->integer('parent_id')->unsigned()->index()->default(0);
            $table->string('icon', 60)->nullable();
            $table->tinyInteger('featured')->default(0);
            $table->tinyInteger('order')->default(0)->nullable();
            $table->string('description', 400)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
