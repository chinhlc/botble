<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFeaturesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permission_flags', function (Blueprint $table) {
            $table->dropColumn(['is_feature', 'feature_visible']);
        });

        Schema::dropIfExists('features');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permission_flags', function (Blueprint $table) {
            $table->integer('is_feature')->default(0);
            $table->integer('feature_visible')->default(1);
        });

        Schema::create('features', function ($table) {
            $table->increments('id');
            $table->integer('feature_id')->references('id')->on('permission_flags')->unsigned()->index();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }
}
