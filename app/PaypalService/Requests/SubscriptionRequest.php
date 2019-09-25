<?php

namespace App\PaypalService\Requests;


use BraintreeHttp\HttpRequest;
use Illuminate\Support\Facades\URL;


class SubscriptionRequest extends HttpRequest
{
    function __construct($id=null, $verb='POST', $mode=null)
    {
        $url = '/v1/billing/subscriptions';
        if ($id) {
            $url .= '/' . $id;
        }

        if ($mode == 'cancel') {
            $url .= '/cancel';
        }


        parent::__construct($url, $verb);
        $this->headers["Content-Type"] = "application/json";
    }

    public function payPalRequestId($payPalRequestId)
    {
        $this->headers["PayPal-Request-Id"] = $payPalRequestId;
    }
    public function prefer($prefer)
    {
        $this->headers["Prefer"] = $prefer;
    }

    public function buildCreateRequestBody($plan_id, $subscriber)
    {
        $request_body = '{
            "plan_id": "' . $plan_id . '",
            "subscriber": {
              "email_address": "' . $subscriber . '"
            },
            "auto_renewal": true,
            "application_context": {
              "brand_name": "example",
              "shipping_preference": "SET_PROVIDED_ADDRESS",
              "user_action": "SUBSCRIBE_NOW",
              "payment_method": {
                  "payer_selected": "PAYPAL",
                  "payee_preferred": "IMMEDIATE_PAYMENT_REQUIRED"
              },
              "return_url": "' . 'https://example.com/billing/get_paypal_payment_result' . '",
              "cancel_url": "' . 'https://example.com/billing/cancel_paypal_payment_result' . '"
            }
        }';

        $res = json_decode($request_body, true);
        return $res;
    }
}
