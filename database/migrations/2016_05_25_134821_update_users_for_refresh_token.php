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
            $table->string('track_links');
            $table->integer('second_last_login');
        });
        Schema::table('emails', function ($table) {
            $table->integer('one_off')->nullable();
        });
        Schema::table('messages', function ($table) {
            $table->string('files');
        });
        Schema::create('events', function ($table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('message_id');
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
                'refresh_token',
                'track_links',
                'second_last_login'
                ]);
        });
        Schema::table('emails', function ($table)
        {
            $table->dropColumn(['one_off',
                ]);
        });
        Schema::table('messages', function ($table)
        {
            $table->dropColumn(['files',
                ]);
        });

        Schema::drop('events');

    }
}
