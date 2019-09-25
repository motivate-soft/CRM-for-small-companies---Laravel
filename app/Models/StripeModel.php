<?php

namespace App\Models;

use App\GlobalConstant;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class StripeModel extends Model
{
    protected $table = 'stripe_model';

    use Billable;

    protected $fillable = ['stripe_id', 'company_id', 'card_brand', 'card_last_four', 'trial_ends_at'];

    protected static function boot()
    {
        parent::boot();

        if(backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {

            // auto-sets values on creation
            static::creating(function ($query) {
                $query->company_id = backpack_user()->company->id;
            });
        }
    }

//    public function subscription()
//    {
//        return $this->hasOne('App\Models\Subscription');
//    }

    public static function cancelStripeSubscription()
    {
        try{
            backpack_user()->company->stripeModel->subscription('main')->cancel();
        } catch (\Exception $exception) {

        }

        backpack_user()->company->companyPlan->billing_status = GlobalConstant::COMPANY_PLAN_STATUS_PENDING;
        backpack_user()->company->companyPlan->save();
        backpack_user()->company->stripeModel->delete();
        return true;
    }

    public static function retrieveStripeSubscription()
    {
        try{
            if (backpack_user()->company->stripeModel) {
                $subscription_item =  backpack_user()->company->stripeModel->subscription('main');
                return $subscription_item;
            } else {
                return null;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
}
