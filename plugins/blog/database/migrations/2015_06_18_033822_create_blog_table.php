<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 120);
            $table->string('slug', 120);
            $table->integer('parent_id')->unsigned()->default(0)->index();
            $table->text('description');
            $table->tinyInteger('status')->unsigned()->default(1);
            $table->integer('user_id')->unsigned()->index()->references('id')->on('users');
            $table->string('icon', 60)->nullable();
            $table->tinyInteger('featured')->default(0);
            $table->tinyInteger('order')->default(0);
            $table->tinyInteger('is_default')->unsigned()->default(0);

            $table->softDeletes();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 120);
            $table->string('slug', 120);
            $table->integer('user_id')->unsigned()->index()->references('id')->on('users');
            $table->string('description', 400)->nullable()->default('');
            $table->integer('parent_id')->unsigned()->default(0)->index();
            $table->tinyInteger('status')->unsigned()->default(1);

            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('description', 400);
            $table->text('content');
            $table->tinyInteger('status')->unsigned()->default(1);
            $table->integer('user_id')->references('id')->on('users');
            $table->tinyInteger('featured')->unsigned()->default(0);
            $table->string('image', 255)->nullable();
            $table->integer('views')->unsigned()->default(0);
            $table->string('format_type', 30)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tag_id')->unsigned()->index()->references('id')->on('tags')->onDelete('cascade');
            $table->integer('post_id')->unsigned()->index()->references('id')->on('posts')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('post_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->index()->references('id')->on('categories')->onDelete('cascade');
            $table->integer('post_id')->unsigned()->index()->references('id')->on('posts')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('post_category');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('tags');
    }
}
