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
Route::post('/user/{user}/disable-blurb', 'UserController@disableBlurb')->name('user.disableBlurb');

Route::post('/request', 'DemonstratorRequestController@update')->name('request.update');
Route::post('/request/{demRequest}/withdraw', 'DemonstratorRequestController@destroy')->name('request.withdraw');

Route::post('/request/{demRequest}/apply', 'DemonstratorApplicationController@store')->name('application.apply');
Route::post('/application/{demRequest}/withdraw', 'DemonstratorApplicationController@destroy')->name('application.destroy');

Route::post('/application/{application}/toggle-accepted', 'DemonstratorApplicationController@toggleAccepted')->name('application.toggleaccepted');

Route::post('/application/{application}/student-confirms', 'DemonstratorApplicationController@studentConfirms')->name('application.studentconfirms');
Route::post('/application/{application}/student-declines', 'DemonstratorApplicationController@studentDeclines')->name('application.studentdeclines');

Route::group(['middleware' => ['admin']], function () {
    Route::get('/admin/contracts', 'ContractController@edit')->name('admin.edit_contracts');
    Route::post('/admin/contracts', 'ContractController@update')->name('admin.update_contracts');
    Route::get('/admin/staff', 'AdminStaffController@index')->name('admin.staff');
    Route::get('/admin/staff/old', 'AdminController@staff')->name('admin.staff.old');
    Route::post('/admin/rtw', 'ContractController@updateRTW')->name('admin.update_rtw');
    Route::post('/admin/withdraw', 'ContractController@manualWithdraw')->name('admin.manual_withdraw');
    Route::post('/admin/megadelete', 'ContractController@megaDelete')->name('admin.mega_delete');

    Route::get('/admin/import', 'ImportController@index')->name('import.index');
    Route::post('/admin/import', 'ImportController@update')->name('import.update');
});
