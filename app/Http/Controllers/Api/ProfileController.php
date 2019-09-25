<?php

namespace App\Http\Controllers\Api;

use App\Models\Action;
use App\Models\App;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeHolidayDays;
use App\Models\Holiday;
use App\Models\HolidaySetting;
use App\Models\WorkingPlaceHoliday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function profile(Request $request)
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


        /* Get Working Hours */
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d h:i:s');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d h:i:s');

        $monthly_items = $app->employee->actions->where('datetime', '>=', $startDate)->where('datetime', '<=', $endDate)->reverse()->values();
        $month_hours = $this->calcWorkingHours($monthly_items);


        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d h:i:s');
        $endDate = Carbon::now()->endOfWeek()->format('Y-m-d h:i:s');

        $weekly_item = $app->employee->actions->where('datetime', '>=', $startDate)->where('datetime', '<=', $endDate)->reverse()->values();
        $week_hours = $this->calcWorkingHours($weekly_item);


        $startDate = Carbon::now()->startOfYear()->format('Y-m-d h:i:s');
        $endDate = Carbon::now()->endOfYear()->format('Y-m-d h:i:s');

        $year_item = $app->employee->actions->where('datetime', '>=', $startDate)->where('datetime', '<=', $endDate)->reverse()->values();
        $year_hours = $this->calcWorkingHours($year_item);

        /* End Working hours */


        /* Vacation Days of employee */

        $employee_entered_year = Carbon::createFromFormat('Y-m-d H:i:s', $app->employee->created_at)->year;
        $current_year = Carbon::now()->format('Y');

        $vacation_days = 0;
        $department = $app->employee->department;

        if ($employee_entered_year == $current_year) {
            $vacation_days = $app->employee->holiday_days ?  $app->employee->holiday_days : 0;
        } else {
            if ($department) {
                $default_holiday_item = HolidaySetting::where('department_id', $department->id)->where('year', date('Y'))->first();
                if ($default_holiday_item) {
                    $vacation_days = $default_holiday_item->default_holidays_per_employee;
                }
            }
        }


        $d_vacation_days_last_year = 0;
        if ($employee_entered_year == ($current_year - 1)) {
            $d_vacation_days_last_year = $app->employee->holiday_days ? $app->employee->holiday_days : 0;
        } else {
            if ($department) {
                $default_holiday_item_last_year = HolidaySetting::where('department_id', $department->id)->where('year', date('Y')-1)->first();
                if ($default_holiday_item_last_year) {
                    $d_vacation_days_last_year = $default_holiday_item_last_year->default_holidays_per_employee;
                }
            }
        }

        $expiring_days = 0;
        $employee_holidays_last_year_item = $app->employee->employeeHolidayDays->where('year', date('Y')-1)->first();
        if ($employee_holidays_last_year_item) {

            $rest_days_last_year = $d_vacation_days_last_year - $employee_holidays_last_year_item->spend_holidays;
            if ($department) {
                $default_holiday_item = HolidaySetting::where('department_id', $department->id)->where('year', date('Y'))->first();
                if ($default_holiday_item) {
                    if ($default_holiday_item->expire_date < date('Y-m-d') && $rest_days_last_year > 0) {
                        $expiring_days = $rest_days_last_year;
                        $expire_date = $default_holiday_item->exemployee_holiday_dayspire_date;
                    }
                }
            }
        }

        $spent_days_this_year = 0;

        if ($app->employee->employeeHolidayDays){
            try {
                $spent_days_this_year = $app->employee->employeeHolidayDays->where('year', date('Y'))->first()->spend_holidays;
            } catch (\Exception $exception) {

            }
        }

        $available_days = (int)$vacation_days + (int)$expiring_days - $spent_days_this_year;


        /*  Result response */
        $employee = $app->employee;
        $user = $employee->user;

        $data['name'] = $user->name;
        $data['image'] = $employee->photo;
        $data['hours_week'] = $week_hours;
        $data['hours_month'] = $month_hours;
        $data['hours_year'] = $year_hours;
        $data['vacation_days'] = $vacation_days;
        $data['days_available'] = $available_days > 0 ? $available_days : 0;
        if (isset($expire_date)) {
            $data['expire'] = $expire_date;
        }
        $data['expiring_days'] = $expiring_days;

        return response()->json($data);
    }


    public function getworkingplace($employee_id) {
        $workcenter = Employee::find($employee_id)->workcenter;
        $departments = Employee::find($employee_id)->department;


        /* Calendar Event */
        $array_holidays = collect();

        if ($workcenter) {
            $array_holidays = $workcenter->holidays;
        }

        if ($departments) {
            $d_holidays = $departments->holidays;
            $array_holidays = $array_holidays->merge($d_holidays);
        }

        return $array_holidays;
    }

    public function calcWorkingHours($item)
    {
        $time_total = 0;

        if(count($item) > 0) {
            $seconds = 0;
            for ($i = 0; $i < count($item); $i++) {

                if($item[$i]->option && $item[$i]->option->type == 'in' && isset($item[$i + 1]) && $item[$i + 1]->option->type == 'out' && Carbon::parse($item[$i]->datetime)->toDateString() == Carbon::parse($item[$i + 1]->datetime)->toDateString()) {
                    $seconds += (int)Carbon::parse($item[$i]->datetime)->diffInSeconds(Carbon::parse($item[++$i]->datetime));
                }
            }

            $time_total += $seconds;

            $time = Carbon::now()->addSeconds($time_total);

            $hours = $time->diffInHours();
            $minutes = $time->subHours($hours)->diffInMinutes();
            $min = (int)$minutes/60;

            $res_hours = (int)$hours + round($min, 1);

            return $res_hours;
        }else {
            return $time_total;
        }
    }

    public function changePicture(Request $request)
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

        $user_id = $array_header[0];
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


        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 400);
        }


        $fileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/employees/', $fileName);
            $fileName = 'employees/' . $fileName;
        }
        DB::table('employees')->where('id', $app->employee->id)->update([
            'photo' => $fileName
        ]);

        //$app->employee->photo = $fileName;
        //$app->employee->save();
        return response()->json([
            'status' => true,
        ]);
    }
}
