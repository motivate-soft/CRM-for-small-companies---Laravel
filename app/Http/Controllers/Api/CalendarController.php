<?php

namespace App\Http\Controllers\Api;

use App\Models\Absence;
use App\Models\App;
use App\Models\Company;
use App\Models\EmployeeEvent;
use App\Models\EventMandatoryType;
use App\Models\EventType;
use App\Models\Expense;
use App\Models\Holiday;
use App\Models\MedicalDay;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use function PHPSTORM_META\type;
use Illuminate\Support\Str;

class CalendarController extends Controller
{
    public function getSummary(Request $request)
    {

        $header = $request->header('Authorization');

        if ($header == "") {
            return response()->json([
                "error" => "Not set authorization header"
            ], 401);
        }

        $array_header = explode(":", $header);
        if (count($array_header) != 2) {
            return response()->json([
                "error" => "Invalid Authorization header"
            ], 401);
        }

        $user_id = Str::substr($array_header[0], 10);
        $access_company_token = $array_header[1];

        $company = Company::where('access_company_token', $access_company_token)->first();
        $app = App::where('app_id', $user_id)->first();

        if ($app == null || $company == null) {
            return response()->json([
                "error" => "Unauthorized"
            ], 401);
        }

        if ($app->employee->company->id !== $company->id) {
            return response()->json([
                'error' => 'App ID is not belong to the company'
            ], 401);
        }


        $workcenter = $app->employee->workcenter;
        $departments = $app->employee->department;


        /* Calendar Event */
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

        $calendar_holidays = $array_holidays->toArray();
        $calendar_events = $array_events->toArray();



        $res_array = array();
        foreach ($calendar_holidays as $item) {
            $temp = array();
            $temp['start_date'] = $item['start_date'];
            $temp['end_date'] = $item['end_date'];
            $temp['name'] = $item['name'];
//            $temp['comment'] = $item['comment'];
            $temp['type'] = 'national_holiday';
            array_push($res_array, $temp);
        }

        foreach ($calendar_events as $item) {
            $temp = array();
            $temp['start_date'] = $item['start_date'];
            $temp['end_date'] = $item['end_date'];
            $temp['name'] = $item['name'];
//            $temp['comment'] = $item['comment'];
            $temp['type'] = 'company_event';
            array_push($res_array, $temp);
        }
        /* End Calendar Event */

        $company_id = $company->id;
        $holiday_type = EventMandatoryType::where('type', 'holiday')->where('company_id', $company_id)->first();
        $absence_type = EventMandatoryType::where('type', 'absence')->where('company_id', $company_id)->first();
        $expense_type = EventMandatoryType::where('type', 'expense')->where('company_id', $company_id)->first();
        $medical_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', $company_id)->first();
        $incident_type = EventMandatoryType::where('type', 'incident')->where('company_id', $company_id)->first();

        /* Employee Event */
        $employee_holidays = Holiday::where('employee_id', $app->employee->id)->get();
        $employee_absence = Absence::where('employee_id', $app->employee->id)->get();
        $employee_medical = MedicalDay::where('employee_id', $app->employee->id)->get();
        $employee_expense = Expense::where('employee_id', $app->employee->id)->get();

        $employee_others = EmployeeEvent::where('employee_id', $app->employee->id)->get();



        $collection = collect();
        $collection = $collection->merge($employee_holidays->map(function ($item) use ($holiday_type){
            $item['type'] = 'holiday';
            unset($item['event_type_id']);
            unset($item['id']);

            if ($holiday_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
                if ($item['status'] == 'pending') {
                    $item['type'] = 'pending';
                }
                if ($item['status'] == 'approved') {
                    $item['type'] = 'vacation';
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$holiday_type->has_file) {
                unset($item['doc']);
            }
            if (!$holiday_type->has_comment) {
                unset($item['comment']);
            }
            if (!$holiday_type->has_amount) {
                unset($item['amount']);
            }
            return $item;
        }));
        $collection = $collection->merge($employee_absence->map(function ($item) use ($absence_type){
            $item['type'] = 'absence';
            unset($item['event_type_id']);
            unset($item['id']);

            if ($absence_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$absence_type->has_file) {
                unset($item['doc']);
            }
            if (!$absence_type->has_comment) {
                unset($item['comment']);
            }
            if (!$absence_type->has_amount) {
                unset($item['amount']);
            }

            return $item;
        }));
        $collection = $collection->merge($employee_expense->map(function ($item) use ($expense_type){
            $item['type'] = 'expense';
            unset($item['event_type_id']);
            unset($item['id']);

            if ($expense_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$expense_type->has_file) {
                unset($item['photo']);
            }
            if (!$expense_type->has_comment) {
                unset($item['comment']);
            }
            if (!$expense_type->has_amount) {
                unset($item['amount']);
            }

            return $item;
        }));
        $collection = $collection->merge($employee_medical->map(function ($item) use ($medical_type){
            $item['type'] = 'leave';
            unset($item['event_type_id']);
            unset($item['id']);

            if ($medical_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$medical_type->has_file) {
                unset($item['photo']);
            }
            if (!$medical_type->has_comment) {
                unset($item['comment']);
            }
            if (!$medical_type->has_amount) {
                unset($item['amount']);
            }
            return $item;
        }));

        $collection = $collection->merge($employee_others->map(function ($item) {
            $event_type = EventType::find($item['event_type_id']);
            $item['type'] = $event_type->name;
            unset($item['event_type_id']);
            unset($item['id']);

            if ($event_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }
            if (!$event_type->has_file) {
                unset($item['file']);
            }
            if (!$event_type->has_comment) {
                unset($item['comment']);
            }
            if (!$event_type->has_amount) {
                unset($item['amount']);
            }
            return $item;
        }));


        /* End Employee Event */

        if($departments) {
            $workingdays = $departments->workingDays;
            $working_day_list = $this->get_work_days($workingdays);
        } else {
            $working_day_list = [1, 1, 1, 1, 1, 1, 1];
        }

        $res_array = array_merge($res_array, $collection->toArray());
        $data['working_days'] = $working_day_list;
        $data['event_days'] = $res_array;


        return response()->json($data);
    }

    public function distinct_array($array)
    {
        $array = array_map('json_encode', $array);
        $array = array_unique($array);
        $array = array_map('json_decode', $array);
        return $array;
    }

    private function get_work_days($arr) {
        $week_array = [
            0,0,0,0,0,0,0
        ];
        foreach ($arr as $item) {
            $week_array[$item['id']-1] = 1;
        }
        return $week_array;
    }

    private function merge_array($res, $events, $type) {
        foreach ($events as $event) {
            $event['type'] = $type;
            array_push($res, $event);
        }

        return $res;
    }
}
