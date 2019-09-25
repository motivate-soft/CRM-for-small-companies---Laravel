<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalPlan extends Model
{
    protected $table = 'paypal_plan';
    protected $fillable = ['company_plan_id', 'paypal_plan_id'];

}
