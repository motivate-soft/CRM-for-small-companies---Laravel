<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest_1;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Prologue\Alerts\Facades\Alert;

class ChangePasswordController extends Controller
{
    public function employee_index(Request $request){
        $employee_id = $request->id;
        $data['id'] = $employee_id;
        $data['title'] = trans('backpack::base.change_password');

        return view('employee_change_password', $data);
    }
    public function employee_update(ChangePasswordRequest_1 $request){
        $employee_id = $request->id;
        $user_id = Employee::find($employee_id)->user_id;
        $password = $request->new_password;
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = $user_model_fqn::find($user_id);
        $user->password = bcrypt($password);
        $user->save();
//        Alert::success(trans('update_success'))->flash();
        return Redirect::to('employees');
    }

    public function company_index(Request $request){
        $company_id = $request->id;
        $data['id'] = $company_id;
        $data['title'] = trans('backpack::base.change_password');
        return view('company_change_password', $data);
    }
    public function company_update(ChangePasswordRequest_1 $request){
        $company_id = $request->id;
        $user_id = Company::find($company_id)->user_id;
        $password = $request->new_password;
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = $user_model_fqn::find($user_id);
        $user->password = bcrypt($password);
        $user->save();
//        Alert::success(trans('update_success'))->flash();
        return Redirect::to('companies');
    }
}
