<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

class RejectListController extends Controller
{
    public function index()
    {
        $table = Company::all();

        $filtered = $table->filter(function ($item){
            if ($item['status'] == 'approved') {
                $companyPlan = $item->companyPlan;
                if ($companyPlan) {
                    if ($companyPlan->billing_status != 'unlimited' && $companyPlan->billing_status != 'approved') {
                        $free_days = $companyPlan->free_days;
                        $item['expire_date'] = date("Y-m-d", strtotime($companyPlan->created_at . "+" . $free_days . " days"));
                        $item['billing_state'] = $companyPlan->billing_status;

                        if ($item['expire_date'] < date('Y-m-d')){
                            return $item;
                        }
                    }
                }
                 else {
                    $item['billing_state'] = 'no_pay';
                    return $item;
                }
            }
        });
        $data = [
            'title' => trans('fields.reject_list'),
            'table' => $filtered
        ];
        return view('admin.reject_list', $data);
    }

    public function reject($id)
    {
        $user = Company::find($id)->user;
        $user->status = User::STATUS_BANNED;
        $user->save();
        return redirect()->back();
    }

    public function trial_mode()
    {
        $table = Company::all();
        $plan_labels = Plan::getPlanLabelList();
        $filtered = $table->filter(function ($item) use ($plan_labels){
            if ($item['status'] == 'approved') {
                $companyPlan = $item->companyPlan;

                if ($companyPlan) {
                    $free_days = $companyPlan->free_days;

                    if ($companyPlan->billing_status == 'free') {
                        $item['plan'] = explode('_', explode('-', $companyPlan->company_plan_id)[1])[1];

                        $item['expire_date'] = date("Y-m-d", strtotime($companyPlan->created_at . "+" . $free_days . " days"));
                        $item['left_days'] = Carbon::now()->diffInDays(Carbon::parse($item['expire_date']));
                        $item['subscription_date'] = $companyPlan->created_at;

                        if ($item['expire_date'] < Carbon::now()) {
                            $item['left_days'] = -$item['left_days'];
                        }
                        return $item;
                    }
                }
            }
        });

        $data = [
            'title' => trans('fields.trial_mode'),
            'table' => $filtered
        ];

        return view('admin.trial_mode', $data);
    }

    public function edit_trial(Request $request)
    {
        $id = $request->id;
        $data = [
            'title' => trans('fields.edit_trial_mode'),
            'company' => Company::find($id)
        ];

        return view('admin.edit_trial', $data);
    }

    public function save_trial_mode(Request $request)
    {
        $id = $request->id;
        $left_days = $request->left_days;

        $billing_status = $request->billing_status;


        $company_plan = Company::find($id)->companyPlan;
        $company_plan->billing_status = $billing_status;

        $spent_days = Carbon::now()->diffInDays(Carbon::parse($company_plan->created_at));

        $company_plan->free_days = $spent_days + $left_days + 1;
        $company_plan->save();

        return redirect()->to('trial_mode');
    }
}
