<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RouterTest extends TestCase
{
	/**
     * A basic test example.
     *
     * @return void
     */
	public function testExample()
	{
		$factory->define(App\User::class, function (Faker\Generator $faker)
		{
		// 	return [
		// 		'email' => $faker->email,
		// 		'name' => ,
		// 		'gmail_token' => ,
		// 		'created_at' => ,
		// 		'track_email' => 'yes',
		// 		'timezone' => 'America/New_York',
		// 		'referer' => '',

		// 		'paid' => 'yes',
		// 		'belongs_to' => ,

		// 		//admin?
		// 	];
		// });

		// factory(App\Customer::class, 5)->create();
		// factory(App\Email::class, 5)->create();
		// factory(App\Message::class, 5)->create();
		// factory(App\User::class, 5)->create();

		//post tests
		// $this->call('POST', '/returnFields', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/createTemplate', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/makePreviews', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/updatePreviews', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/saveSettings', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/upgrade', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/createTeam', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/useLicense', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/saveTemplate', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/copyTemplate', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/sendFeedback', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/revokeAccess', ['name' => 'Taylor'])->assertResponseOk();
		// $this->call('POST', '/updateSubscription/{direction}', ['name' => 'Taylor'])->assertResponseOk();

		//get tests
		// $this->visit('/archive/{id}')->assertResponseOk();
		// $this->visit('/dearchive/{id}')->assertResponseOk();
		// $this->visit('/hubify/{id}/{status}')->assertResponseOk();
		// $this->visit('/sendEmail/{email_id}/{message_id}')->assertResponseOk();

	}
}
