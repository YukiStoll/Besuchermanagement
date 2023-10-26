<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['web']], function () {

    Route::get('/newVisitor', 'VisitorAPIController@index')->name('newVisitor.all');

    Route::post('/newVisitor', 'VisitorAPIController@store')->name('newVisitor.store');

    Route::get('/newVisitor/test', 'VisitorAPIController@search')->name('newVisitor.search');

    Route::get('/newVisitor/{id}', 'VisitorAPIController@show')->name('newVisitor.show');

    Route::put('/newVisitor/{id}', 'VisitorAPIController@update')->name('newVisitor.update');

    Route::delete('/newVisitor/{id}', 'VisitorAPIController@destroy')->name('newVisitor.destroy');

    Route::post('/showGroupVisitor', 'VisitorAPIController@showGroup')->name('showGroupVisitor.showGroup');



    Route::get('/newAdvancedRegistration', 'AdvanceRegistrationAPIController@index')->name('newAdvancedRegistration.all');

    Route::post('/newAdvancedRegistration', 'AdvanceRegistrationAPIController@store')->name('newAdvancedRegistration.store');

    Route::get('/newAdvancedRegistration/{id}', 'AdvanceRegistrationAPIController@show')->name('newAdvancedRegistration.show');

    Route::put('/newAdvancedRegistration/{id}', 'AdvanceRegistrationAPIController@update')->name('newAdvancedRegistration.update');

    Route::delete('/newAdvancedRegistration/{id}', 'AdvanceRegistrationAPIController@destroy')->name('newAdvancedRegistration.destroy');

    Route::post('/searchAdvancedRegistration', 'AdvanceRegistrationAPIController@search')->name('searchAdvancedRegistration.search');

    Route::post('/newAdvancedRegistration/fileUpload/{id}', 'AdvanceRegistrationAPIController@fileUpload')->name('AdvancedRegistration.fileUpload');

    Route::post('/newAdvancedRegistration/fileDelete', 'AdvanceRegistrationAPIController@fileDelete')->name('AdvancedRegistration.fileDelete');

    Route::post('/newAdvancedRegistration/tempSaveDocuments', 'AdvanceRegistrationAPIController@tempSaveDocuments')->name('AdvancedRegistration.tempSaveDocuments');

    Route::post('/updataMawaIDForVisitor/{id}', 'AdvanceRegistrationAPIController@updataMawaIDForVisitor')->name('AdvancedRegistration.updataMawaIDForVisitor');

    Route::get('/getMawaIDs/{id}', 'AdvanceRegistrationAPIController@getMawaIDs')->name('AdvancedRegistration.getMawaIDs');

    Route::post('/sendMawaPermissionEMail/{id}', 'AdvanceRegistrationAPIController@sendMawaPermissionEMail')->name('AdvancedRegistration.sendMawaPermissionEMail');





    Route::get('/newVisit', 'VisitAPIController@index')->name('newVisit.all');

    Route::post('/newVisit', 'VisitAPIController@store')->name('newVisit.store');

    Route::get('/newVisit/{id}', 'VisitAPIController@show')->name('newVisit.show');

    Route::post('/newVisit/{id}', 'VisitAPIController@search')->name('Visit.search');

    Route::put('/newVisit/{id}', 'VisitAPIController@update')->name('newVisit.update');

    Route::delete('/newVisit/{id}', 'VisitAPIController@destroy')->name('newVisit.destroy');

    Route::post('/getMaWaVisitor', 'VisitAPIController@getMaWaVisitor')->name('getMaWaVisitor');

});


Route::get('/it-porter/itporter', 'ITVisitorAPIController@index')->name('IT-Visitor.test');

Route::get('/it-porter/visits/{id}', 'ITVisitorAPIController@show')->name('IT-Visitor.show');

Route::get('/it-porter/visitsByVisitor', 'ITVisitorAPIController@search')->name('IT-Visitor.search');

Route::post('/it-porter/visits', 'ITVisitorAPIController@store')->name('IT-Visitor.store');

Route::put('/it-porter/visits/{id}', 'ITVisitorAPIController@update')->name('IT-Visitor.update');



Route::post('/admin-settings', 'AdminController@update')->name('admin-settings.update');

Route::any('/get-visits-for-holiday/{id}', 'ProfileController@hasVisits')->name('visits.holiday.show');

Route::get('/user/{id}', 'userInformationController@show')->name('user.show');




Route::post('/MaWa-Badge/{badge_number}', 'MaWaAPIController@create')->name('MaWa.create');

Route::delete('/MaWa-Badge/{badge_number}/{transactionId}', 'MaWaAPIController@destroy')->name('MaWa.destroy');

Route::put('/MaWa-Person', 'MaWaAPIController@store')->name('MaWa.store');
