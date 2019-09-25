<?php

namespace App\Http\Controllers\Admin;

use App\GlobalConstant;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeHolidayDays;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\EmployeeStoreRequest as StoreRequest;
use App\Http\Requests\EmployeeUpdateRequest as UpdateRequest;
use Prologue\Alerts\Facades\Alert;

/**
 * Class EmployeeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class EmployeeCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Employee');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/employees');
        $this->crud->setEntityNameStrings(trans('fields.employee'), trans('fields.employees'));
        $this->crud->addButtonFromModelFunction('line', 'calendar', 'get_calendar_button', 'end'); // add a button whose HTML is returned by a method in the CRUD model
        $this->crud->addButtonFromModelFunction('line', 'password', 'change_password_button');
//        $this->crud->setEditView('change_password');
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->enableExportButtons();

        $this->crud->allowAccess('show');

        $this->crud->setColumns([
            [
                'label'=> trans('fields.name'),
                'type' => "select",
                'name' => 'user_id', // the column that contains the ID of that connected entity;
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
//                'model' => "App\Users", // foreign key model
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('name', 'like', '%'.$searchTerm.'%');
                    });
                },
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    $query->leftJoin('users', 'users.id', '=', 'employees.user_id')
                        ->orderBy('users.name', $columnDirection)->select('employees.*');
                }
            ],
            [
                'name'  => 'employee_uid',
                'label' => trans('fields.employee_id'),
                'type'  => 'text',
                'orderable' => false,
            ],
            [
                'name'  => 'email',
                'label' => trans('fields.email'),
                'type'  => 'email',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('email', 'like', '%'.$searchTerm.'%');
                    });
                }
            ],
            [
                'name'  => 'photo',
                'label' => trans('fields.photo'),
                'type'  => 'image',
                'orderable' => false,
            ],
            [
                'name'  => 'nif',
                'label' => trans('fields.nif'),
                'type'  => 'text',
                'orderable' => false,
            ],
            [
                'name'  => 'affiliation',
                'label' => trans('fields.affiliation'),
                'type'  => 'text',
                'orderable' => false,
            ],
            [
                'name'  => 'holidays',
                'label' => trans('fields.holiday_days'),
                'type'  => 'text',
                'orderable' => true,
            ],
            [
                'label' => trans('fields.work_centers'),
                'type' => "select",
                'name' => 'workcenter_id', // the method that defines the relationship in your Model
                'entity' => 'workcenter', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Workcenter", // foreign key model
                'orderable' => false,
            ],
            [
                'label' => trans('fields.departments'),
                'type' => "select",
                'name' => 'department_id', // the method that defines the relationship in your Model
                'entity' => 'department', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Department", // foreign key model
                'orderable' => false,
            ],
        ]);



        // Fields (both - create, update)
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ]
        ]);
        // Fields (only create)
        $this->crud->addField(
            [
                'name'  => 'employee_uid',
                'label' => trans('fields.employee_id'),
                'type'  => 'text',
                'value' => $this->employee_uid(),
                'hint'  => trans('fields.employee_id_hint'),
            ], 'create')->afterField('name');

        // Fields (only update)
        $this->crud->addField(
            [
                'name'  => 'employee_uid',
                'label' => trans('fields.employee_id'),
                'type'  => 'text',
                'hint'  => trans('fields.employee_id_hint'),
            ], 'update')->afterField('name');

        // Fields (both - create, update)
        $this->crud->addFields([
            [
                'name'  => 'email',
                'label' => trans('fields.email'),
                'type'  => 'email',
            ]
        ]);
        // Fields (only create)
        $this->crud->addField(
            [
                'name'  => 'password',
                'label' => trans('fields.password'),
                'type'  => 'password',
            ],'create')->afterField('email');
        $this->crud->addField(
            [
                'name'  => 'password_confirmation',
                'label' => trans('fields.password_confirmation'),
                'type'  => 'password',
            ], 'create')->afterField('password');

        // Fields (both - create, update)
        $this->crud->addFields([
            [
                'label' => trans('fields.photo'),
                'name' => "photo",
                'type' => 'image',
                'upload' => true,
                'crop' => false,
                'aspect_ratio' => 1,
            ],
            [
                'name'  => 'nif',
                'label' => trans('fields.nif'),
                'type'  => 'text',
            ],
            [
                'name'  => 'affiliation',
                'label' => trans('fields.affiliation'),
                'type'  => 'text',
            ],
            [
                'label' => trans('fields.work_centers'),
                'type' => 'select2',
                'name' => 'workcenter_id', // the method that defines the relationship in your Model
                'entity' => 'workcenter', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Workcenter", // foreign key model
            ],
            [
                'label' => trans('fields.departments'),
                'type' => 'select2',
                'name' => 'department_id', //  the db column for the foreign key
                'entity' => 'department', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Department", // foreign key model
            ],
            [
                'name' => 'user_id',
                'type' => 'hidden'
            ],
        ]);

        $this->crud->addField(
            [
                'label'     => trans('fields.working_days'),
                'type'      => 'working_day',
                'name'      => 'workingDays',
                'entity'    => 'workingDays',
                'attribute' => 'id',
                'model'     => "App\Models\WorkingDays",
                'pivot'     => true,
            ], 'update')->afterField('affiliation');

        $this->crud->addField(
            [
                'name' => 'holidays',
                'label' => trans('fields.number_holidays'),
                'type'  => 'number'
            ], 'update')->afterField('affiliation');


        // add asterisk for fields that are required in EmployeeRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        $max_employees_Plan = Company::getMaxEmployees();
        if ($max_employees_Plan) {
            if ($max_employees_Plan['billing_status'] == GlobalConstant::COMPANY_PLAN_STATUS_UNLIMITED) {

            } else {
                $num_employees = backpack_user()->company->employees->count();
                if ($num_employees >= $max_employees_Plan['employee_num']) {
                    Alert::error(trans('fields.company_exceeded_employee_msg'))->flash();
                    return redirect()->back();
                }
            }
        } else {
            return redirect()->back();
        }
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();
        $holiday_days = backpack_user()->holiday_days;
        $user = $user->create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'role'      => User::ROLE_EMPLOYEE,
            'status'    => User::STATUS_APPROVED,
            'holiday_days' => $holiday_days
        ]);

        $request->request->set('user_id', $user->id);
        $request->request->set('company_id', backpack_user()->company->id);
		$request->request->set('language_id', backpack_user()->company->language_id);
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);

        // default holiday days
        $working_days = User::find(backpack_user()->id)->workingDays->pluck('id');
        $user_id = $user->id;
        User::find($user_id)->workingDays()->attach($working_days);

//        $employee_id = Employee::where('user_id', $user_id)->first()->id;
        $employee_holiday_days = new EmployeeHolidayDays();
        $employee_holiday_days->user_id = $user_id;
        $employee_holiday_days->spend_holidays = 0;
        $employee_holiday_days->year = date('Y');
        $employee_holiday_days->save();


        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
//        $this->handlePasswordInput($request);

        $user_model_fqn = config('backpack.base.user_model_fqn');

        $user = $user_model_fqn::find($request->user_id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->holiday_days = $request->holidays;
//        echo $request->holidays;
//        return "AAA";
        if($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function destroy($id)
    {
        $user_id = Employee::find($id)->user_id;

        $redirect_location = parent::destroy($id);
        User::destroy($user_id);

        return $redirect_location;
    }

    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', bcrypt($request->input('password')));
        } else {
            $request->request->remove('password');
        }
    }
	
	private function employee_uid()
    {
        return Employee::orderBy('employee_uid', 'desc')->first()->employee_uid + 1;
    }
}
