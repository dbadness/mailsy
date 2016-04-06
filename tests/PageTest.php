<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPages()
    {
    	//Test that all pages return 200
        $this->visit('/')->assertResponseOk();

        $this->visit('/tutorial/step1')->assertResponseOk();

        $this->visit('/tutorial/step2')->assertResponseOk();

        $this->visit('/tutorial/step3')->assertResponseOk();

        $this->visit('/create')->assertResponseOk();

        $this->visit('/home')->assertResponseOk();

        $this->visit('/settings')->assertResponseOk();

        $this->visit('/upgrade')->assertResponseOk();

        $this->visit('/upgrade/createTeam')->assertResponseOk();

        $this->visit('/membership/cancel')->assertResponseOk();

        $this->visit('/archives')->assertResponseOk();

        $this->visit('/templatehub')->assertResponseOk();

    	//Test that all pages return 200 with data
        // $this->visit('/track/{e_user_id}/{e_message_id}')->assertResponseOk();

        // $this->visit('/edit/{eid}/{withData?}')->assertResponseOk();

        // $this->visit('/preview/{eid}')->assertResponseOk();

        // $this->visit('/email/{eid}')->assertResponseOk();

        // $this->visit('/use/{eid}')->assertResponseOk();

        // $this->visit('/team/{customer}')->assertResponseOk();

        // $this->visit('/copy/{id}')->assertResponseOk();

        // $this->visit('/view/{id}')->assertResponseOk();

		//Test that auth blocks things
        // $this->visit('/tutorial/step1')->assertRedirectedTo('/');

        // $this->visit('/tutorial/step2')->assertRedirectedTo('/');

        // $this->visit('/tutorial/step3')->assertRedirectedTo('/');

        // $this->visit('/create')->assertRedirectedTo('/');

        // $this->visit('/home')->assertRedirectedTo('/');

        // $this->visit('/settings')->assertRedirectedTo('/');

        // $this->visit('/upgrade')->assertRedirectedTo('/');

        // $this->visit('/upgrade/createTeam')->assertRedirectedTo('/');

        // $this->visit('/membership/cancel')->assertRedirectedTo('/');

        // $this->visit('/archives')->assertRedirectedTo('/');

        // $this->visit('/templatehub')->assertRedirectedTo('/');

    	//Data break
        // $this->visit('/track/{e_user_id}/{e_message_id}')->assertRedirectedTo('/');

        // $this->visit('/edit/{eid}/{withData?}')->assertRedirectedTo('/');

        // $this->visit('/preview/{eid}')->assertRedirectedTo('/');

        // $this->visit('/email/{eid}')->assertRedirectedTo('/');

        // $this->visit('/use/MQ==')->assertRedirectedTo('/');

        // $this->visit('/team/{customer}')->assertRedirectedTo('/');

        // $this->visit('/copy/MQ==')->assertRedirectedTo('/');

        // $this->visit('/view/MQ==')->assertRedirectedTo('/');

    }
}
