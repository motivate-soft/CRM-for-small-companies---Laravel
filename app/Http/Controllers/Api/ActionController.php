<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\ActionsOption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ActionController extends Controller
{
    public function getActionsTypes(Request $request)
    {
        
        $company = app()->make('auth.company');
        $app = app()->make('auth.app');

        $actionsOptions = ActionsOption::select('actions_options.name', 'actions_options.id', 'actions_options.event_id', 'actions_options.type', 'actions_options.mandatory')
            ->join('devices', 'actions_options.device_id', '=', 'devices.id')
            ->where('actions_options.company_id', '=', $company->id)
            ->where('devices.name', '=', 'App')
            ->get();

        return response()->json([
            "managed" => 1,
            'data' => $actionsOptions
        ], 200);
    }

    public function getActions(Request $request)
    {

        $company = app()->make('auth.company');
        $app = app()->make('auth.app');

        $validator = Validator::make($request->all(), [
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid parameters'
            ], 422);
        }

        $month = $request->month;
        $year = $request->year;
        $format = $request->format;

        if ($month < 10) {
            $date = $year . "-0" . $month;
        } else {
            $date = $year . "-" . $month;
        }

        $carbon_instance = Carbon::now();
        $carbon_instance->year = $year;
        $carbon_instance->month = $month;


        $report = DB::table('actions')
            ->join('actions_options', 'actions.option_id', '=', 'actions_options.id')
            ->whereRaw('date(actions.datetime) between ? and ?', [
                $carbon_instance->startOfMonth()->toDateString(), 
                $carbon_instance->endOfMonth()->toDateString()
            ])
            ->where('actions.employee_id', '=', $app->employee_id)
            ->orderBy('actions.datetime', 'desc')
            ->select('actions_options.name', 'actions.datetime', 'actions.gps', 'actions_options.type')
            ->get();

        
        if ($format != null && $format == 'pdf') {

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
            $pdf->save($fullpath);

            return $pdf->download($filename);

            //return response()->download('http://localhost:8000/pdfs/' . $filename, $filename);

            // return response()->json([
            //     'reportpdf' => 'https://admin.biodactil.com/pdfs/' . $filename
            // ], 200);

        } else {

            return response()->json($report, 200);

        }
    }

    public function storeAction(Request $request)
    {

        $company = app()->make('auth.company');
        $app = app()->make('auth.app');

        /*$validator = Validator::make($request->all(), [
            'action_id' => 'required|exists:actions_options,id',
            'gps' => 'nullable',
            'auth_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid parameters'
            ], 422);
        }*/

        $validator = Validator::make($request->all(), [
            'data' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid parameters'
            ], 422);
        }

        $data = $request->data;

        $json = json_decode($data, true);

        $check_option_id = ActionsOption::where('id', $json['action_id'])->first();

        if ($check_option_id == null) {
            return response()->json([
                'error' => 'Invalid parameters'
            ], 422);
        }

        if (!empty($json['gps'])) {            
            $gps = $json['gps'];
        } else {
            $gps = "";
        }

        $action = Action::create([
            'datetime' => Carbon::parse(Carbon::now())->toDateTimeString(),
            'option_id' => $json['action_id'],
            'employee_id' => $app->employee_id,
            'gps' => $gps,
            'auth_type' => $json['auth_type']
        ]);

        return response()->json([
            'action' => $action
        ], 200);
    }

    public function track(Request $request)
    {        

        $company = app()->make('auth.company');
        $app = app()->make('auth.app');

        $action = Action::create([
            'datetime' => Carbon::parse(Carbon::now())->toDateTimeString(),
            'employee_id' => $app->employee_id,
            'gps' => $request->gps,
            'auth_type' => 'tracking'
        ]);

        return response()->json([
            'action' => $action
        ], 200);
    }
}
