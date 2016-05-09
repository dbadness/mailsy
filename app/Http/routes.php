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

Route::get('/', 'IndexController@showIndex');
Route::get('/faq', 'IndexController@showFaq');
Route::get('/signup/{license?}/{domain?}','IndexController@showSignup');
Route::post('/signup/{license?}','IndexController@doSignup');
Route::get('/login','IndexController@showLogin');
Route::post('/login','IndexController@doLogin');

// auth stuff
Route::get('/auth/{signup?}/{license?}', 'IndexController@doAuth'); // signup and license are boolean values, 0 and 1
Route::get('/gmail/{license?}', 'IndexController@doAddGmailUser');
Route::get('/logout', function(){
	Auth::logout();
	return redirect('/');
});

// pages
Route::get('/home', 'PagesController@showHome');
Route::get('/track/{e_user_id}/{e_message_id}', 'ActionController@doTrack'); // processes a read receipt when a recipient opens an email
Route::get('/smtp-setup','PagesController@showSmtpSetup');
Route::get('/tutorial/step1', 'PagesController@showTutorial1');
Route::get('/tutorial/step2', 'PagesController@showTutorial2');
Route::get('/tutorial/step3', 'PagesController@showTutorial3');
Route::get('/create', 'PagesController@showNewEmail');
Route::get('/edit/{eid}/{withData?}','PagesController@showEdit');
Route::get('/preview/{eid}','PagesController@showPreview');
Route::get('/email/{eid}','PagesController@showEmail');
Route::get('/settings','PagesController@showSettings');
Route::get('/upgrade', 'PagesController@showUpgrade');
Route::get('/upgrade/createTeam', 'PagesController@showCreateTeam');
Route::get('/membership/cancel', 'PagesController@showCancel');
Route::get('/use/{eid}', 'PagesController@showUseEmail');
Route::get('/join/{customer}','IndexController@showCompanyPage');
Route::get('/archives','PagesController@showArchive');
Route::get('/copy/{id}','PagesController@showCopy');
Route::get('/view/{id}','PagesController@showView');
Route::get('/admin','PagesController@showAdmin');

// testing
Route::get('/smtp-tester','IndexController@showSmtpTester');

// actions
Route::post('/smtp-tester','ActionController@doSmtpTester');
Route::post('/smtp-save', 'ActionController@doSmtpSave');
Route::post('/returnFields', 'ActionController@returnFields');
Route::post('/createTemplate', 'ActionController@createTemplate');
Route::post('/makePreviews', 'ActionController@makePreviews');
Route::post('/updatePreviews', 'ActionController@updatePreviews');
Route::post('/upgrade', 'ActionController@doUpgrade');
Route::post('/createTeam', 'ActionController@doTeamUpgrade');
Route::post('/useLicense','ActionController@doRedeemLicense');
Route::post('/saveTemplate','ActionController@saveTemplate');
Route::post('/copyTemplate','ActionController@copyTemplate');
Route::post('/sendFeedback','ActionController@doSendFeedback');
Route::post('/revokeAccess','ActionController@doRevokeAccess');
Route::post('/updateSubscription/{direction}','ActionController@doUpdateSubscription');
Route::get('/archive/{eid}','ActionController@doArchiveTemplate');
Route::get('/dearchive/{eid}','ActionController@doDearchiveTemplate');
Route::get('/hubify/{id}/{status}','ActionController@doHubifyTemplate');

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

//API
