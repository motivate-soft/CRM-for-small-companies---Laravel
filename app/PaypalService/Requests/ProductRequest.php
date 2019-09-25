<?php

namespace App\PaypalService\Requests;


use BraintreeHttp\HttpRequest;

class ProductRequest extends HttpRequest
{
    function __construct($id=null)
    {
        $url = '/v1/catalogs/products';
        $verb = 'POST';
        if ($id) {
            $url .= '/' . $id;
            $verb = 'GET';
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

    public function buildRequestBody()
    {
        return array(
            "name" => "biodactil",
            "description" => "biodcatil product",
            "type" => "SERVICE",
            "category" => "SOFTWARE"
        );
    }
}
