<?php

namespace App\Http\Controllers\Api;

use App\Models\App;
use App\Models\Company;
use App\Models\EventMandatoryType;
use App\Models\Incident;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class IncidentController extends Controller
{
    public function addIncident(Request $request)
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
            'image' => 'mimes:jpeg,jpg,png,gif,svg'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 400);
        }

        $res_data = $request->data;
        $fields = json_decode($res_data, true);
        $validator = Validator::make($fields, [
            'comments' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 400);
        }

        $company_id = $company->id;
        $employee_id = $app->employee->id;

        $comment = $fields['comments'];

        $fileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/incidents/', $fileName);
            $fileName = 'incidents/' . $fileName;
        }

        $id = DB::table('incident')->insertGetId([
            'photo' => $fileName,
            'comment' => $comment,
            'employee_id' => $employee_id,
            'event_type_id' => EventMandatoryType::where('company_id', $company_id)->where('type', 'incident')->first()->id,
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
        ]);

        $inserted_model = Incident::find($id);

        return response()->json([
            'id' => $inserted_model->id,
            'datetime' => date('d/m/Y h:i:s', strtotime($inserted_model->created_at)),
            'comments' => $inserted_model->comment,
            'image' => $inserted_model->photo
        ]);
    }
}
