<?php
/**
 * Users admin model config
 */
return array(
	'title' => 'Message',
	'single' => 'message',
	'model' => 'App\Message',
	/**
	 * The display columns
	 */
	'columns' => array(
		'id',
		// 'google_message_id',
		'user_id',
		'email_id',
		'recipient',
		'subject',
		// 'message',
		// 'created_at',
		'sent_at',
		// 'updated_at',
		// 'deleted_at',
		// 'status',
		// 'send_to_salesforce',
		// 'read_at',
		// 'sent_with_csv',
	),

	/**
	 * The editable fields
	 */
	'edit_fields' => array(
		'id' => array(
			'title' => 'id',
			'type' => 'number',
		),
		'google_message_id' => array(
			'title' => 'google_message_id',
			'type' => 'text',
		),
		'user_id' => array(
			'title' => 'user_id',
			'type' => 'number',
		),
		'email_id' => array(
			'title' => 'email_id',
			'type' => 'number',
		),
		'recipient' => array(
			'title' => 'recipient',
			'type' => 'text',
		),
		'subject' => array(
			'title' => 'subject',
			'type' => 'text',
		),
		'message' => array(
			'title' => 'message',
			'type' => 'wysiwyg',
		),
		'created_at' => array(
			'title' => 'created_at',
			'type' => 'number',
		),
		'sent_at' => array(
			'title' => 'sent_at',
			'type' => 'number',
		),
		'updated_at' => array(
			'title' => 'updated_at',
			'type' => 'number',
		),
		'deleted_at' => array(
			'title' => 'deleted_at',
			'type' => 'number',
		),
		'status' => array(
			'title' => 'status',
			'type' => 'text',
		),
		'send_to_salesforce' => array(
			'title' => 'send_to_salesforce',
			'type' => 'text',
		),
		'read_at' => array(
			'title' => 'read_at',
			'type' => 'number',
		),
		'sent_with_csv' => array(
			'title' => 'sent_with_csv',
			'type' => 'text',
		),

	),
);