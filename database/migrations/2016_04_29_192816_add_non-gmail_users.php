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
            $table->string('password')->nullable();
            $table->string('smtp_server')->nullable();
            $table->string('smtp_uname')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_protocol')->nullable();
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