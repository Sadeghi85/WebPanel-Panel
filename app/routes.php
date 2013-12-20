<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});


Route::group(array('before' => 'auth.sentry.root'), function()
{
    //Route::resource('account', 'AccountController', array('except' => array('show')));
	
	Route::resource('groups', 'GroupsController', array());
	Route::resource('users', 'UsersController', array());
	Route::resource('logs', 'LogsController', array('except' => array('update')));
});

Route::group(array('before' => 'auth.sentry'), function()
{
    //Route::resource('account', 'AccountController', array('except' => array('show')));
	
	Route::resource('groups', 'AccountController', array('except' => array('show')));
	Route::resource('account', 'AccountController', array('except' => array('show')));
});


/*
|--------------------------------------------------------------------------
| Authentication and Authorization Routes
|--------------------------------------------------------------------------
|
|
|
*/

Route::group(array('prefix' => 'auth'), function()
{

	# Login
	Route::get('login', array('as' => 'login', 'uses' => 'AuthController@getLogin'));
	Route::post('login', array('before' => 'csrf', 'uses' => 'AuthController@postLogin'));

	# Register
	// Route::get('signup', array('as' => 'signup', 'uses' => 'AuthController@getSignup'));
	// Route::post('signup', 'AuthController@postSignup');

	# Account Activation
	// Route::get('activate/{activationCode}', array('as' => 'activate', 'uses' => 'AuthController@getActivate'));

	# Forgot Password
	// Route::get('forgot-password', array('as' => 'forgot-password', 'uses' => 'AuthController@getForgotPassword'));
	// Route::post('forgot-password', 'AuthController@postForgotPassword');

	# Forgot Password Confirmation
	// Route::get('forgot-password/{passwordResetCode}', array('as' => 'forgot-password-confirm', 'uses' => 'AuthController@getForgotPasswordConfirm'));
	// Route::post('forgot-password/{passwordResetCode}', 'AuthController@postForgotPasswordConfirm');

	# Logout
	Route::get('logout', array('as' => 'logout', 'uses' => 'AuthController@getLogout'));

});