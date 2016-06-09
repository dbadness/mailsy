<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersForRefreshToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('access_token');
            $table->string('token_type');
            $table->string('expires_in');
            $table->string('id_token');
            $table->string('created');
            $table->string('refresh_token');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'read_at'
                ]);
        });
        Schema::create('events', function ($table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('event_type');
            $table->string('event_message');
            $table->integer('timestamp');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table)
        {
            $table->dropColumn(['access_token',
                'token_type',
                'expires_in',
                'id_token',
                'created',
                'refresh_token'
                ]);
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->integer('read_at')->nullable();
        });
        Schema::drop('events');

    }
}
