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

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

//get swagger documnetation
Route::get('docs', function() {
	return file_get_contents(base_path() . '/public/swagger/index.html');
});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//ios/android routes
Route::group([
	'prefix' => 'api/v1',
	'namespace'=>'Api'
	], function() {

	Route::post('users/www', 'UsersController@registerWWWUser');
	Route::post('users/www/session', 'UsersController@loginWWWUser');
	Route::post("report", 'ReportController@error_reporting');
});
