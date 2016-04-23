<?php
/**
 * Users admin model config
 */
return array(
	'title' => 'Email',
	'single' => 'email',
	'model' => 'App\Email',
	/**
	 * The display columns
	 */
	'columns' => array(
		'id',
		'user_id',
		'name',
		'subject',
		// 'template',
		// 'temp_recipients_list',
		// 'fields',
		// 'created_at',
		// 'updated_at',
		// 'deleted_at',
	),

	/**
	 * The editable fields
	 */
	'edit_fields' => array(
		'id' => array(
			'title' => 'id',
			'type' => 'number',
		),
		'user_id' => array(
			'title' => 'user_id',
			'type' => 'number',
		),
		'name' => array(
			'title' => 'name',
			'type' => 'text',
		),
		'subject' => array(
			'title' => 'subject',
			'type' => 'text',
		),
		'template' => array(
			'title' => 'template',
			'type' => 'wysiwyg',
		),
		'temp_recipients_list' => array(
			'title' => 'temp_recipients_list',
			'type' => 'text',
		),
		'fields' => array(
			'title' => 'fields',
			'type' => 'text',
		),
		'created_at' => array(
			'title' => 'created_at',
			'type' => 'number',
		),
		'updated_at' => array(
			'title' => 'updated_at',
			'type' => 'text',
		),
		'deleted_at' => array(
			'title' => 'deleted_at',
			'type' => 'text',
		),

	),
);