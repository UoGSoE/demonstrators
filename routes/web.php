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

Auth::routes();
Route::get('/home', function () {
    return redirect()->route('home');
});
Route::get('/', 'HomeController@index')->name('home');

Route::post('/student/{user}/notes', 'UserController@updateNotes')->name('student.notes');

Route::post('/request', 'DemonstratorRequestController@update')->name('request.update');
Route::post('/request/{demRequest}/apply', 'DemonstratorApplicationController@apply')->name('request.apply');

Route::post('/application/{application}/toggle-accepted', 'DemonstratorApplicationController@toggleAccepted')->name('application.toggleaccepted');

Route::get('/admin/contracts', 'ContractController@edit')->name('admin.edit_contracts');
Route::post('/admin/contracts', 'ContractController@update')->name('admin.update_contracts');
