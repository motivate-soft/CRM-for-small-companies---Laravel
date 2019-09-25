<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\WorkingPlaceRequest;
use App\Models\Employee;
use App\Models\EmployeeEvent;
use App\Models\EmployeeHolidayDays;
use App\Models\EventType;
use App\Models\Holiday;
use App\Models\Company;
use App\Models\WorkingPlaceHoliday;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkingplaceHolidaysCrudController extends CrudController
{
    public function setup()
    {
        /*
       |--------------------------------------------------------------------------
       | CrudPanel Basic Information
       |--------------------------------------------------------------------------
       */
        $this->crud->setModel('App\Models\WorkingPlaceHoliday');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/workingplace_hollidays');
        $this->crud->setEntityNameStrings(trans('fields.holiday'), trans('fields.holidays'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->enableExportButtons();

        $this->crud->allowAccess('show');

        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text'
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
//            [
//                'name' => 'status',
//                'label' => trans('fields.status'),
//                'type' => 'select_from_array',
//                'options' => ['pending' => trans('fields.pending'), 'approved' => trans('fields.approved'), 'rejected' => trans('fields.rejected')],
//                'allows_null' => false,
//            ],
            [
                // n-n relationship (with pivot table)
                'label' => trans('fields.work_centers'),
                'type' => "select_multiple",
                'name' => 'workcenters', // the method that defines the relationship in your Model
                'entity' => 'workcenters', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Workcenter", // foreign key model
            ],
            [
                // n-n relationship (with pivot table)
                'label' => trans('fields.departments'),
                'type' => "select_multiple",
                'name' => 'departments', // the method that defines the relationship in your Model
                'entity' => 'departments', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Department", // foreign key model
            ],
        ]);

        // Fields
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
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
//            [
//                'name' => 'status',
//                'label' => trans('fields.status'),
//                'type' => 'select_from_array',
//                'options' => ['pending' => trans('fields.pending'), 'approved' => trans('fields.approved'), 'rejected' => trans('fields.rejected')],
//                'allows_null' => false,
//            ],
            [
                'label' => trans('fields.work_centers'),
                'type' => 'select2_multiple',
                'name' => 'workcenters', // the method that defines the relationship in your Model
                'entity' => 'workcenters', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Workcenter", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'select_all' => true, // show Select All and Clear buttons?
            ],
            [
                'label' => trans('fields.departments'),
                'type' => 'select2_multiple',
                'name' => 'departments', // the method that defines the relationship in your Model
                'entity' => 'departments', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Department", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'select_all' => true, // show Select All and Clear buttons?
            ],
        ]);

        // add asterisk for fields that are required in EmployeeRequest
        $this->crud->setRequiredFields(WorkingPlaceRequest::class, 'create');
        $this->crud->setRequiredFields(WorkingPlaceRequest::class, 'edit');
    }

    public function store(WorkingPlaceRequest $request)
    {
        $request->request->set('company_id', backpack_user()->company->id);
        $redirect_location = parent::storeCrud($request);

        /*$company_id = backpack_user()->company->id;
        $employee_ids = Employee::where('company_id', $company_id)->get()->pluck('id');
        foreach($employee_ids as $employee_id){
            $spend_holidays = $this->get_spend_holidays($employee_id);
            $user_id = Employee::find($employee_id)->user_id;
            EmployeeHolidayDays::where('user_id', $user_id)->update(['spend_holidays'=>$spend_holidays]);
        }*/

        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(WorkingPlaceRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here

        /*$company_id = backpack_user()->company->id;
        $employee_ids = Employee::where('company_id', $company_id)->get()->pluck('id');
        foreach($employee_ids as $employee_id){
            $spend_holidays = $this->get_spend_holidays($employee_id);
            $user_id = Employee::find($employee_id)->user_id;
            EmployeeHolidayDays::where('user_id', $user_id)->update(['spend_holidays'=>$spend_holidays]);
        }*/
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function destroy($id)
    {
        // your additional operations before save here
        $redirect_location = parent::destroy($id);
        // your additional operations after save here

        /*$company_id = backpack_user()->company->id;
        $employee_ids = Employee::where('company_id', $company_id)->get()->pluck('id');
        foreach($employee_ids as $employee_id){
            $spend_holidays = $this->get_spend_holidays($employee_id);
            $user_id = Employee::find($employee_id)->user_id;
            EmployeeHolidayDays::where('user_id', $user_id)->update(['spend_holidays'=>$spend_holidays]);
        }*/
        // use $this->data['entry'] or $this->crud->entry
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
        return Employee::find($employee_id)->user->workingDays->pluck('id')->toarray();
    }

    // get banking days approved
    public function get_banking_days($employee_id){
        $result = [];
        $employee_events = EmployeeEvent::where('employee_id', $employee_id)->where('status', 'approved')->get();
        foreach ($employee_events as $item){
            $event_type_id = $item['event_type_id'];
            $is_working_day = EventType::find($event_type_id)->is_working_day;
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
        $workcenter_holidays = collect();
        if(Employee::find($employee_id)->department){
            $department_holidays = Employee::find($employee_id)->department->holidays; // get national department holiday for employee
        }

        if(Employee::find($employee_id)->workcenter){
            $workcenter_holidays = Employee::find($employee_id)->workcenter->holidays; // get national workcenter holiday for employee
        }
        $national_holidays = $department_holidays->merge($workcenter_holidays);
        $result = [];
        foreach($national_holidays as $item){
            $result = array_merge($result, $this->getDatesFromRange($item['start_date'], $item['end_date']));
        }
        return $result;
    }
}
