<?php

use App\User;

Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout')->name('logout' );

Route::any('/', 'HomeController@index')->name('home');

Route::get('/profile', 'ProfileController@index')->name('profile');

Route::get('/profileUNO', 'ProfileController@uno')->name('profile.uno');

Route::post('/profileUNO', 'ProfileController@storeuno')->name('profile.storeuno');

Route::post('/profile', 'ProfileController@store')->name('profile.store');

Route::any('/new_visitor', 'visitorController@index')->name('newVisitor');

Route::any('/advance_registration', 'advanceRegistrationController@index')->name('advanceRegistration');

Route::get('/myAdvanceRegistration', 'myAdvanceRegistrationController@search')->name('myAdvanceRegistration');

Route::get('/AdvanceRegistration', 'myAdvanceRegistrationController@search')->name('gatekeeperAdvanceRegistration');

Route::post('/myAdvanceRegistrationGetVisitors', 'myAdvanceRegistrationController@getVisitors')->name('myAdvanceRegistrationGetVisitors');

Route::post('/myAdvanceRegistrationGetUsers', 'myAdvanceRegistrationController@getUsers')->name('myAdvanceRegistrationGetUsers');

Route::get('/myVisitors', 'myVisitorsController@search')->name('myVisitors');

Route::get('/entryPermission/{id}', 'AdvanceRegistrationController@entryPermission')->name('entryPermission');

Route::get('/workPermission/{id}', 'AdvanceRegistrationController@workPermission')->name('workPermission');


Route::get('/users', 'AdminController@showUsers')->name('users');

Route::post('/deleteUser', 'AdminController@deleteUser')->name('deleteUser');

Route::post('/setRole', 'AdminController@setUserRole')->name('setUserRole');

Route::get('/deleteWorkPermissiont/{id}', 'AdminController@deleteWorkPermission')->name('deleteWorkPermissiont');

Route::post('/setWorkPermit', 'AdminController@setUserWorkPermit')->name('setUserWorkPermit');

Route::post('/setEntryPermit', 'AdminController@setUserEntryPermit')->name('setUserEntryPermit');

Route::get('/usersSearch', 'AdvanceRegistrationController@userSerach')->name('user.search');

Route::get('/mawaUsersSearch', 'AdvanceRegistrationController@mawaUserSerach')->name('mawa.user.search');

Route::get('/areaPermissionEMail/{id}', 'AdvanceRegistrationController@areaPermission')->name('areaPermission.email');



Route::get('/history/action', 'historyActionLogController@index')->name('action.history.log');

Route::get('/history/visits', 'historyActionLogController@visits')->name('visits.history.log');

Route::get('/history/visits/export/csv', 'historyActionLogController@ExportCONTimes')->name('visits.history.export.csv');


route::get('/areaPermission', 'areaPermissionController@index')->name('area.permission');

Route::get('/mawaSaveUser', 'areaPermissionController@new')->name('new.area.permission');

Route::post('/mawaSaveUser', 'areaPermissionController@save')->name('mawa.save.user');

Route::get('/mawaRemoveAreaPermission/{id}', 'areaPermissionController@removeAreaPermission')->name('mawa.remove.area.permission');

Route::post('/areaPermissionUpdate/{id}', 'areaPermissionController@update')->name('mawa.edit.user');

route::get('/areaPermission/{id}', 'areaPermissionController@edit')->name('area.permission.edit');

Route::post('/mawaAddUser', 'areaPermissionController@addNewUser')->name('mawa.add.user');

Route::get('/mawaRemoveUser/{id}', 'areaPermissionController@removeUser')->name('mawa.remove.user');

Route::post('/mawaChangeUserPosition/{id}', 'areaPermissionController@mawaChangeUserPosition')->name('mawa.change.user.position');



Route::post('/makeSpontaneousVisit', 'VisitController@makeSpontaneousVisit')->name('makeSpontaneousVisit');

Route::get('/Visits', 'VisitController@search')->name('Visits');

Route::post('/visitsGetUsers', 'VisitController@getUsers')->name('visitsGetUsers');

Route::post('/changeDateOfVisit', 'VisitController@changeDateOfVisit')->name('changeDateOfVisit');

Route::get('/Visitors', 'myVisitorsController@search')->name('gatekeeperVisitors');

Route::any('/Tests', 'testController@index')->name('tests');

Route::any('/settings', 'AdminController@index')->name('admin.settings');

Route::any('/adminVisitors', 'myVisitorsController@admin')->name('admin.visitor.table');



Route::get('/emailOverview', 'emailTemplateController@overview')->name('email.overview');

Route::get('/emailTemplates', 'emailTemplateController@index')->name('emailTemplates');

Route::post('/emailTemplates', 'emailTemplateController@post')->name('emailTemplatesPost');

Route::post('/deleteCompleteVisitor', 'myVisitorsController@deleteCompleteVisitor')->name('deleteCompleteVisitor');

Route::any('/safetyInstructionQuestions/{id}', 'myVisitorsController@safetyInstructionQuestions')->name('safetyInstructionQuestions');

Route::any('/printTestResults/{id}/{short}', 'myVisitorsController@printTestResults')->name('print.TestResults');

Route::any('/test', 'testController@test')->name('test');

Route::get('/lang/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
})->name('lang');

Route::get('/faq/help', function () {
    $users = User::select()->where("username", "!=", "service01.fhp@unilever.com")->where("role", "=", "Admin")->orWhere("role", "=", "Super Admin")->where("username", "!=", "service01.fhp@unilever.com")->sortable('surname')->get();
    return view('faq-help')->with('users', $users);
})->name('faq-help');

Route::any('/printQRCode/{id}/{visitor}', function ($id, $visitor) {
    return view('prints.qrcode')->with('id', $id)->with('visitor', $visitor);
})->name('print.qrCode');

Route::any('/printTestResults', function () {
    return view('prints.testResult');
})->name('print.TestResults');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
