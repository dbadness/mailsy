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

// auth stuff
Route::get('/signup', 'IndexController@showSignup');
Route::post('/auth', 'IndexController@sendToGoogleAuth');
Route::get('/gmail', 'IndexController@doAddUser');
Route::get('/login', 'IndexController@showLogin');
Route::get('/logout', function(){
	Auth::logout();
	return redirect('/');
});

// pages
Route::get('/home', 'PagesController@showHome');
Route::get('/create', 'PagesController@showNewEmail');
Route::get('/edit/{eid}','PagesController@showEdit');
Route::get('/preview/{eid}','PagesController@showPreview');
Route::get('/email/{eid}','PagesController@showEmail');
Route::get('/settings','PagesController@showSettings');
Route::get('/upgrade', 'PagesController@showUpgrade');
Route::get('/use/{eid}', 'PagesController@showUseEmail');

// actions
Route::post('/returnFields', 'ActionController@returnFields');
Route::post('/makePreviews', 'ActionController@makePreviews');
Route::post('/updatePreviews', 'ActionController@updatePreviews');
Route::post('/sendEmails', 'ActionController@sendEmails');
Route::post('/saveSettings', 'ActionController@saveSettings');
Route::post('/upgrade', 'ActionController@doUpgrade');
Route::post('/saveTemplate','ActionController@saveTemplate');
Route::get('/getMessageStatus/{id}','ActionController@doUpdateMessageStatus'); // ajax call

// webhooks
Route::post('/payment/chargeSucceeded','APIController@doChargeSucceeded');
Route::post('/payment/chargeFailed','APIController@doChargeFailed');
