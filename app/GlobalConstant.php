<?php
/**
 * Created by PhpStorm.
 * User: AOJIE
 * Date: 6/24/2019
 * Time: 1:32 AM
 */

namespace App;


class GlobalConstant
{
    /* Payment */
    const PAYMENT_VISA = 'visa';
    const PAYMENT_PAYPAL = 'paypal';
    const STRIPE_SUCCESS = 'succeeded';
    const STRIPE_FAIL = 'failed';

    /* Company Plan */
    const COMPANY_PLAN_STATUS_FREE = 'free';
    const COMPANY_PLAN_STATUS_APPROVED = 'approved';
    const COMPANY_PLAN_STATUS_PENDING = 'pending';
    const COMPANY_PLAN_STATUS_UNLIMITED = 'unlimited';
    const COMPANY_PLAN_STATUS_EXPIRED = 'expired';
    const COMPANY_PLAN_STATUS_REJECTED = 'rejected';
}
