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

use App\User;
use App\Course;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\Staff as StaffResource;

Auth::routes();
Route::get('/home', function () {
    return redirect()->route('home');
});

Route::get('/', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::post('/student/{user}/notes', 'UserController@updateNotes')->name('student.notes');
    Route::post('/user/{user}/disable-blurb', 'UserController@disableBlurb')->name('user.disableBlurb');

    Route::post('/request', 'DemonstratorRequestController@update')->name('request.update');
    Route::post('/request/{demRequest}/withdraw', 'DemonstratorRequestController@destroy')->name('request.withdraw');

    Route::post('/request/{demRequest}/apply', 'DemonstratorApplicationController@store')->name('application.apply');
    Route::post('/application/{demRequest}/withdraw', 'DemonstratorApplicationController@destroy')->name('application.destroy');

    Route::post('/application/{application}/toggle-accepted', 'DemonstratorApplicationController@toggleAccepted')->name('application.toggleaccepted');

    Route::post('/application/{application}/student-confirms', 'DemonstratorApplicationController@studentConfirms')->name('application.studentconfirms');
    Route::post('/application/{application}/student-declines', 'DemonstratorApplicationController@studentDeclines')->name('application.studentdeclines');

    Route::post('/application/mark-seen', 'DemonstratorApplicationController@markSeen')->name('application.markseen');

    Route::group(['middleware' => ['admin']], function () {
        Route::get('/admin/staff', 'AdminStaffController@index')->name('admin.staff.index');
        Route::post('/admin/staff', 'AdminStaffController@update')->name('admin.staff.update');
        Route::post('/admin/staff/remove-course', 'AdminStaffController@removeCourse')->name('admin.staff.removeCourse');
        Route::get('/admin/staff/{staff_id}/course/{course_id}', 'AdminStaffController@courseInfo')->name('admin.staff.courseInfo');
        Route::post('/admin/staff/remove-requests', 'AdminStaffController@removeRequests')->name('admin.staff.removeRequests');
        Route::post('/admin/staff/reassign-requests', 'AdminStaffController@reassignRequests')->name('admin.staff.reassignRequests');

        Route::get('/admin/requests', 'AdminController@requests')->name('admin.requests');

        Route::post('/admin/rtw', 'RTWController@update')->name('admin.rtw.update');
        Route::get('/admin/rtw/dates/{id}', 'RTWController@getDates')->name('admin.rtw.get_dates');
        Route::post('/admin/rtw/dates', 'RTWController@updateDates')->name('admin.rtw.update_dates');

        Route::get('/admin/contracts', 'ContractController@edit')->name('admin.edit_contracts');
        Route::post('/admin/contracts', 'ContractController@update')->name('admin.update_contracts');
        Route::get('/admin/contracts/dates/{id}', 'ContractController@getDates')->name('admin.contract.get_dates');
        Route::post('/admin/contracts/dates', 'ContractController@updateDates')->name('admin.contract.update_dates');
        Route::post('/admin/withdraw', 'ContractController@manualWithdraw')->name('admin.manual_withdraw');
        Route::post('/admin/megadelete', 'ContractController@megaDelete')->name('admin.mega_delete');
        
        Route::post('/admin/students/hoover', 'HooverController@destroy')->name('admin.students.hoover');

        Route::get('/admin/reports/output1', 'ReportController@output1')->name('admin.reports.output1');
        Route::get('/admin/reports/output2', 'ReportController@output2')->name('admin.reports.output2');
        Route::get('/admin/reports/output3', 'ReportController@output3')->name('admin.reports.output3');
        Route::get('/admin/reports/output4', 'ReportController@output4')->name('admin.reports.output4');
        Route::get('/admin/reports/output5', 'ReportController@output5')->name('admin.reports.output5');
        Route::get('/admin/reports/output6', 'ReportController@output6')->name('admin.reports.output6');
        Route::get('/admin/reports/output7', 'ReportController@output7')->name('admin.reports.output7');

        Route::get('/admin/import', 'ImportController@index')->name('import.index');
        Route::post('/admin/import', 'ImportController@update')->name('import.update');
    });

    Route::get('/api/users', function () {
        return UserResource::collection(User::orderBy('surname')->get());
    });

    Route::get('/api/users/{id}', function ($id) {
        return new UserResource(User::find($id));
    });

    Route::get('/api/staff', function () {
        return UserResource::collection(User::staff()->orderBy('surname')->get());
    })->name('api.staff.index');

    Route::get('/api/staff/{id}', function ($id) {
        return new StaffResource(User::find($id));
    });

    Route::get('/api/courses', function () {
        return CourseResource::collection(Course::orderBy('code')->get());
    });
});
