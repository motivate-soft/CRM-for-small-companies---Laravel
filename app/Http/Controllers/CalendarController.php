<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventMandatoryType;
use App\Models\EventType;
use App\Models\Expense;
use App\Models\Holiday;
use App\Models\Incident;
use App\Models\MedicalDay;
use App\Models\WorkingPlaceHoliday;
use function foo\func;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Workcenter;
use App\Models\EmployeeEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CalendarController extends Controller
{
    private $model_id;

    public function getDepartmentCalendar($id)
    {
        $this->model_id = $id;
        $model = Department::find($id);
        $holidays = $model->holidays;
        $event = $model->events;


        $data['working_place_id'] = $id;
        $data['title'] = trans('fields.calendar');
        $data['holidays'] = $holidays;
        $data['events'] = $event;
        $data['workingplace_type'] = 'department';
        $data['language'] = backpack_user()->company->language->abbr;

        return view('company.calendar', $data);
    }

    public function save_event(Request $request)
    {
        $working_place_id = $request->working_place_id;
        $workingplace_type = $request->workingplace_type;
        $name = $request->name;
        $type = $request->type;
        $start_date = $request->start_date;
        $end_date = $start_date;
//        $end_date = date('Y-m-d', strtotime($start_date .' +1 day'));
        if ($type == 'event') {
            $event = new CalendarEvent();
            $event->name = $name;
            $event->start_date = $start_date;
            $event->end_date = $end_date;
            $event->company_id = backpack_user()->company->id;
            $event->save();

            if ($workingplace_type == 'department') {
                $data = array('department_id'=> $working_place_id, 'event_id' => $event->id);
                DB::table('department_has_events')->insert($data);
            } else {
                $data = array('workcenter_id'=> $working_place_id, 'event_id' => $event->id);
                DB::table('workcenter_has_events')->insert($data);
            }

            return $event->id;

        } else if ($type == 'holiday') {
            $holiday = new WorkingPlaceHoliday;
            $holiday->name = $name;
            $holiday->start_date = $start_date;
            $holiday->end_date = $end_date;
            $holiday->save();

            if ($workingplace_type == 'department') {
                $data = array('department_id'=> $working_place_id, 'holiday_id' => $holiday->id);
                DB::table('department_has_holidays')->insert($data);
            } else {
                $data = array('workcenter_id'=> $working_place_id, 'holiday_id' => $holiday->id);
                DB::table('workcenter_has_holidays')->insert($data);
            }

            return $holiday->id;
        }
    }

    public function update_event(Request $request) {

        $name = $request->name;
        $type = $request->type;
        $start_date = $request->start_date;
        $id = $request->id;
        if ($type == 'event') {
            $model = CalendarEvent::find($id);
        } else {
            $model = WorkingPlaceHoliday::find($id);
        }

        if (isset($request->end_date)) {
            $end_date = $request->end_date;
            $end_date = date('Y-m-d', strtotime($end_date .' -1 day'));
        } else {
//            $end_date = date('Y-m-d', strtotime($start_date .' +1 day'));
            $end_date = $start_date;
        }

        $model->start_date = $start_date;
        $model->end_date = $end_date;
        $model->name = $name;
        $model->start_date = $start_date;
        $model->save();
        return 'success';
    }

    public function delete_event(Request $request) {
        $working_place_id = $request->working_place_id;

        $id = $request->id;
        $type = $request->type;

        if ($type == 'event') {
            $model = CalendarEvent::find($id);
            $model->delete();
            DB::table('department_has_events')->where('department_id', $working_place_id)->where('event_id', $id)->delete();
        } else {
            $model = WorkingPlaceHoliday::find($id);
//            $model->delete();
            DB::table('department_has_holidays')->where('department_id', $working_place_id)->where('holiday_id', $id)->delete();
        }

        return 'success';
    }

    public function getWorkCenterCalendar($id)
    {
        $this->model_id = $id;
        $model = Workcenter::find($id);
        $holidays = $model->holidays;
        $event = $model->events;


        $data['working_place_id'] = $id;
        $data['title'] = trans('fields.calendar');
        $data['holidays'] = $holidays;
        $data['events'] = $event;
        $data['workingplace_type'] = 'workcenter';

        $data['language'] = backpack_user()->company->language->abbr;
        return view('company.calendar', $data);
    }

    public function getEmployerEvent($id)
    {
        $workcenter = Employee::find($id)->workcenter;
        $departments = Employee::find($id)->department;

        $array_holidays = collect();
        $array_events = collect();

        if ($workcenter) {
            $array_holidays = $workcenter->holidays;
            $array_events = $workcenter->events;
        }

        if ($departments) {
            $d_holidays = $departments->holidays;
            $array_holidays = $array_holidays->merge($d_holidays);
            $d_events = $departments->events;
            $array_events = $array_events->merge($d_events);
        }



        $holiday_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->company->id)->first();
        $absence_type = EventMandatoryType::where('type', 'absence')->where('company_id', backpack_user()->company->id)->first();
        $expense_type = EventMandatoryType::where('type', 'expense')->where('company_id', backpack_user()->company->id)->first();
        $medical_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->company->id)->first();
        $incident_type = EventMandatoryType::where('type', 'incident')->where('company_id', backpack_user()->company->id)->first();


         if ($holiday_type->has_confirmation) {
            $employee_holidays = Holiday::where('employee_id', $id)->where('status', 'approved')->where('cancel_state', '!=', 'approved')->get();
        } else {
            $employee_holidays = Holiday::where('employee_id', $id)->where('cancel_state', '!=', 'approved')->get();
        }

        if ($absence_type->has_confirmation) {
            $employee_absence = Absence::where('employee_id', $id)->where('status', 'approved')->get();
        } else {
            $employee_absence = Absence::where('employee_id', $id)->get();
        }

        if ($expense_type->has_confirmation) {
            $employee_expense = Expense::where('employee_id', $id)->where('status', 'approved')->get();
        } else {
            $employee_expense = Expense::where('employee_id', $id)->get();
        }

        if ($medical_type->has_confirmation) {
            $employee_medical = MedicalDay::where('employee_id', $id)->where('status', 'approved')->get();
        } else {
            $employee_medical = MedicalDay::where('employee_id', $id)->get();
        }


        $employee_others = EmployeeEvent::where('employee_id', $id)->where('status', 'approved')->get();


        $data['title'] = trans('fields.calendar');

        /* Calendar Event */
        $data['holidays'] = $array_holidays;
        $data['events'] = $array_events;


        /* Employee Event */
        $data['employee_holidays'] = $employee_holidays;
        $data['employee_absense'] = $employee_absence;
        $data['employee_medical'] = $employee_medical;
        $data['employee_expense'] = $employee_expense;
