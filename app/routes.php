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
	//return Redirect::route('overview.index');
	return Redirect::to(Session::get('loginRedirect', route('overview.index')));
});

Route::get('/experiment', function()
{
	$output = '';
	$error = '';
	
	// $handle = popen('sudo cat /etc/passwd 2>&1', 'r');
	
	// if (is_resource($handle))
	// {

		// $read = stream_get_contents($handle);
		// $output = $read;
		// $output = '<pre>'. $output . '</pre>';
		// $ret = pclose($handle);
		// $output .= "\n$ret";
	// }

	
	// $handle = popen(' tee /etc/sadeghi_test 2>&1', 'w');
	// if (is_resource($handle))
	// {
		// fwrite($handle, 'actual test');
		
		// $ret = pclose($handle);
		// $output .= "\n$ret";
	// }
	
	$descriptorspec = array(
	   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
	   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
	   2 => array("pipe", "w") // stderr is a pipe that the child will write to
	);

	$cwd = '/root';
	//$env = array('some_option' => 'aeiou');

	$process = proc_open('sudo tee /etc/sadeghi_test', $descriptorspec, $pipes, $cwd);

	if (is_resource($process)) {
		// $pipes now looks like this:
		// 0 => writeable handle connected to child stdin
		// 1 => readable handle connected to child stdout
		// Any error output will be appended to /tmp/error-output.txt

		fwrite($pipes[0], 'actual test');
		fclose($pipes[0]);

		$output = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		
		$error = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		// It is important that you close any pipes before calling
		// proc_close in order to avoid a deadlock
		$return_value = proc_close($process);
		
		if ($return_value === 0)
		{
			return $output;
		}
		else
		{
			return $error;
		}
	}
	else
	{
		return 'process error';
	}
});

Route::group(array('before' => 'auth.sentry.root'), function()
{
	// Group
	Route::resource('groups', 'GroupsController', array('except' => array('show')));
	
	// User
	Route::resource('users', 'UsersController', array('except' => array('show')));
});

Route::group(array('before' => 'auth.sentry'), function()
{
    // Overview
	Route::resource('overview', 'OverviewController', array('only' => array('index')));
	
	// Domain
	Route::resource('sites', 'SitesController', array('except' => array('show')));
	
	// Log
	Route::resource('logs', 'LogsController', array('only' => array('index', 'show', 'destroy')));
	
	// Profile
	Route::resource('profile', 'ProfileController', array('only' => array('index')));
	
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
	// Login
	Route::get('login', array('as' => 'auth.login', 'uses' => 'AuthController@getLogin'));
	Route::post('login', array('uses' => 'AuthController@postLogin'));
	
	// Logout
	Route::get('logout', array('as' => 'auth.logout', 'uses' => 'AuthController@getLogout'));
});

