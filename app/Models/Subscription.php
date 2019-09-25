<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;


class Subscription extends Model
{
    use CrudTrait;

    protected $table = 'subscriptions';

    protected $fillable = ['name', 'stripe_id', 'stripe_plan', 'quantity', 'trial_ends_at', 'ends_at', 'payment_method', 'stripe_model_id'];


    protected static function boot()
    {
//        parent::boot();
//
//        if(backpack_user()) {
//            static::addGlobalScope('company_id', function (Builder $builder) {
//                if (backpack_user()->role == User::ROLE_COMPANY) {
//                    $builder->where('company_id', '=', backpack_user()->company->id);
//                }
//            });
//        }
//
//        if (backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {
//            static::creating(function ($query) {
//                $query->company_id = backpack_user()->company->id;
//            });
//        }
    }


//    public function company()
//    {
//        return $this->belongsTo('App\Models\Company');
//    }

//    public function plan()
//    {
//        return $this->belongsTo('App\Models\Plan');
//    }
}
