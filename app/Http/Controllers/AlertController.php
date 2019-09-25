<?php

namespace App\Http\Controllers;

use App\Models\AlertConfig;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

class AlertController extends Controller
{

    public function index(Request $request)
    {
        $company_id = backpack_user()->company->id;
        $punctuality_time = '';
        $exceed_working_time = '';
        $notify_employee_punctuality = '';
        $notify_company_punctuality = '';
        $notify_employee_exceed = '';
        $notify_company_exceed = '';
        if(AlertConfig::where('company_id', $company_id)->first()){
            $punctuality_time = AlertConfig::where('company_id', $company_id)->first()->punctuality_time;
        }
        if(AlertConfig::where('company_id', $company_id)->first()){
            $exceed_working_time = AlertConfig::where('company_id', $company_id)->first()->exceed_working_time;
        }
        if(AlertConfig::where('company_id', $company_id)->first()){
            $notify_employee_punctuality = AlertConfig::where('company_id', $company_id)->first()->notify_employee_punctuality;
        }
        if(AlertConfig::where('company_id', $company_id)->first()){
            $notify_company_punctuality = AlertConfig::where('company_id', $company_id)->first()->notify_company_punctuality ;
        }
        if(AlertConfig::where('company_id', $company_id)->first()){
            $notify_employee_exceed = AlertConfig::where('company_id', $company_id)->first()->notify_employee_exceed;
        }
        if(AlertConfig::where('company_id', $company_id)->first()){
            $notify_company_exceed = AlertConfig::where('company_id', $company_id)->first()->notify_company_exceed;
        }

        $data['punctuality_time'] = $punctuality_time;
        $data['exceed_working_time'] = $exceed_working_time;
        $data['notify_employee_punctuality'] = $notify_employee_punctuality;
        $data['notify_company_punctuality'] = $notify_company_punctuality;
        $data['notify_employee_exceed'] = $notify_employee_exceed;
        $data['notify_company_exceed'] = $notify_company_exceed;
        $data['options'] = ['',15,30,45,60,90,120];
        return view('backpack::auth.account.alert_configuration', $data);
    }

    public function save(Request $request){
        $company_id = backpack_user()->company->id;
        $punctuality_time = $request->punctuality_time;

        $exceed_working_time = $request->exceed_working_time;

        if(isset($request->notify_employee_punctuality)){
            $notify_employee_punctuality = 1;
        }else{
            $notify_employee_punctuality = 0;
        }
        if(isset($request->notify_company_punctuality)){
            $notify_company_punctuality = 1;
        }else{
            $notify_company_punctuality = 0;
        }
        if(isset($request->notify_employee_exceed)){
            $notify_employee_exceed = 1;
        }else{
            $notify_employee_exceed = 0;
        }
        if(isset($request->notify_company_exceed)){
            $notify_company_exceed = 1;
        }else{
            $notify_company_exceed = 0;
        }

        $result = true;

        if($punctuality_time == '' && $exceed_working_time == '' && $notify_employee_punctuality==0 && $notify_company_punctuality==0 && $notify_employee_exceed==0 && $notify_company_exceed==0){
           if(AlertConfig::where('company_id', $company_id)->first()){
               $result = AlertConfig::where('company_id', $company_id)->delete();
           }
        }else {
            if(AlertConfig::where('company_id', $company_id)->first()){
                AlertConfig::where('company_id', $company_id)->update([
                    'punctuality_time' => $punctuality_time,
                    'exceed_working_time' => $exceed_working_time,
                    'notify_employee_punctuality' => $notify_employee_punctuality,
                    'notify_company_punctuality' => $notify_company_punctuality,
                    'notify_employee_exceed' => $notify_employee_exceed,
                    'notify_company_exceed' => $notify_company_exceed,
                ]);
            }else{
                $alert = new AlertConfig();
                $alert->company_id = $company_id;
                $alert->punctuality_time = $punctuality_time;
                $alert->exceed_working_time = $exceed_working_time;
                $alert->notify_employee_punctuality = $notify_employee_punctuality;
                $alert->notify_company_punctuality = $notify_company_punctuality;
                $alert->notify_employee_exceed = $notify_employee_exceed;
                $alert->notify_company_exceed = $notify_company_exceed;
                $result = $alert->save();
            }
        }

        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }
}
