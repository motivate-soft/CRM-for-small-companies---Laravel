<?php

namespace App\Http\Controllers\Api;

use App\Models\App;
use App\Models\Company;
use App\Models\FcmToken;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function registerToken(Request $request)
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
            'token' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }


        $model = FcmToken::where('app_id', $app->id)->where('token', $res['token'])->first();

        if (!$model) {
            $model = new FcmToken();
        }
        $model->app_id = $app->id;
        $model->token = $res['token'];
        $model->save();

        return response()->json([
            'status' => true,
        ]);
    }

    public function pushstatus(Request $request)
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

  		 $res = $request->json()->all();
        $validator = Validator::make($request->json()->all(), [
            'id' => 'required|exists:notification,id',
            'status' => 'required|in:received,dismissed,read',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $notification = Notification::find($res['id']);
        $notification->status = $res['status'];
        $notification->save();
        return response()->json([
            'status' => true,
        ]);
    }

    public function notification()
    {
        $token = "cyueuJe-3i0:APA91bHmfiCvGD4YqhRHr3Oh_vK66s6Ji5stw_AGkye3wmyBK9iBESuZcOlUD17fCRkQrB2O2V-peTXGdMA1KuDZX5E1osYmXwPVM8IY8Hm6Yx0hLabtjbhBBbTjRxXczNy8fsD-CxAz";

        $notification = [
            'title' => "title",
            'sound' => true,
            'body' => 'Your visitor, XY has arrived',
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'icon' => 'fcm_push_icon'
        ];

        $data = [
            "landing_page" => "second",
            "price" => [
                "name" => "lehel",
                "email" => "test.com",
                "message" => "test messsage",
                "visitors" => [
                    "id" => "mQWLdQOn9K",
                    "name" => "visitor 1",
                    "email" => "koocka44@gmail.com",
                    "company" => "XX company1112221111",
                    "arrivalDate" => "2019-04-30 12:15",
                    "leaveDate" => "2019-05-30 12:15",
                    "cardnumber" => "123456789",
                    "cardHandedDown" => false
                ]
            ]
        ];


        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to' => $token, //single token
            'notification' => $notification,
            'data' => $data
        ];

        return Notification::notification($fcmNotification);
    }
}
