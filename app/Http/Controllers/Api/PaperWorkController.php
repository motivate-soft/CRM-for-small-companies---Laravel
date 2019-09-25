<?php

namespace App\Http\Controllers\Api;

use App\Models\Absence;
use App\Models\App;
use App\Models\Company;
use App\Models\EventMandatoryType;
use App\Models\Expense;
use App\Models\Holiday;
use App\Models\Incident;
use App\Models\MedicalDay;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Collection;
use Illuminate\Support\Str;

class PaperWorkController extends Controller
{
    public function getPaperwork(Request $request)
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


        $validator = Validator::make($request->all(), [
            'page' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 400);
        }


        $company_id = $company->id;
        $employee_id = $app->employee->id;
		

        $holiday_type = EventMandatoryType::where('type', 'holiday')->where('company_id', $company_id)->first();
        $absence_type = EventMandatoryType::where('type', 'absence')->where('company_id', $company_id)->first();
        $expense_type = EventMandatoryType::where('type', 'expense')->where('company_id', $company_id)->first();
        $medical_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', $company_id)->first();
        $incident_type = EventMandatoryType::where('type', 'incident')->where('company_id', $company_id)->first();


        $holidays = Holiday::where('employee_id', $employee_id)->get();
        $absence = Absence::where('employee_id', $employee_id)->get();
        $expense = Expense::where('employee_id', $employee_id)->get();
        $medical = MedicalDay::where('employee_id', $employee_id)->get();
        $incident = Incident::where('employee_id', $employee_id)->get();

        $collection = collect();
        $collection = $collection->merge($holidays->map(function ($item) use ($holiday_type){
            $item['type'] = 'vacation';
            unset($item['event_type_id']);
            //$item['from_date'] = date('d/m/Y',strtotime($item->start_date));
            //$item['end_date'] = date('d/m/Y',strtotime($item->end_date));
            //$item['datetime'] = date('d/m/Y',strtotime($item->created_at));

            if ($holiday_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
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
        $collection = $collection->merge($absence->map(function ($item) use ($absence_type){
            $item['type'] = 'absence';
            //$item['from_date'] = date('d/m/Y',strtotime($item->start_date));
            //$item['end_date'] = date('d/m/Y',strtotime($item->end_date));
           // $item['datetime'] = date('d/m/Y h:i:s',strtotime($item->created_at));
            unset($item['event_type_id']);

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
        $collection = $collection->merge($expense->map(function ($item) use ($expense_type){
            $item['type'] = 'expense';
           // $item['date'] = date('d/m/Y',strtotime($item->date));
           // $item['datetime'] = date('d/m/Y h:i:s',strtotime($item->created_at));
            unset($item['event_type_id']);

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
        $collection = $collection->merge($medical->map(function ($item) use ($medical_type){
            $item['type'] = 'leave';
           // $item['date'] = date('d/m/Y',strtotime($item->date));
           // $item['datetime'] = date('d/m/Y h:i:s',strtotime($item->created_at));
            unset($item['event_type_id']);

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
        $collection = $collection->merge($incident->map(function ($item) use ($incident_type){
            $item['type'] = 'incident';
          //  $item['datetime'] = date('d/m/Y h:i:s',strtotime($item->created_at));
            unset($item['event_type_id']);

            if ($incident_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$incident_type->has_file) {
                unset($item['photo']);
            }
            if (!$incident_type->has_comment) {
                unset($item['comment']);
            }
            if (!$incident_type->has_amount) {
                unset($item['amount']);
            }

            return $item;
        }));

        $page_size = config('backpack_config.paperwork_page_size');

        $total_count = $collection->count();
        $next_page_state = ($total_count - ($request->page) * $page_size) > 0 ? 1 : 0;

        $pagination = $collection->sortByDesc(function ($item) {
            return $item->created_at;
        })->values()->forpage($request->page, $page_size)->toArray();


        $res['data'] = array_values($pagination);
        $res['page'] = $request->page;
        $res['more'] = $next_page_state;

        return response()->json($res, 200);
    }
}
