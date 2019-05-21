<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('invites');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 128);
            $table->boolean('accepted')->default(false);
            $table->integer('user_id');
            $table->integer('invitee_id');
            $table->integer('role_id');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }
}
