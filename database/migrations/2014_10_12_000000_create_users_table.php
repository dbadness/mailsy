<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('belongs_to')->nullable();
            $table->string('has_users')->nullable();
            $table->string('email')->unique();
            $table->string('stripe_id')->nullable();
            $table->string('status')->nullable();
            $table->string('name')->nullable();
            $table->rememberToken();
            $table->text('gmail_token');
            $table->string('sf_address');
            $table->string('paid')->nullable();
            $table->text('signature');
            $table->integer('created_at');
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
            $table->integer('expires')->nullable();
            $table->string('tutorial_email',3)->nullable();
            $table->string('saw_tutorial_one',3)->nullable();
            $table->string('saw_tutorial_two',3)->nullable();
            $table->string('saw_tutorial_three',3)->nullable();
            $table->string('referer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
