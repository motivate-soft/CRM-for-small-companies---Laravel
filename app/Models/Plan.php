<?php

namespace App\Models;

use App\PaypalService\PayPalClient;
use App\PaypalService\Requests\PlanRequest;
use App\PaypalService\Requests\ProductRequest;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Plan as StripePlan;
use App\Models\PaypalPlan;

class Plan extends Model
{
    use CrudTrait;

    protected $table = 'plans';

    protected $fillable = ['currency_id','free_month',  'data'];

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    public function subscription()
    {
        return $this->hasMany('App\Models\Subscription');
    }

    public static function getPlanLabelList()
    {
        $plans = array(
            0 => trans('fields.basic'),
            1 => trans('fields.standard'),
            2 => trans('fields.premium'),
            3 => trans('fields.business'),
            4 => trans('fields.enterprise'),
        );

        return $plans;
    }

    public static function addStripPlan($request)
    {
        $plan_data = json_decode($request->data, true);
        $plan_id = Plan::orderBy('created_at', 'desc')->first()->id;

        $currency = Currency::find($request->currency_id)->short_key;

        Stripe::setApiKey(Config::get('services.stripe.secret'));

        for ($i = 0; $i < count($plan_data); $i++) {
            $p_id = "biodactil_plan-" . $plan_id . "_" . $i;

            try{
                $plan = StripePlan::retrieve($p_id);
            } catch (\Exception $exception) {
                StripePlan::create([
                    "amount" => (float)$plan_data[$i]['price'] * 100,
                    "interval" => "month",
                    "product" => [
                        "name" => $currency . "_biodactil_stripe_plan"
                    ],
                    "currency" => $currency,
                    "id" => $p_id
                ]);
            }
        }

        return true;
    }

    public static function updateStripPlan($request)
    {
        $plan_data = json_decode($request->data, true);
        $plan_id = $request->id;
        $currency = Currency::find($request->currency_id)->short_key;

        Stripe::setApiKey(Config::get('services.stripe.secret'));

        for ($i = 0; $i < count($plan_data); $i++) {
            $p_id = "biodactil_plan-" . $plan_id . "_" . $i;

            try{
                $plan = StripePlan::retrieve($p_id);
                $plan->delete();

                StripePlan::create([
                    "amount" => (float)$plan_data[$i]['price'] * 100,
                    "interval" => "month",
                    "product" => [
                        "name" => $currency . "_biodactil_stripe_plan"
                    ],
                    "currency" => $currency,
                    "id" => $p_id
                ]);

            } catch (\Exception $exception) {
                StripePlan::create([
                    "amount" => (float)$plan_data[$i]['price'] * 100,
                    "interval" => "month",
                    "product" => [
                        "name" => $currency . "_biodactil_stripe_plan"
                    ],
                    "currency" => $currency,
                    "id" => $p_id
                ]);
            }
        }
        return true;
    }

    public static function getPlanId($id, $type)
    {
        return "biodactil_plan-" . $id . "_" . $type;
    }

    public static function createPaypalPlan($product_id, $price, $currency) {
        $client = PayPalClient::client();
        $request = new PlanRequest();
        $request->body = $request->buildCreateRequestBody($product_id, $price, $currency);
        $res = $client->execute($request);
        return $res->result->id;
    }

    public static function addPaypalPlan($request)
    {
        $plan_data = json_decode($request->data, true);
        $plan_id = Plan::orderBy('created_at', 'desc')->first()->id;

        $currency = Currency::find($request->currency_id)->short_key;
        for ($i = 0; $i < count($plan_data); $i++) {
            $p_id = "biodactil_plan-" . $plan_id . "_" . $i;

            try{
                if (Session::has('product_id')) {
                    $product_id = Session::get('product_id');
                } else {
                    $product_id = self::createPaypalProduct();
                }
                $paypal_plan_id = self::createPaypalPlan($product_id, (float)$plan_data[$i]['price'], $currency);

                if ($paypal_plan_id) {
                    PaypalPlan::create([
                        'company_plan_id' => $p_id,
                        'paypal_plan_id' => $paypal_plan_id
                    ]);
                }
            }catch (\Exception $exception) {

            }
        }
    }

    public static function updatePaypalPlan($request)
    {
        $plan_data = json_decode($request->data, true);
        $plan_id = $request->id;
        $currency = Currency::find($request->currency_id)->short_key;

        for ($i = 0; $i < count($plan_data); $i++) {
            $p_id = "biodactil_plan-" . $plan_id . "_" . $i;

            $paypal_plan = PaypalPlan::where('company_plan_id', $p_id)->first();

            if ($paypal_plan) {
                if (self::getPaypalPlan($paypal_plan->paypal_plan_id)->statusCode == '200') {
                    $client = PayPalClient::client();
                    $request = new PlanRequest($paypal_plan->paypal_plan_id, 'POST', 'update');
                    $request->body = $request->buildUpdateRequestBody((float)$plan_data[$i]['price'], $currency);
                    $res = $client->execute($request);
                } else {
                    if (Session::has('product_id')) {
                        $product_id = Session::get('product_id');
                    } else {
                        $product_id = self::createPaypalProduct();
                    }

                    $paypal_plan_id = self::createPaypalPlan($product_id, (float)$plan_data[$i]['price'], $currency);
                    if ($paypal_plan_id) {
                        PaypalPlan::create([
                            'company_plan_id' => $p_id,
                            'paypal_plan_id' => $paypal_plan_id
                        ]);
                    }
                }
            }
            else {
                if (Session::has('product_id')) {
                    $product_id = Session::get('product_id');
                } else {
                    $product_id = self::createPaypalProduct();
                }
                $paypal_plan_id = self::createPaypalPlan($product_id, (float)$plan_data[$i]['price'], $currency);
                if ($paypal_plan_id) {
                    PaypalPlan::create([
                        'company_plan_id' => $p_id,
                        'paypal_plan_id' => $paypal_plan_id
                    ]);
                }
            }
        }
    }

    public static function getPaypalPlan($plan_id)
    {
        $client = PayPalClient::client();
        $request = new PlanRequest($plan_id, 'GET');
        $res = $client->execute($request);
        return $res;
    }

    public static function createPaypalProduct()
    {
        $client = PayPalClient::client();
        $request = new ProductRequest();
        $request->body = $request->buildRequestBody();
        $res = $client->execute($request);

        if ($res->statusCode == 201) {
            Session::put('product_id', $res->result->id);
            return $res->result->id;
        } else {
            return null;
        }
    }
}
