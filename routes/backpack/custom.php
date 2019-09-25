<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
], function () {

    Route::group([
        'middleware' => ['role:admin'],
    ], function (){
        Route::group([
            'namespace'  => 'App\Http\Controllers\Admin',
        ], function (){
            CRUD::resource('companies', 'CompanyCrudController');
            CRUD::resource('countries', 'CountryCrudController');
            CRUD::resource('plans', 'PlanCrudController');
            CRUD::resource('currencies', 'CurrencyCrudController');
            CRUD::resource('subscriptions', 'SubscriptionCrudController');
        });

        Route::group([
            'namespace'  => 'App\Http\Controllers',
        ], function (){
	  Route::get('financial_summary', 'FinancialController@index');
        Route::get('reject_list', 'RejectListController@index');
        Route::get('reject_list/reject/{id}', 'RejectListController@reject');
        Route::get('trial_mode', 'RejectListController@trial_mode');
        Route::get('trial_mode/edit/{id}', 'RejectListController@edit_trial');
        Route::post('save_trial_mode', 'RejectListController@save_trial_mode');
        Route::get('company_palns', 'CompanyPlanManageController@companyPlanList');
        Route::get('company_plans/edit/{id}', 'CompanyPlanManageController@edit');
        Route::get('company_plans/delete/{id}', 'CompanyPlanManageController@delete');
        Route::post('save_company_plan', 'CompanyPlanManageController@save_compay_plan');
            // change password of employee in company panel
            Route::get('companies/{id}/change_password', 'ChangePasswordController@company_index');
            Route::post('companies/{id}/change_password', 'ChangePasswordController@company_update')->name('company_change_password');
        });
    });

    /* Company routes */
    Route::group([
        'middleware' => ['role:company'],
    ], function (){
        Route::group([
            'namespace'  => 'App\Http\Controllers\Admin',
        ], function (){
            CRUD::resource('workcenters', 'WorkcenterCrudController');
            CRUD::resource('employees', 'EmployeeCrudController');
            CRUD::resource('actions-options', 'ActionsOptionCrudController');
            CRUD::resource('departments', 'DepartmentCrudController');
            CRUD::resource('schedules', 'ScheduleCrudController');
            CRUD::resource('events', 'EventCrudController');
            CRUD::resource('apps', 'AppCrudController');
            CRUD::resource('devices/schedules', 'DevicesScheduleCrudController');
            CRUD::resource('devices/logs', 'DeviceLogCrudController');
            Route::get('devices/{id}/get-data', 'DeviceCrudController@getData')->name('get_device_data');
            CRUD::resource('devices', 'DeviceCrudController');
            CRUD::resource('event_type', 'EventTypeCrudController');
            CRUD::resource('event_mandatory_type', 'EventMandatoryTypeCrudController');
            CRUD::resource('calendar_event', 'CalendarEventCrudController');
	        CRUD::resource('transaction', 'TransactionCrudController');
            // edit number of holidays and working days in company panel
            CRUD::resource('holidaydays', 'HolidayDaysCrudController');
            Route::get('holidaydays', function () {
                return redirect('account');
            });
            Route::get('holidaydays/create', function () {
                return redirect('account');
            });
        });
        Route::group([
            'namespace'  => 'App\Http\Controllers',
        ], function (){
            Route::get('reports/general_listing', 'ReportController@getGeneralList');
            Route::get('reports/monthly_decimal_hours', 'ReportController@getMonthlyDecimalHours');
            Route::get('reports/hours_by_employee', 'ReportController@getHoursByEmployee');
            Route::get('reports/monthly_summary', 'ReportController@getMonthlySummary');
            Route::get('reports/detailed_hours', 'ReportController@getDetailedHours');
            Route::get('reports/hours_by_department', 'ReportController@getHoursByDepartment');
            Route::get('reports/hours_by_department_employee', 'ReportController@getHoursByDepartmentEmployee');
            Route::get('reports/punctuality', 'ReportController@getPunctuality');

            Route::get('holidays/approve/{id}', 'HolidayController@approveCancelVacation');
            Route::get('holidays/reject/{id}', 'HolidayController@rejectVacation');
            // change password of employee in company panel
            Route::get('employees/{id}/change_password', 'ChangePasswordController@employee_index');
            Route::post('employees/{id}/change_password', 'ChangePasswordController@employee_update')->name('employee_change_password');
			
			// holiday detail each employee
            Route::get('employee/{id}/detail', 'DetailController@index');
			
			// none-working employees
            Route::get('none_working_employees', 'NoneWorkingController@index');

            /* Financial Menu*/
            Route::get('billing', 'BillingController@index');
            Route::get('billing/addplan/{id}', 'BillingController@addPlan');
            Route::post('billing/post_strip_payment', 'BillingController@postPaymentStrip');
            Route::post('billing/post_paypal_payment', 'PaypalController@payWithpaypal');
            Route::get('billing/get_paypal_payment_result', 'PaypalController@getExpressCheckoutSuccess')->name('payment_result');
            Route::get('billing/cancel_paypal_payment_result', 'PaypalController@cancelPaypalPayment')->name('cancel_payment');
            Route::get('billing/company_information', 'BillingController@company_information');
            Route::post('billing/subscription/inactive/visa', 'BillingController@inactivePayment');
            Route::post('billing/subscription/inactive/paypal', 'PaypalController@inactivePayment');


            Route::get('createpaypalplan', 'PaypalController@cretePaypalPlan');
            Route::get('createBillingAgreement', 'PaypalController@createBillingAgreement');
            Route::get('createProduct', 'PaypalController@createProduct');
            Route::get('createPlan', 'PaypalController@createPlan');
            Route::get('createSubscription', 'PaypalController@createSubscription');
        });
    });

    /* Employee routes */
    Route::group([
        'middleware' => ['role:employee'],
    ], function (){
        Route::group([
            'namespace'  => 'App\Http\Controllers',
        ], function (){
            Route::get('record', 'RecordController@show');
            Route::post('record', 'RecordController@store');
            Route::get('calendar', 'Employer\CalendarController@index');
            Route::get('calendar', 'CalendarController@getEmployerCalendar');
            Route::get('holidays/cancel/{id}', 'HolidayController@cancelVacation');
        });
    });
    
    /* Company, Admin Combined routes*/
    Route::group([
        'middleware' => ['role:admin|company'],
    ], function () {
        Route::group([
            'namespace' => 'App\Http\Controllers\Admin',
        ], function () {
            CRUD::resource('transaction', 'TransactionCrudController');
        });

        Route::group([
            'namespace' => 'App\Http\Controllers',
        ], function () {
            Route::get('transaction/{id}/invoice', 'BillingController@view_invoice');
            Route::get('transaction/{id}/invoice/pdf', 'BillingController@invoice_pdf');
            Route::get('transaction/{id}/preview', 'BillingController@preview_invoice');
        });
    });

    /* Combined routes */
    Route::group([
        'middleware' => ['role:employee|company'],
    ], function (){
        Route::group([
            'namespace'  => 'App\Http\Controllers\Admin',
        ], function (){
            CRUD::resource('actions', 'ActionCrudController');
            CRUD::resource('absences', 'AbsenceCrudController');
            CRUD::resource('holidays', 'HolidayCrudController');

            Route::post('holidays/validate1', 'HolidayCrudController@validate1');
            Route::post('holidays/store1', 'HolidayCrudController@store1');

            CRUD::resource('event_management', 'EmployeeEventCrudController');
            CRUD::resource('workingplace_hollidays', 'WorkingplaceHolidaysCrudController');
            CRUD::resource('expenses', 'ExpenseCrudController');
            CRUD::resource('medical_day', 'MedicalDayCrudController');
            CRUD::resource('incidents', 'IncidentCrudController');
            CRUD::resource('notification', 'NotificationCrudController');
            CRUD::resource('employee_holiday_days', 'EmployeeHolidayDaysCrudController');
        });


        Route::group([
            'namespace'  => 'App\Http\Controllers',
        ], function (){
            /* Department Event Calendar */
            Route::get('departments/{id}/calendar', 'CalendarController@getDepartmentCalendar');

            Route::post('department_event/save_event', 'CalendarController@save_event');
            Route::post('department_event/update_event', 'CalendarController@update_event');
            Route::post('department_event/delete_event', 'CalendarController@delete_event');

            /* Department Event Calendar */
            Route::get('workcenters/{id}/calendar', 'CalendarController@getWorkCenterCalendar');

            /* Employee Event Calendar */
            Route::get('employees/{id}/calendar', 'CalendarController@getEmployerEvent');
        });
    });
});
/* Payment Webhook */
Route::group(['namespace' => 'App\Http\Controllers\Webhook'],
    function () {
        // protects the route from get posts
        Route::get('/stripe', function () {
            return Redirect::route('index');
        });
        Route::post('webhook/stripe/handleinvoicesuccess', 'StripeWebhookController@handleInvoicePaymentSucceeded');
        Route::post('webhook/stripe/handleinvoicefail', 'StripeWebhookController@handleInvoicePaymentFailed');
        Route::post('webhook/paypal/handleinvoicesuccess', 'PaypalWebhookController@handleInvoicePaymentSucceeded');
        Route::post('webhook/paypal/handleinvoicefail', 'PaypalWebhookController@handleInvoicePaymentFailed');
    });
/* End */


/* Frontend Dashboard */
Route::group([
    'middleware' => ['web'],
    'prefix' => ''
], function () {
    Route::group([
        'namespace' => 'App\Http\Controllers\Frontend',
        'middleware' => ['guest:' . backpack_guard_name()],
    ], function () {
        Route::get('/plan', 'HomeController@index')->name('plans');
        Route::get('/home/register', 'HomeController@register');
        Route::get('frontend/getplan', 'HomeController@getPlan');
        Route::get('frontend/addplan/{id}/{type}', 'HomeController@addPlan');
    });
});
/* End */
