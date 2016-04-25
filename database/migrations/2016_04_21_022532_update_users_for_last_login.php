<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersForLastLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->integer('last_login');

        });
        Schema::table('emails', function ($table) {
            $table->integer('shared'); //0 = no, 1 = cust_published, 2 = team_published 3 = public

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn(['last_login'
                ]);
        });
        Schema::table('emails', function ($table) {
            $table->dropColumn(['shared'
                ]);
        });
    }
}
