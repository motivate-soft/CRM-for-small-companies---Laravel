<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Builder;


class Payment extends Model
{
    use Billable;

    protected $table = ['braintree_id', 'company_id', 'paypal_email', 'card_brand', 'card_last_four', 'trial_ends_at'];

    protected static function boot()
    {
        parent::boot();

        if(backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {

            // auto-sets values on creation
            static::creating(function ($query) {
                $query->company_id = backpack_user()->company_id;
            });
        }
    }
}
