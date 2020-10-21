<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::resource('users', 'UsersController');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
if ($options['reset'] ?? true) {
    Route::resetPassword();
}

// Email Verification Routes...
if ($options['verify'] ?? false) {
    Route::emailVerification();
}

Route::get('/home/authorized-clients', 'HomeController@getAuthorizedClients')->name('authorized-clients');
Route::get('/home/my-clients', 'HomeController@getClients')->name('personal-clients');
Route::get('/home/my-tokens', 'HomeController@getTokens')->name('personal-tokens');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/paypal', 'PayPalController@index')->name('paypal');
URL::forceScheme('https');
// Route::get('/', function () {
//   return view('welcome');
// })->middleware('guest');
