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

Auth::loginUsingID(1);

Auth::routes();
Route::get('/home', function () {
    return redirect()->route('home');
});
Route::get('/', 'HomeController@index')->name('home');

Route::post('/request', 'DemonstratorRequestController@update')->name('request.update');
Route::post('/request/{demRequest}/apply', 'DemonstratorApplicationController@apply')->name('request.apply');

Route::post('/application/{application}/toggle-accepted', 'DemonstratorApplicationController@toggleAccepted')->name('application.toggleaccepted');
