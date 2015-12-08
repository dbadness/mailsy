<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('google_message_id');
            $table->integer('user_id');
            $table->integer('email_id');
            $table->string('recipient');
            $table->string('subject');
            $table->text('message');
            $table->integer('created_at');
            $table->integer('sent_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
            $table->string('status')->nullable();
            $table->string('send_to_salesforce')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop the table
        Schema::drop('messages');
    }
}
