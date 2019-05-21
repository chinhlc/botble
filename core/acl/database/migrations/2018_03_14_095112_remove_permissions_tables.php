<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePermissionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('permission_flags');
        Schema::dropIfExists('role_flags');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('permission_flags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('flag', 100)->unique();
            $table->string('name', 100);
            $table->integer('parent_flag')->default(0);
            $table->integer('permission_visible')->default(1);

            $table->engine = 'InnoDB';
            $table->timestamps();
        });

        Schema::create('role_flags', function ($table) {
            $table->increments('id');
            $table->integer('role_id')->references('id')->on('roles')->unsigned()->index();
            $table->integer('flag_id')->unsigned()->references('id')->on('permission_flags')->index();

            $table->engine = 'InnoDB';
        });
    }
}
