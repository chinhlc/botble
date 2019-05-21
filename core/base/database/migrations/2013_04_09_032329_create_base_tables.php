<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBaseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revisions', function ($table) {
            $table->increments('id');
            $table->string('revisionable_type')->index();
            $table->integer('revisionable_id')->index();
            $table->integer('user_id')->unsigned()->references('id')->on('users')->index();
            $table->string('key');
            $table->text('old_value', 65535)->nullable();
            $table->text('new_value', 65535)->nullable();
            $table->timestamps();
        });

        Schema::create('meta_boxes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('content_id')->unsigned()->index();
            $table->string('meta_key', 255);
            $table->text('meta_value')->nullable();
            $table->string('reference', 120);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('plugins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('alias', 120);
            $table->string('provider', 255);
            $table->string('author', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('version', 30)->nullable();
            $table->string('description', 255)->nullable();
            $table->tinyInteger('status')->unsigined()->default(0);
            $table->timestamps();
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
        Schema::dropIfExists('revisions');
        Schema::dropIfExists('meta_boxes');
        Schema::dropIfExists('plugins');
    }
}
