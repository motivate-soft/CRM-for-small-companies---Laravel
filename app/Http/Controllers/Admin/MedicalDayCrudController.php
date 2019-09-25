<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MedicalDayRequest;
use App\Http\Requests\MedicalDayUpdateRequest;
use App\Models\Employee;
use App\Models\EventMandatoryType;
use App\Models\MedicalDay;
use App\Models\Notification;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MedicalDayCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\MedicalDay');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/medical_day');
        $this->crud->setEntityNameStrings(trans('fields.medical_day'), trans('fields.medical_day'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->enableExportButtons();

        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $event_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->employee->company->id)->first();
        } else if (backpack_user()->role == User::ROLE_COMPANY) {
            $event_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->company->id)->first();
        }

        $this->crud->setColumns([
            [
                'name'  => 'date',
                'label' => trans('fields.date'),
                'type'  => 'date',
            ],
            [
                'name'  => 'leave_type',
                'label' => trans('fields.leave_type'),
                'type' => 'select_from_array',
                'options' => ['discharge' => trans('fields.discharge'), 'leave' => trans('fields.leave')],
                'allows_null' => false,
            ],
        ]);

        if ($event_type && $event_type->has_confirmation == true) {
            $this->crud->addColumn([
                'name' => 'status',
                'label' => trans('fields.status'),
                'type' => 'select_from_array',
                'options' => ['pending' => trans('fields.pending'), 'approved' => trans('fields.approved'), 'rejected' => trans('fields.rejected')],
            ])->afterColumn('date');
        }

        if ($event_type && $event_type->has_file == true) {
            $this->crud->addColumn([
                'name' => 'photo',
                'label' => trans('fields.document'),
                'type' => 'image',
            ])->afterColumn('date');
        }

        if ($event_type && $event_type->has_amount == true) {
            $this->crud->addColumn([
                'name' => 'amount',
                'label' => trans('fields.amount'),
                'type' => 'text',
            ])->afterColumn('date');
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
                    return $query->leftJoin('employees', 'employees.id', '=', 'medical_days.employee_id')
                        ->leftJoin('users', 'users.id', '=', 'employees.user_id')
                        ->orderBy('users.name', $columnDirection)->select('medical_days.*');
                }
            ])->afterColumn('status');

            $this->crud->addFields([
                [
                    'name' => 'date', // a unique name for this field
//                    'end_name' => 'end_date', // the db column that holds the end_date
                    'label' => trans('fields.date'),
                    'type' => 'date_picker',
                    'attributes' => [
                        'readonly' => 'readonly',
                        'disabled' => 'disabled',
                    ],
                    'date_picker_options' => [
                        'todayBtn' => true,
                        'format' => 'dd/mm/yyyy',
                        'language' => backpack_user()->company->language->abbr
                    ],
                ],
                [
                    'name' => 'employee_id',
                    'type' => 'hidden',
                ],
            ]);

            if ($event_type && $event_type->has_confirmation == true) {
                $this->crud->addFields([[
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

            $this->crud->addFields([
                [
                    'name' => 'date', // a unique name for this field
//                    'end_name' => 'end_date', // the db column that holds the end_date
                    'label' => trans('fields.date'),
                    'type' => 'date_picker',
                    'date_picker_options' => [
                        'todayBtn' => true,
                        'format' => 'dd/mm/yyyy',
                        'language' => backpack_user()->employee->company->language->abbr
                    ],
                ],
                [
                    'name' => 'employee_id',
                    'type' => 'hidden',
                    'value' => backpack_user()->employee->id
                ],
                [
                    'name'  => 'leave_type',
                    'label' => trans('fields.leave_type'),
                    'type' => 'select_from_array',
                    'options' => ['discharge' => trans('fields.discharge'), 'leave' => trans('fields.leave')],
                    'allows_null' => false,
                ],
            ]);

            if ($event_type && $event_type->has_file == true) {
                $this->crud->addFields([[
                    'name' => 'photo',
                    'label' => trans('fields.document'),
                    'type' => 'upload',
					'upload' => true,
					'hint' => 'jpg, png, pdf, doc, docx',
                    //'crop' => true, // set to true to allow cropping, false to disable
                    //'aspect_ratio' => 1, // ommit or set to 0 to allow any aspect ratio
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
        $this->crud->setRequiredFields(MedicalDayRequest::class, 'create');
        $this->crud->setRequiredFields(MedicalDayUpdateRequest::class, 'edit');

        //
        if(backpack_user()->role == User::ROLE_COMPANY) {
            $employees = backpack_user()->company->employees;
            foreach($employees as $employee){
                $employee_id = $employee->id;
                MedicalDay::where('employee_id', $employee_id)->update(['company_is_read'=>1]);
            }
        }else if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $employee_id = backpack_user()->employee->id;
            MedicalDay::where('employee_id', $employee_id)->update(['employee_is_read'=>1]);
        }
    }

    public function store(MedicalDayRequest $request)
    {
        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $event_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->employee->company->id)->first();
        } else if (backpack_user()->role == User::ROLE_COMPANY) {
            $event_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->company->id)->first();
        }


        // your additional operations before save here
        $request->request->set('event_type_id', $event_type->id);
		$request->request->set('employee_is_read', 1);
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry

        // Send notification from employee to company
        $notification = [
            'title' => "Biodactil",
            'sound' => true,
            'body' => trans('fields.notification_request_medical_day'),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'icon' => 'fcm_push_icon'
        ];

        $data = [
            "type" => "medical"
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
        $id = $request->id;
        $before_status = MedicalDay::find($id)->status;
        $after_status = $request->status;


        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $event_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->employee->company->id)->first();
        } else if (backpack_user()->role == User::ROLE_COMPANY) {
            $event_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->company->id)->first();
        }

        if ($after_status == 'rejected' && $before_status != $after_status && $event_type->has_confirmation) {
            $reject_message = $request->reject_message;
            if ($reject_message == '') {
                return redirect()->back()->withErrors("Please write reject reason.");
            }
        }

        $redirect_location = parent::updateCrud($request);

        if ($before_status != $after_status) {
            // if status is changed, company --> employee
            MedicalDay::find($request->id)->update(['employee_is_read'=>0]);
            // Send notification from employee to company
            $notification = [
                'title' => "Biodactil",
                'sound' => true,
                'body' => trans('fields.notification_event_changed'),
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'icon' => 'fcm_push_icon'
            ];

            $data = [
                "type" => "medical"
            ];
			if(Employee::find($request->employee_id)->webToken->first()){
				$web_token = Employee::find($request->employee_id)->webToken->first()->token;
				$webNotification = [
					'to' => $web_token,
					'notification' => $notification,
					'data' => $data
				];
	//            return $webNotification;
				$web_notification_res = json_decode(Notification::web_notification($webNotification), true);
			}
			
            try {
                $token = Employee::find($request->employee_id)->apps->first()->fcmToken->first()->token;
            } catch (\Exception $e) {

            }

            if (isset($token)) {

                $notification_item = new Notification();
                $notification_item->token_id = Employee::find($request->employee_id)->apps->first()->fcmToken->first()->id;
                $notification_item->type = 'medical_day';
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
                    "type" => "medical_day",
                    "status" => $request->status,
                    "title" => 'Biodactil Notification',
                    "message" => 'Your event status has been changed!'
                ];

                $fcmNotification = [
                    'to'        => $token, //single token
                    'notification' => $notification,
                    'data' => $data
                ];

                $res = json_decode(Notification::notification($fcmNotification), true);
                if ($res['success'] == 1) {
                    $notification_item->status = 'received';
                    $notification_item->save();
                }

                //web notification
                $web_token = Employee::find($request->employee_id)->webToken->first()->token;
                $webNotification = [
                    'to'        => $web_token,
                    'notification' => $notification,
                    'data' => $data
                ];
                $web_notification_res = json_decode(Notification::web_notification($webNotification), true);
            }
        }
        return $redirect_location;
    }
}
