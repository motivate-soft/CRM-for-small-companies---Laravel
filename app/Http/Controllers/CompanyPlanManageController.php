<?php

namespace App\Http\Controllers;

use App\GlobalConstant;
use App\Models\CompanyPlan;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyPlanManageController extends Controller
{
    public function companyPlanList()
    {
        $companies = Company::all();
        $data = [
            'title' => trans('fields.company_plans'),
            'companies' => $companies
        ];
        return view('admin.companyplan_manage', $data);
    }

    public function edit($id)
    {
        $company = Company::find($id);
        $data = [
            'title' => trans('fields.edit_company_plan'),
            'company' => $company
        ];
        return view('admin.companyplan_edit', $data);
    }

    public function save_compay_plan(Request $request)
    {
        $id = $request->id;
        $plan_id = $request->plan_id;
        $plan_type = $request->plan_type;
        $free_days = $request->free_days;
        $billing_status = $request->billing_status;

        $company_plan = CompanyPlan::where('company_id', $id)->first();
        if (!$company_plan) {
            $company_plan = new CompanyPlan();
        }

//        if ($billing_status != GlobalConstant::COMPANY_PLAN_STATUS_UNLIMITED) {
//            $company_plan->plan_id = $plan_id;
//            $company_plan->company_plan_id = Plan::getPlanId($plan_id, $plan_type);
//            $company_plan->free_days = $free_days;
//        }
        /* Optional.*/
        $company_plan->plan_id = $plan_id;
        $company_plan->company_plan_id = Plan::getPlanId($plan_id, $plan_type);
        $company_plan->free_days = $free_days;

        $company_plan->company_id = $id;
        $company_plan->billing_status = $billing_status;
        $company_plan->save();

        return redirect()->to('company_palns');
    }

    public function delete($id)
    {
        $company_plan = CompanyPlan::where('company_id', $id)->first();
        if ($company_plan) {
            $company_plan->delete();
        }


        return redirect()->back();
    }
}
