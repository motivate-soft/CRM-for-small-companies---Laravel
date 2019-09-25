<?php

namespace App\Http\Controllers\Webhook;

use App\GlobalConstant;
use App\Models\Company;
use App\Models\PaymentTransaction;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Cartalyst\Stripe\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Symfony\Component\HttpFoundation\Response;
use TeamTNT\Stripe\WebhookTester;

class StripeWebhookController extends CashierController
{
    /* Stripe Payment succeeded */
    public function handleInvoicePaymentSucceeded(Request $request)
    {
        $request_data = $request->data['object'];
        if($request_data['status'] == GlobalConstant::STRIPE_SUCCESS) {
            $last_transaction = PaymentTransaction::orderBy('created_at', 'DESC')->first();
            if ($last_transaction && Carbon::createFromFormat('Y-m-d H:i:s', $last_transaction->created_at)->year == date('Y')) {
                $invoice_id = $last_transaction->invoice_number;
                $inv_num = explode('-', $invoice_id)[2];
                $new_inv_id = config('backpack_config.invoice_prefix').str_pad(((int)$inv_num + 1), 6, '0', STR_PAD_LEFT);
            } else {
                $new_inv_id = config('backpack_config.invoice_prefix').'000001';
            }
//            $company_email = $request_data['billing_details']['email'];
            $company_email = 'test@test.com';
            $company = User::where('email', $company_email)->first()->company;
            $transaction = new PaymentTransaction();
            $transaction->company_id = $company->id;
            $transaction->invoice_number = $new_inv_id;
            $transaction->payment_type = GlobalConstant::PAYMENT_VISA;
            $transaction->amount = (float)$request_data['amount']/100;
            $transaction->currency = strtoupper($request_data['currency']);
            $transaction->save();

            $company_plan = $company->companyPlan;
            if ($company_plan) {
                $company_plan->billing_status = GlobalConstant::COMPANY_PLAN_STATUS_APPROVED;
                $company_plan->save();
            }
        }
    }

    /* Stripe Payment failed */
    public function handleInvoicePaymentFailed(Request $request)
    {
        $request_data = $request->data['object'];
        if($request_data['status'] == GlobalConstant::STRIPE_FAIL) {
//            $company_email = $request_data['billing_details']['email'];
            $company_email = 'test@test.com';
            $company_plan = User::where('email', $company_email)->first()->company->companyPlan;
            $company_plan->billing_status = GlobalConstant::COMPANY_PLAN_STATUS_PENDING;
            $company_plan->save();
        }
    }
}
