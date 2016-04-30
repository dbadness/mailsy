<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNonGmailUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('password');
            $table->string('smtp_server');
            $table->string('smtp_uname');
            $table->string('smtp_port');
            $table->string('smtp_protocol');
            $table->text('gmail_token')->nullable()->change();
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
            $table->dropColumn(['password', 'smtp_server', 'smtp_uname', 'smtp_port', 'smtp_protocol']);
        });
    }
}