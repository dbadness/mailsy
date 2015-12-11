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
            $table->string('subscription_id')->nullable();
            $table->string('name')->nullable();
            $table->rememberToken();
            $table->string('gmail_token');
            $table->string('sf_address');
            $table->string('paid')->nullable();
            $table->text('signature');
            $table->integer('created_at');
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
            $table->integer('expires')->nullable();
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
