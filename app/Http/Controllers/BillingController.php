<?php

namespace App\Http\Controllers;

use App\Models\CompanyPlan;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\StripeModel;
use App\Models\Subscription;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Jenssegers\Date\Date;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Details;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Prologue\Alerts\Facades\Alert;
use Stripe\Error\Card;
use Stripe\Stripe;
use Stripe\Plan as StripPlan;
use Stripe\SubscriptionItem;
use TeamTNT\Stripe\WebhookTester;


class BillingController extends Controller
{
    protected $_api_context;

    public function index()
    {
        $this->data = [
            'title' => trans('fields.my_billing'),
            'user' => backpack_user()
        ];

        return view('company.financial', $this->data);
    }

    public function addPlan(Request $request)
    {
        $type = $request->plan_type;
        $id = $request->id;

        $company_id = backpack_user()->company->id;

        if (backpack_user()->company->companyPlan) {
            $companyPlan = backpack_user()->company->companyPlan;
            $companyPlan->company_plan_id = 'biodactil_plan-' . $id . '_' . $type;
        } else {

            $companyPlan = new CompanyPlan();
            $companyPlan->plan_id = $id;
            $companyPlan->company_id = backpack_user()->company->id;
            $companyPlan->company_plan_id = 'biodactil_plan-' . $id . '_' . $type;

            $free_month = Plan::find($id)->free_month;
            $expre_date = date('Y-m-d', strtotime(date('Y-m-d') . "+" . $free_month . " month"));
            $free_days = Carbon::now()->diffInDays(Carbon::parse($expre_date));
            $companyPlan->free_days = $free_days;
        }

        $companyPlan->save();
        Alert::success('Congratulations! Your plan work with free plan for 1 month.')->flash();
        return redirect()->back();
    }

    public function postPaymentStrip(Request $request)
    {
        $companyPlan = backpack_user()->company->companyPlan;
        if (!$companyPlan) {
            Alert::error(trans('fields.error_select_plan'))->flash();
            return redirect()->to('billing');
        }

        $company_plan_id = $companyPlan->company_plan_id;

        $strip_model = backpack_user()->company->stripeModel;

        if ($strip_model) {

        } else {
            $strip_model = new StripeModel();
            $strip_model->newSubscription('main', $company_plan_id)->create($request->stripeToken, [
                'email' => backpack_user()->company->email,
                'name' => backpack_user()->company->name,
            ]);

            if ($strip_model->subscribed('main')) {
                $companyPlan->billing_status = 'approved';
                Alert::success('Your payment successfully transferred. You can start now working with this plan.')->flash();
            } else {
                $companyPlan->billing_status = 'pending';
                Alert::error('There is some problem in your payment.')->flash();
            }
            $companyPlan->save();
        }

        return redirect()->to('billing');
    }


    public function transactionList(Request $request)
    {
        return view('company.transaction_list', ['title' => 'invoice']);
    }

    public function view_invoice($id)
    {
        $data = PaymentTransaction::find($id);
        $title = trans('fields.invoice');
        return view('company.invoice', ['title' => $title, 'data' => $data]);
    }

    public function company_information()
    {
        $data = [
            'title' => trans('fields.company_information'),
            'user' => backpack_user()
        ];
        return view('company.company_information', $data);
    }

    public function invoice_pdf($id)
    {
        $data = PaymentTransaction::find($id);
        $title = trans('fields.invoice');

        $pdf = PDF::loadView('company.invoice_template', ['data' => $data]);

        $name = $data->invoice_number;
        return $pdf->download($name . '.pdf');
    }

    public function inactivePayment(Request $request)
    {
        StripeModel::cancelStripeSubscription();
        return redirect()->back();
    }

    public function sendTestWebHook()
    {
        $tester = new WebhookTester('http://localhost:8000/webhook/stripe/handleinvoicesuccess');
        $response = $tester->setVersion('2014-09-08')->triggerEvent('charge.succeeded');

        return $response;
    }
}
