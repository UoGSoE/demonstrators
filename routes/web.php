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
    Route::post('/student/{user}/notes', 'Api\UserNotesController@update')->name('student.notes');
    Route::post('/user/{user}/disable-blurb', 'Api\BlurbOptionsController@update')->name('user.disableBlurb');

    Route::post('/request', 'Api\RequestController@update')->name('request.update');
    Route::post('/request/{demRequest}/withdraw', 'Api\RequestController@destroy')->name('request.withdraw');
    Route::get('/request/empty-dates/{staff_id}', 'Api\RequestController@checkForEmptyDates')->name('request.emptyDates');
    
    Route::post('/request/{demRequest}/apply', 'Api\ApplicationController@store')->name('application.apply');
    Route::post('/application/{demRequest}/withdraw', 'Api\ApplicationController@destroy')->name('application.destroy');

    Route::post('/application/{application}/toggle-accepted', 'Api\ApplicationAcceptanceController@update')->name('application.toggleaccepted');

    Route::post('/application/{application}/student-confirms', 'Api\PositionOfferController@confirm')->name('application.studentconfirms');
    Route::post('/application/{application}/student-declines', 'Api\PositionOfferController@decline')->name('application.studentdeclines');

    Route::post('/application/mark-seen', 'Api\ApplicationSeenController@update')->name('application.markseen');

    Route::group(['middleware' => ['admin']], function () {
        Route::get('/admin/student/new', 'Admin\StudentController@create')->name('admin.students.create');
        Route::post('/admin/student/new', 'Admin\StudentController@store')->name('admin.students.store');
        Route::post('/admin/student/delete', 'Admin\StudentController@destroy')->name('admin.students.destroy');

        Route::get('/admin/staff', 'Admin\StaffController@index')->name('admin.staff.index');
        Route::get('/admin/staff/new', 'Admin\StaffController@create')->name('admin.staff.create');
        Route::post('/admin/staff/new', 'Admin\StaffController@store')->name('admin.staff.store');
        Route::post('/admin/staff', 'Admin\StaffController@update')->name('admin.staff.update');
        Route::post('/admin/staff/remove-course', 'Api\StaffCourseController@destroy')->name('admin.staff.removeCourse');
        Route::get('/admin/staff/{staff_id}/course/{course_id}', 'Api\CourseController@show')->name('admin.staff.courseInfo');
        Route::post('/admin/staff/remove-requests', 'Api\StaffCourseController@destroy')->name('admin.staff.removeRequests');
        Route::post('/admin/staff/reassign-requests', 'Api\StaffCourseController@update')->name('admin.staff.reassignRequests');

        Route::get('/admin/users/lookup/{username?}', 'Api\LdapController@show')->name('admin.users.ldaplookup');

        Route::get('/admin/courses', 'Admin\CourseController@index')->name('admin.courses.index');
        Route::get('/admin/courses/new', 'Admin\CourseController@create')->name('admin.courses.create');
        Route::post('/admin/courses/new', 'Admin\CourseController@store')->name('admin.courses.store');
        Route::get('/admin/courses/{id}/edit', 'Admin\CourseController@edit')->name('admin.courses.edit');
        Route::post('/admin/courses/{id}/edit', 'Admin\CourseController@update')->name('admin.courses.update');
        Route::post('/admin/courses/{id}/delete', 'Admin\CourseController@destroy')->name('admin.courses.destroy');

        Route::get('/admin/requests', 'Admin\RequestsController@index')->name('admin.requests');

        Route::post('/admin/rtw', 'Api\ReturnToWorkController@update')->name('admin.rtw.update');
        Route::get('/admin/rtw/dates/{id}', 'Api\ReturnToWorkDatesController@show')->name('admin.rtw.get_dates');
        Route::post('/admin/rtw/dates', 'Api\ReturnToWorkDatesController@update')->name('admin.rtw.update_dates');

        Route::get('/admin/contracts', 'ContractController@edit')->name('admin.edit_contracts');
        Route::post('/admin/contracts', 'Api\ContractController@update')->name('admin.update_contracts');
        Route::get('/admin/contracts/dates/{id}', 'Api\ContractDateController@show')->name('admin.contract.get_dates');
        Route::post('/admin/contracts/dates', 'Api\ContractDateController@update')->name('admin.contract.update_dates');
        Route::post('/admin/withdraw', 'ContractController@destroy')->name('admin.manual_withdraw');
        
        Route::post('/admin/students/hoover', 'HooverController@destroy')->name('admin.students.hoover');

        Route::post('/admin/permissions/{id}', 'Api\PermissionController@update')->name('admin.permissions');

        Route::get('/admin/reports/output1', 'ReportController@output1')->name('admin.reports.output1');
        Route::get('/admin/reports/output2', 'ReportController@output2')->name('admin.reports.output2');
        Route::get('/admin/reports/output3', 'ReportController@output3')->name('admin.reports.output3');
        Route::get('/admin/reports/output4', 'ReportController@output4')->name('admin.reports.output4');
        Route::get('/admin/reports/output5', 'ReportController@output5')->name('admin.reports.output5');
        Route::get('/admin/reports/output6', 'ReportController@output6')->name('admin.reports.output6');
        Route::get('/admin/reports/output7', 'ReportController@output7')->name('admin.reports.output7');

        Route::get('/admin/reports/output1/download', 'ReportDownloadController@output1')->name('admin.reports.output1.download');
        Route::get('/admin/reports/output2/download', 'ReportDownloadController@output2')->name('admin.reports.output2.download');
        Route::get('/admin/reports/output3/download', 'ReportDownloadController@output3')->name('admin.reports.output3.download');
        Route::get('/admin/reports/output4/download', 'ReportDownloadController@output4')->name('admin.reports.output4.download');
        Route::get('/admin/reports/output5/download', 'ReportDownloadController@output5')->name('admin.reports.output5.download');
        Route::get('/admin/reports/output6/download', 'ReportDownloadController@output6')->name('admin.reports.output6.download');
        Route::get('/admin/reports/output7/download', 'ReportDownloadController@output7')->name('admin.reports.output7.download');

        Route::get('/admin/import', 'Admin\ImportController@index')->name('import.index');
        Route::post('/admin/import', 'Admin\ImportController@update')->name('import.update');

        Route::get('/admin/test', function () {
            \Notification::send(auth()->user(), new \App\Notifications\TestNotification());
        });
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
