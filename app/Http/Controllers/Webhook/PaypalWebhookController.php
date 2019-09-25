<?php

namespace App\Http\Controllers\Webhook;

use App\GlobalConstant;
use App\Models\Company;
use App\Models\PaymentTransaction;
use App\Models\PaypalModel;
use App\PaypalService\PayPalClient;
use App\PaypalService\Requests\SubscriptionRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\ExpressCheckout;

class PaypalWebhookController extends Controller
{
    public function handleInvoicePaymentSucceeded(Request $request)
    {
        $subscription_id = $request->resource['billing_agreement_id'];
//        $subscription_id = 'I-2YXVRLGWT8AJ';
        $paypal_model = PaypalModel::where('profile_id', $subscription_id)->first();
        if ($paypal_model) {
            $company = $paypal_model->company;


//            $subscription_id = 'I-DSXHLUEGTFJX';
            $client = PayPalClient::client();
            $request = new SubscriptionRequest($subscription_id, 'GET');
            $res = $client->execute($request);
            $currency = $res->result->billing_info->last_payment->amount->currency_code;
            $amount = $res->result->billing_info->last_payment->amount->value;



            $last_transaction = PaymentTransaction::orderBy('created_at', 'DESC')->first();
            if ($last_transaction && Carbon::createFromFormat('Y-m-d H:i:s', $last_transaction->created_at)->year == date('Y')) {
                $invoice_id = $last_transaction->invoice_number;
                $inv_num = explode('-', $invoice_id)[2];
                $new_inv_id = config('backpack_config.invoice_prefix').str_pad(((int)$inv_num + 1), 6, '0', STR_PAD_LEFT);
            } else {
                $new_inv_id = config('backpack_config.invoice_prefix').'000001';
            }

            $transaction = new PaymentTransaction();
            $transaction->company_id = $company->id;
            $transaction->invoice_number = $new_inv_id;
            $transaction->payment_type = GlobalConstant::PAYMENT_PAYPAL;
            $transaction->amount = $amount;
            $transaction->currency = $currency;
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
        $resource_type = $request->resource_type;
        if ($resource_type == 'subscription') {
                    $subscription_id = $request->resource['id'];
//            $subscription_id = 'I-2YXVRLGWT8AJ';

        } else {
            //        $subscription_id = $request->resource['id'];
            $subscription_id = $request->resource['billing_agreement_id'];
        }

        $paypal_model = PaypalModel::where('profile_id', $subscription_id)->first();
        if ($paypal_model) {
            $company = $paypal_model->company;
            $company_plan = $company->companyPlan;
            if ($company_plan) {
                $company_plan->billing_status = GlobalConstant::COMPANY_PLAN_STATUS_PENDING;
                $company_plan->save();
            }
        }
    }
}
