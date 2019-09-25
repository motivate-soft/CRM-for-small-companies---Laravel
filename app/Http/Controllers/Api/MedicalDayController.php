<?php

namespace App\Http\Controllers\Api;

use App\Models\App;
use App\Models\Company;
use App\Models\EventMandatoryType;
use App\Models\MedicalDay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MedicalDayController extends Controller
{
    public function addMedical(Request $request)
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
//            'comments' => 'required',
            'data' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png,gif,svg'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 400);
        }

        $res = $request->data;
        $fields = json_decode($res, true);

        $validator = Validator::make($fields, [
            'date' => 'required|date|date_format:d/m/Y',
            'leave_type' => 'required|in:leave,discharge',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 400);
        }

        $company_id = $company->id;
        $employee_id = $app->employee->id;

        $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $fields['date'])));

       $comments = '';

		if (isset($fields['comments'])) {
			$comments = $fields['comments'];
		}
        $fileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/medical_day/', $fileName);
            $fileName = 'medical_day/' . $fileName;
        }

        $id = DB::table('medical_days')->insertGetId([
            'date' => $start_date,
            'photo' => $fileName,
            'comment' => $comments,
			'leave_type' => $fields['leave_type'],
            'employee_id' => $employee_id,
            'event_type_id' => EventMandatoryType::where('company_id', $company_id)->where('type', 'medical_day')->first()->id,
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
        ]);

        $model = MedicalDay::find($id);

        $result['id'] = $model->id;
        $result['datetime'] = date('d/m/Y h:i:s',strtotime($model->created_at));
        $result['date'] = date('d/m/Y',strtotime($model->date));
        $result['leave_type'] = $model->leave_type;
        $result['comments'] = $model->comment;
        $result['image'] = $model->photo;
        return response()->json($result);
    }
}
