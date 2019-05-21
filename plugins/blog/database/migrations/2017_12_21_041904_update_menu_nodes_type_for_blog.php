<?php

use Illuminate\Database\Migrations\Migration;

class UpdateMenuNodesTypeForBlog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('menu_nodes')->where('type', 'categories')->update(['type' => 'category']);
        DB::table('menu_nodes')->where('type', 'tags')->update(['type' => 'tag']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('menu_nodes')->where('type', 'category')->update(['type' => 'categories']);
        DB::table('menu_nodes')->where('type', 'tag')->update(['type' => 'tags']);
    }
}
