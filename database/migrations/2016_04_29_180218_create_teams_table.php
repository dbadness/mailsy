<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->integer('created_at');
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
        });

        Schema::create('customer_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('team_id');
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id');
            $table->integer('user_id');
            $table->integer('admin')->nullable();
        });

        Schema::table('users', function ($table) {
            $table->integer('has_teams');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teams');
        Schema::drop('customer_user');
        Schema::drop('team_user');

        Schema::table('users', function ($table) {
            $table->dropColumn(['has_teams'
                ]);
        });
    }
}
