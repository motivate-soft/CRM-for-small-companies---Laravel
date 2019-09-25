<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeEvent;
use Illuminate\Http\Request;
use Backpack\CRUD\app\Http\Controllers\CrudController;

use App\Http\Requests\EventRequest as StoreRequest;
use App\Http\Requests\EventRequest as UpdateRequest;
use App\Http\Requests\EmployEventRequest;
use App\User;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use App\Models\Notification;

class EmployeeEventCrudController extends CrudController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function setup()
    {
        $this->crud->setModel('App\Models\EmployeeEvent');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/event_management');
        $this->crud->setEntityNameStrings(trans('fields.event'), trans('fields.events'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->removeButton('delete');
        $this->crud->removeButton('edit');

        $this->crud->addButtonFromView('line', 'delete-mandatory', 'delete-mandatory', 'beginning');
//        $this->crud->denyAccess(['create']);
        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'label' => trans('fields.event_type'), // Table column heading
                'type' => "select",
                'name' => 'event_type_id', // the column that contains the ID of that connected entity;
                'entity' => 'event_type', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\EventType", // foreign key model
            ],
            [
                'name'  => 'start_date',
                'label' => trans('fields.start_date'),
                'type'  => 'text',
            ],
            [
                'name'  => 'end_date',
                'label' => trans('fields.end_date'),
                'type'  => 'text',
            ],
            [
                'name' => 'status',
                'label' => trans('fields.status'),
                'type' => 'select_from_array',
                'options' => ['pending' => trans('fields.pending'), 'approved' => trans('fields.approved'), 'rejected' => trans('fields.rejected')],
                'allows_null' => false,
            ],

        ]);

         if(backpack_user()->role == User::ROLE_COMPANY) {
            $this->crud->denyAccess(['create']);
            $this->crud->addFields([
                [
                    'name'  => 'name',
                    'label' => trans('fields.name'),
                    'type'  => 'text',
                    'attributes' => [
                        'readonly' => 'readonly',
                    ],
                ],
                [
                    'name' => 'date', // a unique name for this field
                    'start_name' => 'start_date', // the db column that holds the start_date
                    'end_name' => 'end_date', // the db column that holds the end_date
                    'label' => trans('fields.date'),
                    'type' => 'date_range',
                    'attributes' => [
                        'readonly' => 'readonly',
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
                    'name' => 'status',
                    'label' => trans('fields.status'),
                    'type' => 'select_from_array',
                    'options' => ['pending' => trans('fields.pending'), 'approved' => trans('fields.approved'), 'rejected' => trans('fields.rejected')],
                    'allows_null' => false,
                ],
                [
                    'name' => 'employee_id',
                    'type' => 'hidden',
                ],
            ]);
         }

        if(backpack_user()->role == User::ROLE_EMPLOYEE) {
            $this->crud->removeAllButtonsFromStack('line');
            $this->crud->addFields([
                [
                    'name'  => 'name',
                    'label' => trans('fields.name'),
                    'type'  => 'text',
                ],
                [
                    'label' => trans('fields.event_type'), // Table column heading
                    'type' => "select",
                    'name' => 'event_type_id', // the column that contains the ID of that connected entity;
                    'entity' => 'event_type', // the method that defines the relationship in your Model
                    'attribute' => "name", // foreign key attribute that is shown to user
                    'model' => "App\Models\EventType", // foreign key model
                ],
                [
                    'name' => 'date', // a unique name for this field
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
                    'name'  => 'comment',
                    'label' => trans('fields.comment'),
                    'type'  => 'textarea',
                ],
            ]);
        }
        // Fields

        // add asterisk for fields that are required in EventRequest
        $this->crud->setRequiredFields(EmployEventRequest::class, 'create');
//        $this->crud->setRequiredFields(EmployEventRequest::class, 'edit');

        //
        if(backpack_user()->role == User::ROLE_COMPANY) {
            $employees = backpack_user()->company->employees;
            foreach($employees as $employee){
                $employee_id = $employee->id;
                EmployeeEvent::where('employee_id', $employee_id)->update(['company_is_read'=>1]);
            }
        }else if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $employee_id = backpack_user()->employee->id;
            EmployeeEvent::where('employee_id', $employee_id)->update(['employee_is_read'=>1]);
        }
    }

    public function store(EmployEventRequest $request)
    {
        $request->request->set('employee_id', backpack_user()->employee->id);
		$request->request->set('employee_is_read', 1);
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry

        // Send notification from employee to company
        $notification = [
            'title' => "Biodactil",
            'sound' => true,
            'body' => trans('fields.notification_request_other_event'),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'icon' => 'fcm_push_icon'
        ];

        $data = [
            "type" => "other"
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

    public function update(EmployEventRequest $request, EmployeeEvent $employeeEvent)
    {
        $id = $request->id;
        $before_status = EmployeeEvent::find($id)->status;
        $after_status = $request->status;

        if ($before_status != $after_status) {
            // if status is changed, company --> employee
            EmployeeEvent::find($request->id)->update(['employee_is_read'=>0]);
            // Send notification from employee to company
            $notification = [
                'title' => "Biodactil",
                'sound' => true,
                'body' => trans('fields.notification_event_changed'),
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'icon' => 'fcm_push_icon'
            ];

            $data = [
                "type" => "other"
            ];
			if(Employee::find($request->employee_id)->webToken->first()){
				$web_token = Employee::find($request->employee_id)->webToken->first()->token;
				$webNotification = [
					'to' => $web_token,
					'notification' => $notification,
					'data' => $data
				];
				$web_notification_res = json_decode(Notification::web_notification($webNotification), true);
			}
        }

         // your additional operations before save here
         $redirect_location = parent::updateCrud($request);
         // your additional operations after save here
         // use $this->data['entry'] or $this->crud->entry
         return $redirect_location;
    }
}
