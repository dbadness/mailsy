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
Route::get('/auth', 'IndexController@showSignup');
Route::post('/auth', 'IndexController@sendToGoogleAuth');
Route::get('/signup', 'IndexController@doAddUser');
Route::get('/logout', function(){
	Auth::logout();
	return redirect('/');
});

// pages
Route::get('/home', 'PagesController@showHome');
Route::get('/newEmail', 'PagesController@showNewEmail');

// ajax actions
Route::post('/addContacts', 'ActionController@returnFields');
