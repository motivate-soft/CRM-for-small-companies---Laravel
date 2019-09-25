<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeHolidayDays;
use App\Models\Holiday;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    // employee -> company
    public function cancelVacation(Request $request)
    {
		Holiday::find($request->id)->update(['company_is_read'=>0]);
        $id = $request->id;
        $holiday = Holiday::find($id);
        $holiday->cancel_state = 'requested';
        $holiday->save();

        // Send notification from employee to company
        $notification = [
            'title' => "Biodactil",
            'sound' => true,
            'body' => trans('fields.notification_cancel_vacation'),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'icon' => 'fcm_push_icon'
        ];

        $data = [
            "type" => "vacation"
        ];
		if(Employee::find($holiday->employee_id)->company->web_token->first()){
			$web_token = Employee::find($holiday->employee_id)->company->web_token->first()->token;
			$webNotification = [
				'to' => $web_token,
				'notification' => $notification,
				'data' => $data
			];
			$web_notification_res = json_decode(Notification::web_notification($webNotification), true);
		}

        return redirect()->back();
    }

    // company -> employee
    public function approveCancelVacation(Request $request)
    {
		Holiday::find($request->id)->update(['employee_is_read'=>0]);
        $id = $request->id;
        $holiday = Holiday::find($id);
        $holiday->cancel_state = 'approved';
        $holiday->save();

        $status = Holiday::find($id)->status;
        if ($status == 'approved') {
            $add_holiday_days = Holiday::find($id)->real_holiday_days;
            $user_id = Employee::find($holiday->employee_id)->user_id;
            if(EmployeeHolidayDays::where('user_id', $user_id)->where('year', date('Y'))->first()){
                $spend_holidays = EmployeeHolidayDays::where('user_id', $user_id)->first()->spend_holidays;
                $spend_holidays = $spend_holidays - $add_holiday_days;
                EmployeeHolidayDays::where('user_id', $user_id)->update(['spend_holidays'=>$spend_holidays]);
            }else{
                $employee_holiday_days = new EmployeeHolidayDays();
                $employee_holiday_days->user_id = $user_id;
                $employee_holiday_days->spend_holidays = 0;
                $employee_holiday_days->year = date('Y');
                $employee_holiday_days->save();
            }
        }

        // Send notification from company to employee
        $notification = [
            'title' => "Biodactil",
            'sound' => true,
            'body' => trans('fields.notification_approved_cancel_vacation'),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'icon' => 'fcm_push_icon'
        ];

        $data = [
            "type" => "vacation"
        ];
		if(Employee::find($holiday->employee_id)->webToken->first()){
			$web_token = Employee::find($holiday->employee_id)->webToken->first()->token;
			$webNotification = [
				'to'        => $web_token,
				'notification' => $notification,
				'data' => $data
			];
        	$web_notification_res = json_decode(Notification::web_notification($webNotification), true);
		}
		
        return redirect()->back();
    }

    // company -> employee
    public function rejectVacation(Request $request)
    {
		Holiday::find($request->id)->update(['employee_is_read'=>0]);
        $id = $request->id;
        $holiday = Holiday::find($id);
        $holiday->cancel_state = 'rejected';
        $holiday->save();

        // Send notification from company to employee
        $notification = [
            'title' => "Biodactil",
            'sound' => true,
            'body' => trans('fields.notification_reject_vacation'),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'icon' => 'fcm_push_icon'
        ];

        $data = [
            "type" => "vacation"
        ];
		if(Employee::find($holiday->employee_id)->webToken->first()){
			$web_token = Employee::find($holiday->employee_id)->webToken->first()->token;
			$webNotification = [
				'to'        => $web_token,
				'notification' => $notification,
				'data' => $data
			];

			$web_notification_res = json_decode(Notification::web_notification($webNotification), true);
		}
		
        return redirect()->back();
    }
}