//        $data['employee_incident'] = $employee_incident;
        $data['employee_others'] = $employee_others;

        $data['event_mandatory_types'] = EventMandatoryType::all();
        $data['event_types'] = EventType::all();
        $data['language'] = Employee::find($id)->company->language->abbr;
		
        return view('employee.calendar', $data);
    }

    public function getEmployerCalendar()
    {
        $id = backpack_user()->employee->id;
//        $id = 295;
        $workcenter = Employee::find($id)->workcenter;
        $departments = Employee::find($id)->department;

        $array_holidays = collect();
        $array_events = collect();

        if ($workcenter) {
            $array_holidays = $workcenter->holidays;
            $array_events = $workcenter->events;
        }

        if ($departments) {
            $d_holidays = $departments->holidays;
            $array_holidays = $array_holidays->merge($d_holidays);
            $d_events = $departments->events;
            $array_events = $array_events->merge($d_events);
        }



        $holiday_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->employee->company->id)->first();
        $absence_type = EventMandatoryType::where('type', 'absence')->where('company_id', backpack_user()->employee->company->id)->first();
        $expense_type = EventMandatoryType::where('type', 'expense')->where('company_id', backpack_user()->employee->company->id)->first();
        $medical_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->employee->company->id)->first();


        if ($holiday_type->has_confirmation) {
            $employee_holidays = Holiday::where('status', 'approved')->where('cancel_state', '!=', 'approved')->get();
        } else {
            $employee_holidays = Holiday::all();
        }

        if ($absence_type->has_confirmation) {
            $employee_absence = Absence::where('status', 'approved')->get();
        } else {
            $employee_absence = Absence::all();
        }

        if ($expense_type->has_confirmation) {
            $employee_expense = Expense::where('status', 'approved')->get();
        } else {
            $employee_expense = Expense::all();
        }

        if ($medical_type->has_confirmation) {
            $employee_medical = MedicalDay::where('status', 'approved')->get();
        } else {
            $employee_medical = MedicalDay::all();
        }


        $employee_others = EmployeeEvent::where('employee_id', $id)->where('status', 'approved')->get();



        $data['title'] = trans('fields.calendar');

        /* Calendar Event */
        $data['holidays'] = $array_holidays;
        $data['events'] = $array_events;


        /* Employee Event */
        $data['employee_holidays'] = $employee_holidays;
        $data['employee_absense'] = $employee_absence;
        $data['employee_medical'] = $employee_medical;
        $data['employee_expense'] = $employee_expense;
        $data['employee_others'] = $employee_others;

        $data['event_mandatory_types'] = EventMandatoryType::all();

//        return $data['event_mandatory_types'];
        $data['event_types'] = EventType::all();
        $data['language'] = Employee::find($id)->company->language->abbr;

        return view('employee.calendar', $data);
    }
}
