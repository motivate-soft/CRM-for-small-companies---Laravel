<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    public function show()
    {

        $action = Action::where('employee_id', backpack_user()->employee->id)->latest('id')->first();

        $this->data = [
            'title' => trans('backpack::base.dashboard'),
            'actions_options' => \App\Models\ActionsOption::web()->get(),
            'last_action_option_event_id' => $action ? $action->option->event_id : null,
            'last_action_option_type' => $action ? $action->option->type : null
        ];

        return view('record', $this->data);
    }

    public function store(Request $request)
    {
        $option_id = $request->get('option_id');
        $gps = $request->get('location');

        if ($gps == "") {

            $geoIp = \geoip()->getClientIP();

            $action = Action::create([
                'datetime' => Carbon::now()->toDateTimeString(),
                'option_id' => $option_id,
                'ip' => $geoIp,
                'employee_id' => backpack_user()->employee->id,
            ]);

            return response()->json([
                'status' => $action ? true : false
            ]);
        } else {
            $action = Action::create([
                'datetime' => Carbon::now()->toDateTimeString(),
                'option_id' => $option_id,
                'gps' => $gps,
                'employee_id' => backpack_user()->employee->id,
            ]);

            return response()->json([
                'status' => $action ? true : false
            ]);
        }
    }
}
