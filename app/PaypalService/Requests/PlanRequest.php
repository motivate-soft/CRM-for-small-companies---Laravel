<?php

namespace App\PaypalService\Requests;


use BraintreeHttp\HttpRequest;

class PlanRequest extends HttpRequest
{
    function __construct($id = null, $verb='POST', $mode = null)
    {
        $url = '/v1/billing/plans';
        $verb = 'POST';
        if ($id) {
            $url .= '/' . $id;
        }

        if ($mode && $mode == 'update') {
            $url .= '/update-pricing-schemes';
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

    public function buildCreateRequestBody($product_id, $price, $currency)
    {
        $request_body = '{
            "product_id": "' . $product_id .'",
            "name": "Biodactil Plan",
            "description": "Biodactil monthly subscription Plan",
            "billing_cycles": [
                {
                    "frequency": {
                        "interval_unit": "MONTH",
                        "interval_count": 1
                    },
                    "tenure_type": "TRIAL",
                    "sequence": 1
                },
                {
                    "frequency": {
                        "interval_unit": "MONTH",
                        "interval_count": 1
                    },
                    "tenure_type": "REGULAR",
                    "sequence": 2,
                    "total_cycles": 12,
                    "pricing_scheme": {
                        "fixed_price": {
                            "value": "' . $price . '",
                            "currency_code": "' . $currency . '"
                        }
                    }
                }
            ],
            "payment_preferences": {
                "service_type": "PREPAID",
                "auto_bill_outstanding": true,
                "setup_fee_failure_action": "CONTINUE",
                "payment_failure_threshold": 3
            },
            "quantity_supported": true,
            "taxes": {
                "percentage": "10",
                "inclusive": false
            }
        }';

        $res = json_decode($request_body, true);
        return $res;
    }

    public function buildUpdateRequestBody($price, $currency)
    {
        $request_body = '{
          "pricing_schemes": [{
            "billing_cycle_sequence": 2,
            "pricing_scheme": {
              "fixed_price": {
                "value": "' . $price . '",
                "currency_code": "' . $currency . '"
                }
              }
            }
          ]
        }';

        $res = json_decode($request_body, true);
        return $res;
    }
}
