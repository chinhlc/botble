<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSoftDeleteForMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('menu_nodes')->whereNotNull('deleted_at')->delete();
        Schema::table('menu_nodes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        DB::table('menus')->whereNotNull('deleted_at')->delete();

        Schema::table('menus', function (Blueprint $table) {
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
        Schema::table('menus', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('menu_nodes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}
