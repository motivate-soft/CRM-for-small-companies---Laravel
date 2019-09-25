<?php

namespace App\Http\Controllers\Api;

use App\Models\App;
use App\Models\Company;
use App\Models\Employee;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_id' => 'required|unique:apps,app_id',
            'temporal_code' => 'required|exists:apps,temporal_code',
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $employee = User::where('email', $request->email)->first()->employee;
        $app = $employee->apps->where('temporal_code' ,$request->temporal_code)->where('app_id', '')->first();

        if ($app) {
            $created_date = new Carbon($app->created_at);
            $now_date = Carbon::now();
            $difference = $created_date->diff($now_date)->days;

            if ($difference > config('backpack_config.app_link_expire_date')) {
                return response()->json([
                    'status' => false,
                    'error' => trans('fields.temporal_code_expired')
                ], 422);
            }

            $app->app_id = $request->app_id;
            $app->save();

            return response()->json([
                'access_company_token' => $employee->company->access_company_token
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => 'Invalid code for this employee.'
            ], 422);
        }
    }

    public function logout(Request $request)
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


        App::where('app_id', $app->app_id)->delete();

        return response()->json([
            'status' => true,
        ]);
    }
}
