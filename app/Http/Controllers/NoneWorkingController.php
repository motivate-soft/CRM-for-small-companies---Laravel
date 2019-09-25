<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NoneWorkingController extends Controller
{

    public function index(Request $request)
    {
        $not_working_employees =  $this->getNotworkingEmployee();
        $data['not_working_employees'] = $not_working_employees;
        $data['title'] = trans('fields.none_working_employees');
        return view('none_working_employees', $data);
    }

    public function getNotworkingEmployee(){
        $current_date = Carbon::today();
        $current_day = Carbon::now()->format('Y-m-d');
        $employees = backpack_user()->company->employees;
        $notWorkingEmployees = [];
        $employee_ids = Employee::all()->pluck('id');
        foreach($employee_ids as $employee_id){
            // if employee is on vacation, skip
            if(Holiday::where('employee_id', $employee_id)->where('status', 'approved')->where('start_date', '<=', $current_date)->where('end_date', '>=', $current_date)->first()){
                continue;
            }

            // if employee is on company holiday, skip
            if(Employee::find($employee_id)->department){
                $department_holidays = Employee::find($employee_id)->department->holidays;
                $workcenter_holidays = Employee::find($employee_id)->workcenter->holidays;
                $today_department_holidays = $department_holidays->where('start_date', '<=', $current_date)
                    ->where('end_date', '>=', $current_date);
                $today_workcenter_holidays = $workcenter_holidays->where('start_date', '<=', $current_date)
                    ->where('end_date', '>=', $current_date);
                if($today_department_holidays->count() != 0 or $today_workcenter_holidays->count() != 0){
                    continue;
                }
            }
            // if employee is on working day, skip
            $working_days = Employee::find($employee_id)->user->workingDays->pluck('id')->toarray();
            $week_day = date('N',strtotime($current_date));
            if(!in_array($week_day, $working_days)){
                continue;
            }
            $count = Action::where('employee_id', $employee_id)->whereDate('datetime', $current_day)->whereHas('option', function ($query){
                $query->where('type', 'in');
            })->get()->count();
            if($count != 0) continue;
            $employee_name = Employee::find($employee_id)->user->name;
            $employee_photo = Employee::find($employee_id)->photo;
            $last_login_at = Employee::find($employee_id)->user->last_login_at;
            $last_check_in = '';
            $last_check_out = '';
            if($last_check_in = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                $query->where('type', 'in');
            })->first()){
                $last_check_in = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                    $query->where('type', 'in');
                })->first()->datetime;
            }
            if($last_check_out = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                $query->where('type', 'out');
            })->first()){
                $last_check_out = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                    $query->where('type', 'out');
                })->first()->datetime;
            }

            $employee = ['name'=>$employee_name,
                'photo'=>$employee_photo,
                'last_login_at'=>$last_login_at,
                'last_check_in' => $last_check_in,
                'last_check_out' => $last_check_out,
            ];
            array_push($notWorkingEmployees, $employee);
        }
        return $notWorkingEmployees;
    }
}
