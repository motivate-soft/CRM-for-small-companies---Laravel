<?php

namespace App\Http\Controllers\Api;

use App\Models\App;
use App\Models\Company;
use App\Models\EventMandatoryType;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HolidayController extends Controller
{
    public function addVacation(Request $request)
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

        $res = $request->json()->all();
        $validator = Validator::make($request->json()->all(), [
            'from_date' => 'required|date',
            'end_date' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }


               $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $res['from_date'])));
        $end_date = date('Y-m-d', strtotime(str_replace('/', '-', $res['end_date'])));


        $comment = '';
        if (isset($res['comments'])) {
            $comment = $res['comments'];
        }
        $vacation = new Holiday();
        $vacation->start_date = $start_date;
        $vacation->end_date = $end_date;
        $vacation->comment = $comment;
        $vacation->employee_id = $app->employee->id;
        $vacation->event_type_id = EventMandatoryType::where('company_id', $company->id)->where('type', 'holiday')->first()->id;
        $vacation->save();

        $result = [
            'id' => $vacation->id,
            'datetime' => date('d/m/Y h:i:s',strtotime($vacation->created_at)),
            'from_date' => date('d/m/Y',strtotime($vacation->start_date)),
            'end_date' => date('d/m/Y',strtotime($vacation->end_date)),
        ];

        if ($vacation->comment) {
            $result['comments'] = $vacation->comment;
        }

        return response()->json($result);
    }

    public function cancelVacation(Request $request, $id)
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

        if (Holiday::find($id)) {
            $holiday = Holiday::find($id);

            $start_date = $holiday->start_date;
            $now = Carbon::now()->format('Y-m-d');

            if ($start_date < $now) {
                return response()->json([
                    "error" => "You can't cancel this vacation."
                ], 422);
            } else {
                $holiday->cancel_state = 'requested';
                $holiday->save();
                return response()->json([
                    'status' => true,
                ]);
            }
        } else {
            return response()->json([
                "error" => "Vacation id doesn't exist for this value."
            ], 400);
        }
    }
}
