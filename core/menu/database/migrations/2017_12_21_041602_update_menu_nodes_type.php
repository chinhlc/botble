<?php

use Illuminate\Database\Migrations\Migration;

class UpdateMenuNodesType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('menu_nodes')->where('type', 'pages')->update(['type' => 'page']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('menu_nodes')->where('type', 'page')->update(['type' => 'pages']);
    }
}
