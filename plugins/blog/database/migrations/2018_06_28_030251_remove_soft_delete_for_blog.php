<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSoftDeleteForBlog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('post_tag')->whereNotNull('deleted_at')->delete();

        Schema::table('post_tag', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        DB::table('post_category')->whereNotNull('deleted_at')->delete();
        Schema::table('post_category', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        DB::table('posts')->whereNotNull('deleted_at')->delete();
        Schema::table('posts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        DB::table('categories')->whereNotNull('deleted_at')->delete();
        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('post_tag', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('post_category', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}
