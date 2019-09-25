<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Holiday;
use App\User;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index(Request $request){
        $user_id = $request->id;
        $holidays =  $this->getHolidays($user_id);
        $data['holidays'] = $holidays;
        $data['title'] = trans('fields.holidays');
        $data['employee_name']  = $this->getEmployeeName($user_id);
        return view('holiday_detail', $data);
    }

    public function getHolidays($user_id){
        $employee_id = Employee::where('user_id', $user_id)->first()->id;
        return Holiday::where('employee_id', $employee_id)->orderBy('created_at', 'DESC')->get();
    }
    public function getEmployeeName($user_id){
        return User::find($user_id)->name;
    }
}
