<?php

namespace App\Http\Controllers;

use App\GlobalConstant;
use App\Models\PaypalModel;
use App\Models\PaypalPlan;
use App\Models\Transaction;
use App\PaypalService\Requests\PlanRequest;
use App\PaypalService\Requests\ProductRequest;
use App\PaypalService\Requests\SubscriptionRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use App\PaypalService\PayPalClient;
use Prologue\Alerts\Facades\Alert;
use Srmklive\PayPal\Services\ExpressCheckout;
use Illuminate\Support\Facades\URL;


use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel as PaypalCreateModel;
use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\ShippingAddress;


class PaypalController extends Controller
{
    /**
     * @var ExpressCheckout
     */
    protected $provider;

    public function __construct()
    {
        $this->provider = new ExpressCheckout();
        /** PayPal api context **/
        $paypal_conf = Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function payWithpaypal(Request $request)
    {
        $company_plan_id = backpack_user()->company->companyPlan->company_plan_id;
        $paypal_plan = PaypalPlan::where('company_plan_id', $company_plan_id)->first()->paypal_plan_id;

        $client = PayPalClient::client();
        $request = new SubscriptionRequest();
        $request->body = $request->buildCreateRequestBody($paypal_plan, backpack_user()->company->email);
        $res = $client->execute($request);

        if ($res->statusCode == 201) {
             $res_link = $res->result->links[0]->href;
             $subscription_id = $res->result->id;

             $paypalmodel = backpack_user()->company->paypalModel;
             if ($paypalmodel) {
                 $paypalmodel->delete();
             }


            $paypalmodel = new PaypalModel();
            $paypalmodel->company_id = backpack_user()->company->id;
            $paypalmodel->profile_id = $subscription_id;
            $paypalmodel->save();

            backpack_user()->company->companyPlan->billing_status = GlobalConstant::COMPANY_PLAN_STATUS_PENDING;
            backpack_user()->company->companyPlan->save();

            return redirect($res_link);
        } else {
            Alert::error('There is some problem in your payment.')->flash();
            return redirect()->to('billing');
        }
    }

    public function getExpressCheckoutSuccess(Request $request)
    {
        backpack_user()->company->companyPlan->billing_status = GlobalConstant::COMPANY_PLAN_STATUS_APPROVED;
        backpack_user()->company->companyPlan->save();

        Alert::success('Subscription created successfully.')->flash();
        return redirect()->to('billing');

//        Alert::error('There is some problem in your payment.')->flash();
//        return redirect()->to('billing');


    }

    protected function getCheckoutData($recurring = false)
    {
        $data = [];
        $company_plan_id = backpack_user()->company->companyPlan->company_plan_id;
        $plan_type = explode('_', explode('-', $company_plan_id)[1])[1];

        $plan_data = json_decode(backpack_user()->company->companyPlan->plan->data, true);
        $price = $plan_data[$plan_type]['price'];
        $currency = backpack_user()->company->companyPlan->plan->currency->short_key;


        if ($recurring === true) {
            $data['items'] = [
                [
                    'name' => 'Monthly Subscription ' . config('paypal.invoice_prefix') . ' #' . rand(11111111, 99999999),
                    'price' => $price,
                    'qty' => 1,
                ],
            ];
        } else {
            $data['items'] = [
                [
                    'name' => 'Product 1',
                    'price' => 9.99,
                    'qty' => 1,
                ],
            ];

            $data['return_url'] = url('/paypal/ec-checkout-success');
        }


        $order_id = rand(11111111, 99999999);
        $data['items'] = [
            [
                'name' => 'Monthly Subscription ' . config('paypal.invoice_prefix') . ' #' . $order_id,
                'price' => $price,
                'qty' => 1,
            ],
        ];


        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $data['invoice_id'] = config('paypal.invoice_prefix') . $order_id;
        $data['total'] = $total;
        $data['currency'] = $currency;

        $data['return_url'] = URL::route('payment_result');
        $data['cancel_url'] = URL::route('cancel_payment');

        $data['invoice_description'] = "Order #$order_id Invoice";
        $data['subscription_desc'] = 'Monthly Subscription';

        return $data;
    }

    protected function createTransaction($cart, $status)
    {
        $subscription = backpack_user()->company->subscription;
        $price = json_decode($subscription->plan->data, true)[$subscription->type]['price'];
        $currency = $subscription->plan->currency->short_key;


        if (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed')) {
            $transaction = new Transaction();
//        $transaction->title = $cart['invoice_description'];
            $transaction->amount = $cart['total'];
            $transaction->company_id = backpack_user()->company->id;
            $transaction->invoice_number = $cart['invoice_id'];
            $transaction->payment_type = 'paypal';
            $transaction->currency = $currency;
            $transaction->save();

            $subscription->subscription_date = date('Y-m-d');
            $subscription->status = 'approved';
            $subscription->save();

            return $subscription;
        } else {
            $subscription->subscription_date = date('Y-m-d');

            if ($subscription->status == 'resolving') {

            } else if ($subscription->status == 'pending' || $subscription->status == 'approved') {
                $subscription->subscription_date = date('Y-m-d');
            }

            $subscription->status = 'resolving';
            $subscription->save();

            return $subscription;
        }
    }

    public function notify(Request $request)
    {
        if (!($this->provider instanceof ExpressCheckout)) {
            $this->provider = new ExpressCheckout();
        }

        $post = [
            'cmd' => '_notify-validate',
        ];
        $data = $request->all();
        foreach ($data as $key => $value) {
            $post[$key] = $value;
        }

        $response = (string)$this->provider->verifyIPN($post);

        $ipn = new IPNStatus();
        $ipn->payload = json_encode($post);
        $ipn->status = $response;
        $ipn->save();
    }

    public function inactivePayment()
    {
        $paypalmodel = backpack_user()->company->paypalModel;
        $subscription_id = $paypalmodel->profile_id;

        $client = PayPalClient::client();
        $request = new SubscriptionRequest($subscription_id, 'POST', 'cancel');
        $res = $client->execute($request);

        if ($res->statusCode == 204) {
            $paypalmodel->delete();
            backpack_user()->company->companyPlan->billing_status = GlobalConstant::COMPANY_PLAN_STATUS_PENDING;
            backpack_user()->company->companyPlan->save();
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }


    public function createProduct()
    {
        $client = PayPalClient::client();
        $request = new ProductRequest();
        $request->body = $request->buildRequestBody();
        $res = $client->execute($request);

        if ($res->statusCode == 201) {
            return $res->result->id;
        } else {
            return null;
        }
    }

    public function createPlan()
    {
        $client = PayPalClient::client();
        $request = new PlanRequest();
        $request->body = $request->buildCreateRequestBody('PROD-38568867S1523492D', 50, 'EUR');
        $res = $client->execute($request);
        return $res->result;
    }

    public function createSubscription()
    {
        $subscription_id = 'I-DSXHLUEGTFJX';
        $client = PayPalClient::client();
        $request = new SubscriptionRequest($subscription_id, 'GET');
        $res = $client->execute($request);
//        dd($res->result->billing_info->last_payment->amount->currency_code);
        dd($res->result->billing_info->last_payment->amount->value);
        $company_email = $res->result->billing_info->last_payment;
    }
}
