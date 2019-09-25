<?php

/*
|--------------------------------------------------------------------------
| Backpack\Base Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Backpack\Base package.
|
*/

Route::group(
[
    'namespace'  => 'App\Http\Controllers',
    'middleware' => 'web',
    'prefix'     => config('backpack.base.route_prefix'),
],
function () {
    // if not otherwise configured, setup the auth routes
    if (config('backpack.base.setup_auth_routes')) {
        // Authentication Routes...
        Route::get('login', 'Auth\LoginController@showLoginForm')->name('backpack.auth.login');
        Route::post('login', 'Auth\LoginController@login');
        Route::get('logout', 'Auth\LoginController@logout')->name('backpack.auth.logout');
        Route::post('logout', 'Auth\LoginController@logout');

        // Registration Routes...
        Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('backpack.auth.register');
        Route::get('register/confirm', 'Auth\RegisterController@showRegisterConfirmationForm')->name('backpack.auth.register.confirm');
        Route::post('register', 'Auth\RegisterController@register');
        Route::post('register/confirm', 'Auth\RegisterController@registerConfirm');

        // Password Reset Routes...
        Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('backpack.auth.password.reset');
        Route::post('password/reset', 'Auth\ResetPasswordController@reset');
        Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('backpack.auth.password.reset.token');
        Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('backpack.auth.password.email');
    }

    // if not otherwise configured, setup the dashboard routes
    if (config('backpack.base.setup_dashboard_routes')) {

//        Route::group([
//            'middleware' => ['role:admin|company|employee'],
//        ], function (){
//            Route::get('dashboard', 'AdminController@dashboard')->name('backpack.dashboard');
////            Route::get('/', 'AdminController@redirect')->name('backpack');
//        });

        Route::get('dashboard', 'AdminController@dashboard')->name('backpack.dashboard');
        Route::get('/', 'AdminController@redirect')->name('backpack');
    }

    // if not otherwise configured, setup the "my account" routes
    if (config('backpack.base.setup_my_account_routes')) {
        Route::get('account', 'Auth\MyAccountController@getAccountInfoForm')->name('backpack.account.info');
        Route::post('account', 'Auth\MyAccountController@postAccountInfoForm');
        Route::get('change-password', 'Auth\MyAccountController@getChangePasswordForm')->name('backpack.account.password');
        Route::post('change-password', 'Auth\MyAccountController@postChangePasswordForm');
		
		// alert configuration
        Route::get('alert', 'AlertController@index')->name('backpack.account.alert');
        Route::post('alert', 'AlertController@save')->name('backpack.account.alert');
    }
});
