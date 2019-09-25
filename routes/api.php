<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('authlocaleapi')->get('actions/types', 'ActionController@getActionsTypes');
Route::middleware('authlocaleapi')->get('actions', 'ActionController@getActions');
Route::middleware('authlocaleapi')->post('actions', 'ActionController@storeAction');
Route::middleware('authlocaleapi')->post('tracking', 'ActionController@track');

Route::post('devices', 'PhysicalDevicesController@storeData')->name('api.devices');
// NEW ROUTE
Route::post('devices/actions', 'PhysicalDevicesController@storeAction');

Route::get('calendar', 'CalendarController@getSummary');

Route::get('paperwork', 'PaperWorkController@getPaperwork');

Route::post('incident', 'IncidentController@addIncident');

Route::post('expense', 'ExpenseController@addExpense');

Route::post('leave', 'MedicalDayController@addMedical');

Route::post('vacation', 'HolidayController@addVacation');

Route::delete('vacation/{id}', 'HolidayController@cancelVacation');

Route::post('login', 'AuthController@login');

Route::post('logout', 'AuthController@logout');

Route::post('fcmtoken', 'NotificationController@registerToken');

Route::post('pushstatus', 'NotificationController@pushstatus');

Route::get('profile', 'ProfileController@profile');

Route::post('profile', 'ProfileController@changePicture');
