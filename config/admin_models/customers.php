<?php
/**
 * Users admin model config
 */
return array(
	'title' => 'Customer',
	'single' => 'customer',
	'model' => 'App\Customer',
	/**
	 * The display columns
	 */
	'columns' => array(
		'id',
		'owner_id',
		'company_name',
		'domain',
		'total_users',
		'users_left',
		'created_at',
		'deleted_at',
	),

	/**
	 * The editable fields
	 */
	'edit_fields' => array(
		'id' => array(
			'title' => 'id',
			'type' => 'number',
		),
		'id' => array(
			'title' => 'owner_id',
			'type' => 'number',
		),
		'id' => array(
			'title' => 'company_name',
			'type' => 'text',
		),
		'id' => array(
			'title' => 'domain',
			'type' => 'text',
		),
		'id' => array(
			'title' => 'total_users',
			'type' => 'number',
		),
		'id' => array(
			'title' => 'users_left',
			'type' => 'number',
		),
		'id' => array(
			'title' => 'created_at',
			'type' => 'number',
		),
		'id' => array(
			'title' => 'deleted_at',
			'type' => 'number',
		),

	),
);