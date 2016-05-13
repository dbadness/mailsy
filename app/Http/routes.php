<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::group(['middleware' => 'notAuth'], function(){
	Route::get('/', ['as' => 'index', 'uses' => 'IndexController@showIndex']);
	Route::get('/faq', ['as' => 'faq', 'uses' => 'IndexController@showFaq']);
	Route::get('/signup/{license?}/{domain?}', ['as' => 'signup', 'uses' => 'IndexController@showSignup']);
	Route::post('/signup/{license?}', ['as' => 'signupPost', 'uses' => 'IndexController@doSignup']);
	Route::get('/login', ['as' => 'login', 'uses' => 'IndexController@showLogin']);
	Route::post('/login', ['as' => 'loginPost', 'uses' => 'IndexController@doLogin']);
// });

// auth stuff
Route::get('/auth/{signup?}/{license?}', ['as' => 'Auth', 'uses' => 'IndexController@doAuth']); // signup and license are boolean values, 0 and 1
Route::get('/gmail/{license?}', ['as' => 'index', 'uses' => 'IndexController@doAddGmailUser']);
Route::get('/logout', function(){
	Auth::logout();
	return redirect('/');
});

// Route::group(['middleware' => 'auth'], function(){
	// pages
	Route::get('/home', ['as' => 'home', 'uses' => 'PagesController@showHome']);
	Route::get('/smtp-setup', ['as' => 'smtp-setup', 'uses' => 'PagesController@showSmtpSetup']);
	Route::get('/tutorial/step1', ['as' => 'tutorial1', 'uses' => 'PagesController@showTutorial1']);
	Route::get('/tutorial/step2', ['as' => 'tutorial2', 'uses' => 'PagesController@showTutorial2']);
	Route::get('/tutorial/step3', ['as' => 'tutorial3', 'uses' => 'PagesController@showTutorial3']);
	Route::get('/create', ['as' => 'create', 'uses' => 'PagesController@showNewEmail']);
	Route::get('/edit/{eid}/{withData?}', ['as' => 'edit', 'uses' => 'PagesController@showEdit']);
	Route::get('/preview/{eid}', ['as' => 'preview', 'uses' => 'PagesController@showPreview']);
	Route::get('/email/{eid}', ['as' => 'email', 'uses' => 'PagesController@showEmail']);
	Route::get('/settings', ['as' => 'settings', 'uses' => 'PagesController@showSettings']);
	Route::get('/upgrade', ['as' => 'upgrade', 'uses' => 'PagesController@showUpgrade']);
	Route::get('/upgrade/createTeam', ['as' => 'createTeam', 'uses' => 'PagesController@showCreateTeam']);
	Route::get('/membership/cancel', ['as' => 'cancelMembership', 'uses' => 'PagesController@showCancel']);
	Route::get('/use/{eid}', ['as' => 'use', 'uses' => 'PagesController@showUseEmail']);
	Route::get('/archives', ['as' => 'getArchive', 'uses' => 'PagesController@showArchive']);
	Route::get('/copy/{id}', ['as' => 'copy', 'uses' => 'PagesController@showCopy']);
	Route::get('/view/{id}', ['as' => 'view', 'uses' => 'PagesController@showView']);
// });

//Has special access restrictions in Administrator.php
Route::get('/admin', ['as' => 'admin', 'uses' => 'PagesController@showAdmin']);

Route::get('/join/{customer}', ['as' => 'join', 'uses' =>'IndexController@showCompanyPage']);
Route::get('/track/{e_user_id}/{e_message_id}', ['as' => 'track', 'uses' => 'ActionController@doTrack']); // processes a read receipt when a recipient opens an email

// testing
Route::get('/smtp-tester', ['as' => 'smtpTesterGet', 'uses' => 'IndexController@showSmtpTester']);

// actions
Route::post('/smtp-tester', ['as' => 'smtpTesterPost', 'uses' => 'ActionController@doSmtpTester']);
Route::post('/smtp-save', ['as' => 'smtpSave', 'uses' => 'ActionController@doSmtpSave']);
Route::post('/returnFields', ['as' => 'returnFields', 'uses' => 'ActionController@returnFields']);
Route::post('/createTemplate', ['as' => 'createTemplate', 'uses' => 'ActionController@createTemplate']);
Route::post('/makePreviews', ['as' => 'makePreviews', 'uses' => 'ActionController@makePreviews']);
Route::post('/updatePreviews', ['as' => 'updatePreviews', 'uses' => 'ActionController@updatePreviews']);
Route::post('/upgrade', ['as' => 'upgrade', 'uses' => 'ActionController@doUpgrade']);
Route::post('/createTeam', ['as' => 'createTeam', 'uses' => 'ActionController@doTeamUpgrade']);
Route::post('/useLicense', ['as' => 'useLicense', 'uses' => 'ActionController@doRedeemLicense']);
Route::post('/saveTemplate', ['as' => 'saveTemplate', 'uses' => 'ActionController@saveTemplate']);
Route::post('/copyTemplate', ['as' => 'copyTemplate', 'uses' => 'ActionController@copyTemplate']);
Route::post('/sendFeedback', ['as' => 'sendFeedback', 'uses' => 'ActionController@doSendFeedback']);
Route::post('/revokeAccess', ['as' => 'revokeAccess', 'uses' => 'ActionController@doRevokeAccess']);
Route::post('/updateSubscription/{direction}', ['as' => 'updateSubscription', 'uses' => 'ActionController@doUpdateSubscription']);
Route::get('/archive/{eid}', ['as' => 'archive', 'uses' => 'ActionController@doArchiveTemplate']);
Route::get('/dearchive/{eid}', ['as' => 'dearchive', 'uses' => 'ActionController@doDearchiveTemplate']);
Route::get('/hubify/{id}/{status}', ['as' => 'hubify', 'uses' => 'ActionController@doHubifyTemplate']);

Route::get('/makeTeam/{id}','ActionController@doMakeTeam');
Route::get('/destroyTeam/{id}','ActionController@doDestroyTeam');
Route::get('/addToTeam/{id}/{admin_id}','ActionController@doAddToTeam');
Route::get('/removeFromTeam/{id}','ActionController@doRemoveFromTeam');

// ajax calls
Route::get('/getMessageStatus/{id}','ActionController@doUpdateMessageStatus');
Route::post('/updateCard','ActionController@doUpdateCard');
Route::post('/membership/cancel','ActionController@doCancelMembership');
Route::get('/sendFirstEmail','ActionController@doSendFirstEmail');
Route::get('/getReplyRate/{email_id}','ActionController@doReturnReplyRate');
Route::get('/smtp-auth-check/{e_password}','ActionController@doSmtpAuthCheck');
Route::get('/sendEmail/{email_id}/{message_id}/{password?}', 'ActionController@sendEmail');
Route::post('/saveSettings', 'ActionController@saveSettings');

// webhooks
Route::post('/payment/paid','APIController@doInvoicePaid'); // successful invoice payment
Route::post('/payment/failed','APIController@doInvoiceFailed'); // payment declined for invoice

