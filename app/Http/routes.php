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

// auth stuff
Route::get('/auth', 'IndexController@doAuth');
Route::get('/gmail', 'IndexController@doAddUser');
Route::get('/logout', function(){
	Auth::logout();
	return redirect('/');
});

// pages
Route::get('/home', 'PagesController@showHome');
Route::get('/track/{e_user_id}/{e_message_id}', 'ActionController@doTrack'); // processes a read receipt when a recipient opens an email
Route::get('/tutorial/step1', 'PagesController@showTutorial1');
Route::get('/tutorial/step2', 'PagesController@showTutorial2');
Route::get('/tutorial/step3', 'PagesController@showTutorial3');
Route::get('/create', 'PagesController@showNewEmail');
Route::get('/edit/{eid}','PagesController@showEdit');
Route::get('/preview/{eid}','PagesController@showPreview');
Route::get('/email/{eid}','PagesController@showEmail');
Route::get('/settings','PagesController@showSettings');
Route::get('/upgrade', 'PagesController@showUpgrade');
Route::get('/use/{eid}', 'PagesController@showUseEmail');
Route::get('/membership/confirm/{member}/{master?}','PagesController@showMembershipConfirm');
Route::get('/membership/add','PagesController@showAddUsers');

// actions
Route::post('/returnFields', 'ActionController@returnFields');
Route::post('/makePreviews', 'ActionController@makePreviews');
Route::post('/updatePreviews', 'ActionController@updatePreviews');
Route::post('/sendEmails', 'ActionController@sendEmails');
Route::post('/saveSettings', 'ActionController@saveSettings');
Route::post('/upgrade/{add?}', 'ActionController@doUpgrade');
Route::post('/saveTemplate','ActionController@saveTemplate');
Route::post('/sendFeedback','ActionController@doSendFeedback');

// ajax calls
Route::get('/getMessageStatus/{id}','ActionController@doUpdateMessageStatus');
Route::post('/updateCard','ActionController@doUpdateCard');
Route::post('/membership/cancel/{master?}','ActionController@doCancelMembership');
Route::get('/sendFirstEmail','ActionController@doSendFirstEmail');

// webhooks
Route::post('/payment/paid','APIController@doInvoicePaid'); // successful invoice payment
Route::post('/payment/failed','APIController@doInvoiceFailed'); // payment declined for invoice
