<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\App;
use App\Models\Company;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function getReport(Request $request)
    {
        $header = $request->header('Authorization');

        if ($header == "") {
            return response()->json([
                "error" => "Not set authorization header"
            ], 401);
        }

        $array_header = explode(":", $header);
        $user_id = Str::substr($array_header[0], 10);
        $access_company_token = $array_header[1];

        $validator = Validator::make($request->all(), [
            'month' => 'required|numeric|min:0|max:12',
            'year' => 'required|numeric|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid parameters'
            ], 422);
        }

        $month = $request->month;
        $year = $request->year;

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

        if ($month < 10) {
            $date = $year . "-0" . $month;
        } else {
            $date = $year . "-" . $month;
        }

        $report = DB::table('actions')
            ->join('actions_options', 'actions.option_id', '=', 'actions_options.id')
            ->where('actions.datetime', 'like', '%' . $date . '%')
            ->select('actions_options.name', 'actions.datetime', 'actions.gps', 'actions_options.type')
            ->get();

        return response()->json([
            'report' => $report
        ], 200);
    }

    public function getReportpdf(Request $request)
    {
        $header = $request->header('Authorization');

        if ($header == "") {
            return response()->json([
                "error" => "Not set authorization header"
            ], 401);
        }

        $array_header = explode(":", $header);
        $user_id = Str::substr($array_header[0], 10);
        $access_company_token = $array_header[1];

        if ($header == "") {
            return response()->json([
                "error" => "Not set authorization header"
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|numeric|min:0|max:12',
            'year' => 'required|numeric|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid parameters'
            ], 422);
        }

        $month = $request->month;
        $year = $request->year;

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

        if ($month < 10) {
            $date = $year . "-0" . $month;
        } else {
            $date = $year . "-" . $month;
        }

        $report = DB::table('actions')
            ->join('actions_options', 'actions.option_id', '=', 'actions_options.id')
            ->where('actions.datetime', 'like', '%' . $date . '%')
            ->select('actions_options.name', 'actions.datetime', 'actions.gps', 'actions_options.type')
            ->get();

        $data['month'] = $month;
        $data['year'] = $year;
        $data['cif'] = $company->vat_number;
        $data['name'] = $app->employee->user->name;
        $data['nif'] = $app->employee->nif;
        $data['affiliation'] = $app->employee->affiliation;
        $data['report'] = $report;

        $pdf = PDF::loadView('reportpdf', $data);
        $filename = 'reportpdf-' . date_format(Carbon::parse(Carbon::now()), 'YmdHis') . '.pdf';
        $path = public_path() . '/pdfs/';
        $fullpath = $path . $filename;
        $pdf->save($fullpath)->stream();

        return response()->json([
            'reportpdf' => 'https://admin.biodactil.com/pdfs/' . $filename
        ], 200);
    }

    public function track(Request $request)
    {
        $header = $request->header('Authorization');

        if ($header == "") {
            return response()->json([
                "error" => "Not set authorization header"
            ], 401);
        }

        $array_header = explode(":", $header);
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

        $action = Action::create([
            'datetime' => Carbon::parse(Carbon::now())->toDateTimeString(),
            'employee_id' => $app->employee_id,
            'gps' => $request->gps,
            'auth_type' => 'simple'
        ]);

        return response()->json([
            'action' => $action
        ], 200);
    }
}
