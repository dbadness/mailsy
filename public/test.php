<?php
$test = array (
  'created' => 1326853478,
  'livemode' => false,
  'id' => 'evt_00000000000000',
  'type' => 'charge.succeeded',
  'object' => 'event',
  'request' => NULL,
  'pending_webhooks' => 1,
  'api_version' => '2015-10-16',
  'data' => 
  array (
    'object' => 
    array (
      'id' => 'ch_00000000000000',
      'object' => 'charge',
      'amount' => 1000,
      'amount_refunded' => 0,
      'application_fee' => NULL,
      'balance_transaction' => 'txn_00000000000000',
      'captured' => true,
      'created' => 1449787313,
      'currency' => 'usd',
      'customer' => 'cus_00000000000000',
      'description' => NULL,
      'destination' => NULL,
      'dispute' => NULL,
      'failure_code' => NULL,
      'failure_message' => NULL,
      'fraud_details' => 
      array (
      ),
      'invoice' => 'in_00000000000000',
      'livemode' => false,
      'metadata' => 
      array (
      ),
      'paid' => true,
      'receipt_email' => NULL,
      'receipt_number' => NULL,
      'refunded' => false,
      'refunds' => 
      array (
        'object' => 'list',
        'data' => 
        array (
        ),
        'has_more' => false,
        'total_count' => 0,
        'url' => '/v1/charges/ch_17Gd6jKTSrWuuVfd8OqZPMCp/refunds',
      ),
      'shipping' => NULL,
      'source' => 
      array (
        'id' => 'card_00000000000000',
        'object' => 'card',
        'address_city' => NULL,
        'address_country' => NULL,
        'address_line1' => NULL,
        'address_line1_check' => NULL,
        'address_line2' => NULL,
        'address_state' => NULL,
        'address_zip' => NULL,
        'address_zip_check' => NULL,
        'brand' => 'Visa',
        'country' => 'US',
        'customer' => 'cus_00000000000000',
        'cvc_check' => 'pass',
        'dynamic_last4' => NULL,
        'exp_month' => 2,
        'exp_year' => 2016,
        'funding' => 'credit',
        'last4' => '4242',
        'metadata' => 
        array (
        ),
        'name' => 'dave@mailsy.co',
        'tokenization_method' => NULL,
      ),
      'statement_descriptor' => NULL,
      'status' => 'succeeded',
    ),
  ),
);

echo $test['data']['object']['id'];

?>