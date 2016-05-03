<?php
/**
 * Users admin model config
 */
return array(
	'title' => 'User',
	'single' => 'user',
	'model' => 'App\User',
	/**
	 * The display columns
	 */
	'columns' => array(
		'id',
		// 'belongs_to',
		// 'has_users',
		'email',
		// 'stripe_id',
		// 'status',
		// 'name',
		// 'gmail_token',
		// 'sf_address',
		'paid',
		// 'signature',
		// 'created_at',
		// 'updated_at',
		// 'deleted_at',
		// 'expires',
		// 'tutorial_email',
		// 'saw_tutorial_one',
		// 'saw_tutorial_two',
		// 'saw_tutorial_three',
		// 'referer',
		// 'track_email',
		'admin',
		// 'timezone',
		'last_login',
	),

	/**
	 * The editable fields
	 */
	'edit_fields' => array(
		'id' => array(
			'title' => 'id',
			'type' => 'number',
		),
		'belongs_to' => array(
			'title' => 'belongs_to',
			'type' => 'text',
		),
		'has_users' => array(
			'title' => 'has_users',
			'type' => 'text',
		),
		'email' => array(
			'title' => 'email',
			'type' => 'text',
		),
		'stripe_id' => array(
			'title' => 'stripe_id',
			'type' => 'text',
		),
		// 'status' => array(
		// 	'title' => 'status',
		// 	'type' => 'text',
		// ),
		'gmail_token' => array(
			'title' => 'gmail_token',
			'type' => 'text',
		),
		'sf_address' => array(
			'title' => 'sf_address',
			'type' => 'text',
		),
		'paid' => array(
			'title' => 'paid',
			'type' => 'text',
		),
		'signature' => array(
			'title' => 'signature',
			'type' => 'text',
		),
		'created_at' => array(
			'title' => 'created_at',
			'type' => 'text',
		),
		'updated_at' => array(
			'title' => 'updated_at',
			'type' => 'text',
		),
		'deleted_at' => array(
			'title' => 'deleted_at',
			'type' => 'text',
		),
		'expires' => array(
			'title' => 'expires',
			'type' => 'text',
		),
		'tutorial_email' => array(
			'title' => 'tutorial_email',
			'type' => 'text',
		),
		'saw_tutorial_one' => array(
			'title' => 'saw_tutorial_one',
			'type' => 'text',
		),
		'saw_tutorial_two' => array(
			'title' => 'saw_tutorial_two',
			'type' => 'text',
		),
		'saw_tutorial_three' => array(
			'title' => 'saw_tutorial_three',
			'type' => 'text',
		),
		'referer' => array(
			'title' => 'referer',
			'type' => 'text',
		),
		'track_email' => array(
			'title' => 'track_email',
			'type' => 'text',
		),
		'admin' => array(
			'title' => 'admin',
			'type' => 'text',
		),
		'timezone' => array(
			'title' => 'timezone',
			'type' => 'text',
		),
		'last_login' => array(
			'title' => 'last_login',
			'type' => 'text',
		),
		'team_admin' => array(
			'title' => 'team_admin',
			'type' => 'number',
		),
		'belongs_to_team' => array(
			'title' => 'belongs_to_team',
			'type' => 'number',
		),
	),
);