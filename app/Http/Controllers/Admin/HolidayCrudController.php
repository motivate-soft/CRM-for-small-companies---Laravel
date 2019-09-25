<?php

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeEvent;
use App\Models\EmployeeHolidayDays;
use App\Models\EventMandatoryType;
use App\Models\EventType;
use App\Models\Holiday;
use App\Models\Notification;
use App\Models\WebToken;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\HolidayRequest as StoreRequest;
use App\Http\Requests\HolidayRequest1 as StoreRequest1;
use App\Http\Requests\HolidayRequest as UpdateRequest;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Prologue\Alerts\Facades\Alert;

/**
 * Class HolidayCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class HolidayCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Holiday');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/holidays');
        $this->crud->setEntityNameStrings(trans('fields.holiday'), trans('fields.holidays'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $event_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->employee->company->id)->first();
        } else if (backpack_user()->role == User::ROLE_COMPANY) {
            $event_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->company->id)->first();
        }

        $this->crud->setColumns([
            [
                'name'  => 'start_date',
                'label' => trans('fields.start_date'),
                'type'  => 'date',
            ],
            [
                'name'  => 'end_date',
                'label' => trans('fields.end_date'),
                'type'  => 'date',
            ],
            [
                'name'  => 'real_holiday_days',
                'label' => trans('fields.real_holiday_days'),
                'type'  => 'text',
            ],
        ]);

        if ($event_type && $event_type->has_confirmation == true) {
            $this->crud->addColumn([
                'name' => 'status',
                'label' => trans('fields.status'),
                'type' => 'select_from_array',
                'options' => ['pending' => trans('fields.pending'), 'approved' => trans('fields.approved'), 'rejected' => trans('fields.rejected')],
				'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('status', 'like', '%'.$searchTerm.'%');
                }
            ])->afterColumn('real_holiday_days');
        }

        if ($event_type && $event_type->has_file == true) {
            $this->crud->addColumn([
                'name' => 'doc',
                'label' => trans('fields.document'),
                'type' => 'document_url',
            ])->afterColumn('end_date');
        }

        if ($event_type && $event_type->has_amount == true) {
            $this->crud->addColumn([
                'name' => 'amount',
                'label' => trans('fields.amount'),
                'type' => 'text',
            ])->afterColumn('end_date');
        }

        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => trans('fields.created_at'),
            'type' => 'date',
        ]);

        if(backpack_user()->role == User::ROLE_COMPANY) {

            $this->crud->denyAccess(['create']);

            $this->crud->addColumn([
                'name' => 'employee_name',
                'label' => trans('fields.employee'),
                'type' => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    return $query->orWhereHas('employee', function ($q) use ($column, $searchTerm) {
                        $q->whereHas('user', function ($q) use ($column, $searchTerm) {
                            $q->where('name', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->leftJoin('employees', 'employees.id', '=', 'holidays.employee_id')
                        ->leftJoin('users', 'users.id', '=', 'employees.user_id')
                        ->orderBy('users.name', $columnDirection)->select('holidays.*');
                }
            ])->afterColumn('status');

            $this->crud->addColumn([
                'label' => '',
                'type' => 'closure',
                'name' => '',
                'function' => function($item) {
                    if ($item->cancel_state == 'requested'){
                        $now = Carbon::now()->format('Y-m-d');
                        if ($item->start_date > $now) {
                            return '<a class="btn btn-xs btn-primary" href="'.backpack_url('holidays/approve'). '/' . $item->id . '" data-toggle="tooltip"><i class="fa fa-check"></i>  ' . trans('fields.approve') . '</a>'.
                                    '  <a class="btn btn-xs btn-danger" href="'.backpack_url('holidays/reject'). '/' . $item->id . '" data-toggle="tooltip"><i class="fa fa-times"></i>  ' . trans('fields.reject') . '</a>';
                        }
                    }
                    return null;
                }
            ])->afterColumn('created_at');

            $this->crud->addColumn([
                'name' => 'cancel_state',
                'label' => trans('fields.cancel_state'),
                'type' => 'select_from_array',
                'options' => ['no_request' => '', 'requested' => trans('fields.requested'), 'approved' => trans('fields.cancelled'), 'rejected' => trans('fields.rejected')],
            ])->afterColumn('created_at');


            $this->crud->addFields([
                [
                    'name' => 'date', // a unique name for this field
                    'start_name' => 'start_date', // the db column that holds the start_date
                    'end_name' => 'end_date', // the db column that holds the end_date
                    'label' => trans('fields.date'),
                    'type' => 'date_range',
                    'attributes' => [
                        'readonly' => 'readonly',
                        'disabled' => 'disabled',
                    ],
                    // OPTIONALS
                    'start_default' => Carbon::now()->subWeek()->toDateString(), // default value for start_date
                    'end_default' => Carbon::now()->toDateString(), // default value for end_date
                    'date_range_options' => [ // options sent to daterangepicker.js
                        'timePicker' => false,
                        'locale' => ['format' => 'DD/MM/YYYY', 'firstDay' => 1],
                        'alwaysShowCalendars' => true,
                        'autoUpdateInput' => true,
                    ]
                ],
                [
                    'name' => 'employee_id',
                    'type' => 'hidden',
                ],
            ]);

            if ($event_type && $event_type->has_confirmation == true) {
                $this->crud->addFields([
                    [
                        'name'  => 'real_holiday_days',
                        'label' => trans('fields.real_holiday_days'),
                        'type'  => 'text'
                    ],
                    [
                    'name' => 'status',
                    'label' => trans('fields.status'),
                    'type' => 'select_from_array',
                    'options' => ['pending' => trans('fields.pending'), 'approved' => trans('fields.approved'), 'rejected' => trans('fields.rejected')],
                    'allows_null' => false,
                ],[
                    'name' => 'reject_message',
                    'label' => trans('fields.reject_message'),
                    'type' => 'textarea',
                ]]);
            } else if ($event_type && $event_type->has_confirmation == false) {
                $this->crud->denyAccess(['update']);
            }
        }

        if(backpack_user()->role == User::ROLE_EMPLOYEE) {

            $this->crud->denyAccess(['update']);
            $this->crud->removeAllButtonsFromStack('line');

            $this->crud->addColumn([
                    'label' => trans('fields.actions'),
                    'type' => 'closure',
                    'name' => '',
                    'function' => function($item) {
                        if ($item->cancel_state == 'no_request'){
                            $now = Carbon::now()->format('Y-m-d');
                            if ($item->start_date > $now) {
                                return '<a class="btn btn-xs btn-danger" href="'.backpack_url('holidays/cancel'). '/' . $item->id . '" data-toggle="tooltip"><i class="fa fa-times"></i>  ' . trans('fields.cancel_vacation') . '</a>';
                            }
                        }
                        return null;
                    }
            ])->afterColumn('created_at');

            $this->crud->addColumn([
                'name' => 'cancel_state',
                'label' => trans('fields.cancel_state'),
                'type' => 'select_from_array',
                'options' => ['no_request' => '', 'requested' => trans('fields.requested'), 'approved' => trans('fields.cancelled'), 'rejected' => trans('fields.rejected')],
            ])->afterColumn('created_at');

            $this->crud->addFields([
                [
                    'name' => 'event_date_range', // a unique name for this field
                    'start_name' => 'start_date', // the db column that holds the start_date
                    'end_name' => 'end_date', // the db column that holds the end_date
                    'label' => trans('fields.date'),
                    'type' => 'date_range',
                    // OPTIONALS
                    'start_default' => Carbon::now()->subWeek()->toDateString(), // default value for start_date
                    'end_default' => Carbon::now()->toDateString(), // default value for end_date
                    'date_range_options' => [ // options sent to daterangepicker.js
                        'timePicker' => false,
                        'locale' => ['format' => 'DD/MM/YYYY', 'firstDay' => 1],
                        'alwaysShowCalendars' => true,
                        'autoUpdateInput' => true,
                    ]
                ],
                [
                    'name' => 'employee_id',
                    'type' => 'hidden',
                    'value' => backpack_user()->employee->id
                ],
            ]);

            if ($event_type && $event_type->has_file == true) {
                $this->crud->addFields([[
                    'name' => 'doc',
                    'label' => trans('fields.document'),
                    'type' => 'upload',
                    'hint' => trans('fields.document_absence_hint'),
                    'upload' => true,
                    'disk' => 'uploads' // if you store files in the /public folder, please ommit this; if you store them in /storage or S3, please specify it;
                ]]);
            }

            if ($event_type && $event_type->has_amount == true) {
                $this->crud->addFields([[
                    'name' => 'amount',
                    'label' => trans('fields.amount'),
                    'type' => 'text',
                ]]);
            }

            if ($event_type && $event_type->has_comment == true) {
                $this->crud->addFields([[
                    'name' => 'comment',
                    'label' => trans('fields.comment'),
                    'type' => 'textarea',
                ]]);
            }
        }

        $this->crud->orderBy('created_at', 'DESC');

        // add asterisk for fields that are required in HolidayRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');

        $this->crud->setCreateView('create');


        //
        if(backpack_user()->role == User::ROLE_COMPANY) {
            $employees = backpack_user()->company->employees;
            foreach($employees as $employee){
                $employee_id = $employee->id;
                Holiday::where('employee_id', $employee_id)->update(['company_is_read'=>1]);
            }
        }else if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $employee_id = backpack_user()->employee->id;
            Holiday::where('employee_id', $employee_id)->update(['employee_is_read'=>1]);
        }

    }
	
	public function validate1(Request $request){
        $result = [];
        $user_id = backpack_user()->id;
        $spend_holiday_days = EmployeeHolidayDays::where('user_id', $user_id)->first()->spend_holidays;
        $holiday_days = User::find($user_id)->holiday_days;
        if($spend_holiday_days >= $holiday_days){
            $result = [
                'status'=> 'fail',
                'msg' => "You don't have holidays left"
            ];
            return $result;
        }

        $user_id = backpack_user()->id;
        $spend_holidays = EmployeeHolidayDays::where('user_id', $user_id)->where('year', date('Y'))->first()->spend_holidays;
        $holiday_days = User::find($user_id)->holiday_days;
        $left_holiday_days = $holiday_days - $spend_holidays;

        // calculate real holiday days
        $real_holiday_days = 0;
        $employee_id = backpack_user()->employee->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $requested_holidays = $this->getDatesFromRange($start_date, $end_date);
        $national_holidays = $this->get_holiday_employee($employee_id);
        $banking_holidays = $this->get_banking_days($employee_id);
        $expected_holidays = array_diff(array_diff($requested_holidays, $national_holidays), $banking_holidays);
        $working_days = $this->get_working_days($employee_id);
        foreach ($expected_holidays as $item){
            $week_day = date('N',strtotime($item));
            if(in_array($week_day, $working_days)){
                $real_holiday_days++;
            }
        }
        if($left_holiday_days < $real_holiday_days){
            $result = [
                'status'=> 'fail',
                'msg' => trans('fields.you_requested_more_than_left_holiday_days')
            ];
        }else {
            $result = [
                'status' => 'success',
                'msg' => trans('fields.real_holiday_days_is').' '.$real_holiday_days.'.'
            ];
        }
        return $result;
    }

    public function store1(StoreRequest1 $request){
        $user_id = backpack_user()->id;
        $spend_holiday_days = EmployeeHolidayDays::where('user_id', $user_id)->first()->spend_holidays;
        $holiday_days = User::find($user_id)->holiday_days;
        if($spend_holiday_days >= $holiday_days){
            Alert::success("You don't have hollidays left")->flash();
            return Redirect::to('holidays');
        }
        $event_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->employee->company->id)->first();

        $real_holiday_days = 0;
        $employee_id = backpack_user()->employee->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $requested_holidays = $this->getDatesFromRange($start_date, $end_date);
        $national_holidays = $this->get_holiday_employee($employee_id);
        $banking_holidays = $this->get_banking_days($employee_id);
        $expected_holidays = array_diff(array_diff($requested_holidays, $national_holidays), $banking_holidays);
        $working_days = $this->get_working_days($employee_id);
        foreach ($expected_holidays as $item){
            $week_day = date('N',strtotime($item));
            if(in_array($week_day, $working_days)){
                $real_holiday_days++;
            }
        }
        $comment = $request->comment;
        $holiday = new Holiday();
        $holiday->employee_id = $employee_id;
        $holiday->start_date = $start_date;
        $holiday->end_date = $end_date;
        $holiday->event_type_id = $event_type->id;
        $holiday->employee_is_read = 1;
        $holiday->comment = $comment;
        $holiday->real_holiday_days = $real_holiday_days;
        $holiday->save();

        return Redirect::to('holidays');
    }
	
    public function store(StoreRequest $request)
    {
        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $event_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->employee->company->id)->first();
        } else if (backpack_user()->role == User::ROLE_COMPANY) {
            $event_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->company->id)->first();
        }

        /* Calculate Employee Holidays */
        $request->request->set('event_type_id', $event_type->id);
		$request->request->set('employee_is_read', 1);

        $redirect_location = parent::storeCrud($request);

        // Send notification from employee to company
        $notification = [
            'title' => "Biodactil",
            'sound' => true,
            'body' => trans('fields.notification_request_holiday'),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'icon' => 'fcm_push_icon'
        ];

        $data = [
            "type" => "vacation"
        ];
		if(Employee::find($request->employee_id)->company->web_token->first()){
			$web_token = Employee::find($request->employee_id)->company->web_token->first()->token;
			$webNotification = [
				'to' => $web_token,
				'notification' => $notification,
				'data' => $data
			];
	//        return $webNotification;
			$web_notification_res = json_decode(Notification::web_notification($webNotification), true);
		}

        return $redirect_location;
    }

    public function update(Request $request)
    {
        $employee_id = Holiday::find($request->id)->employee_id;
        $user_id = Employee::find($employee_id)->user_id;

        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $event_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->employee->company->id)->first();
        } else if (backpack_user()->role == User::ROLE_COMPANY) {
            $event_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->company->id)->first();
        }

        $id = $request->id;
        $before_status = Holiday::find($id)->status;
        $after_status = $request->status;

        if ($after_status == 'rejected' && $before_status != $after_status && $event_type->has_confirmation) {
            $reject_message = $request->reject_message;
            if ($reject_message == '') {
                return redirect()->back()->withErrors("Please write reject reason.");
            }
        }
        $redirect_location = parent::updateCrud($request);


        // Update Employee Holiday days
        if ($before_status != $after_status && $after_status == 'approved') {
            $spend_holidays = $this->get_spend_holidays($employee_id);
			
            if(EmployeeHolidayDays::where('user_id', $user_id)->where('year', date('Y'))->first()){
				//return $spend_holidays;
				EmployeeHolidayDays::where('user_id', $user_id)->update(['spend_holidays'=>$spend_holidays]);
				//echo $user_id;
				
            }else{
                $employee_holiday_days = new EmployeeHolidayDays();
                $employee_holiday_days->user_id = $user_id;
                $employee_holiday_days->spend_holidays = 1;
                $employee_holiday_days->year = date('Y');
                $employee_holiday_days->save();
            }
        }

        if ($before_status != $after_status && $before_status == 'approved') {
            $spend_holidays = $this->get_spend_holidays($employee_id);
			//return $spend_holidays;
            if(EmployeeHolidayDays::where('user_id', $user_id)->where('year', date('Y'))->first()){
                EmployeeHolidayDays::where('user_id', $user_id)->update(['spend_holidays'=>$spend_holidays]);
				//echo $user_id;
				
            }else{
                $employee_holiday_days = new EmployeeHolidayDays();
                $employee_holiday_days->user_id = $user_id;
                $employee_holiday_days->spend_holidays = 0;
                $employee_holiday_days->year = date('Y');
                $employee_holiday_days->save();
            }
        }
        // Sending Notification
        if ($before_status != $after_status) {
            // if status is changed, company --> employee
            Holiday::find($request->id)->update(['employee_is_read'=>0]);
            // Send notification from company to employee
            $notification = [
                'title' => "Biodactil",
                'sound' => true,
                'body' => trans('fields.notification_event_changed'),
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'icon' => 'fcm_push_icon'
            ];

            $data = [
                "type" => "vacation"
            ];
			if(Employee::find($request->employee_id)->webToken->first()){
				 $web_token = Employee::find($request->employee_id)->webToken->first()->token;
				$webNotification = [
					'to'        => $web_token,
					'notification' => $notification,
					'data' => $data
				];
				//return $webNotification;
				$web_notification_res = json_decode(Notification::web_notification($webNotification), true);
			}
           

            try {
                $token = Employee::find($request->employee_id)->apps->first()->fcmToken->first()->token;
            } catch (\Exception $e) {

            }

            if (isset($token)) {

                $notification_item = new Notification();
                $notification_item->token_id = Employee::find($request->employee_id)->apps->first()->fcmToken->first()->id;
                $notification_item->type = 'vacation';
                $notification_item->save();

                $notification = [
                    'title' => "Biodactil",
                    'sound' => true,
                    'body' => 'Your event status has been changed!',
                    'click_action' => 'FCM_PLUGIN_ACTIVITY',
                    'icon' => 'fcm_push_icon'
                ];

                $data = [
                    "category" => "status",
                    "id" => $notification_item->id,
                    "type" => "vacation",
                    "status" => $request->status,
                    "title" => 'Biodactil Notification',
                    "message" => 'Your event status has been changed!'
                ];

                $fcmNotification = [
                    'to'        => $token, //single token
                    'notification' => $notification,
                    'data' => $data
                ];

//                $res = json_decode(Notification::notification($fcmNotification), true);
//                if ($res['success'] == 1) {
//                    $notification_item->status = 'received';
//                    $notification_item->save();
//                }
            }
        }
        return $redirect_location;
    }


    // get spend days of employee
    public function get_spend_holidays($employee_id){
        $result = 0;
        $approved_holidays = $this->get_vacation_employee($employee_id);
        $national_holidays = $this->get_holiday_employee($employee_id);
        $banking_holidays = $this->get_banking_days($employee_id);
        $expected_holidays = array_diff(array_diff($approved_holidays, $national_holidays), $banking_holidays);
        $working_days = $this->get_working_days($employee_id);
        foreach ($expected_holidays as $item){
            $week_day = date('N',strtotime($item));
            if(in_array($week_day, $working_days)){
                $result++;
            }
        }
        return $result;
    }

    //get working days of employee
    public function get_working_days($employee_id){
        $working_days = [];
        if(Employee::find($employee_id)->user->workingDays){
            $working_days = Employee::find($employee_id)->user->workingDays->pluck('id')->toarray();
        }
        return $working_days;
    }

    // get banking days approved
    public function get_banking_days($employee_id){
        $result = [];
        $employee_events = EmployeeEvent::where('employee_id', $employee_id)->where('status', 'approved')->get();
        foreach ($employee_events as $item){
            $event_type_id = $item['event_type_id'];
            $is_working_day =EventType::find($event_type_id)->is_working_day;
            if($is_working_day == 0){
                $result = array_merge($result, $this->getDatesFromRange($item['start_date'], $item['end_date']));
            }
        }
        return $result;
    }

    // get approved vacation of employee
    public function get_vacation_employee($employee_id){
        $vacations = Holiday::where('employee_id', $employee_id)->where('status', 'approved')->get();
        $result = [];
        foreach($vacations as $item){
            $result = array_merge($result, $this->getDatesFromRange($item['start_date'], $item['end_date']));
        }
        return $result;
    }

    function getDatesFromRange($start, $end, $format = 'Y-m-d') {

        // Declare an empty array
        $array = array();

        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        // Use loop to store date into array
        foreach($period as $date) {
            $array[] = $date->format($format);
        }

        // Return the array elements
        return $array;
    }

    // get national holiday for employee
    public function get_holiday_employee($employee_id){
        $national_holidays = collect();
        $department_holidays = collect();
        $workcetner_holidays = collect();
        if(Employee::find($employee_id)->department){
            $department_holidays = Employee::find($employee_id)->department->holidays; // get national department holiday for employee
        }

        if(Employee::find($employee_id)->workcenter){
            $workcetner_holidays = Employee::find($employee_id)->workcenter->holidays; // get national workcenter holiday for employee
        }
        $national_holidays = $department_holidays->merge($workcetner_holidays);
        $result = [];
        foreach($national_holidays as $item){
            $result = array_merge($result, $this->getDatesFromRange($item['start_date'], $item['end_date']));
        }
        return $result;
    }
}
