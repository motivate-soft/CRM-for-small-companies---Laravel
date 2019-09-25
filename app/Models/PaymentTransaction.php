<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PaymentTransaction extends Model
{
    use CrudTrait;

    protected $table = 'transactions';

    protected $fillable = ['company_id', 'invoice_number', 'payment_type', 'amount', 'currency'];

    protected static function boot()
    {
        parent::boot();

        if(backpack_user()) {
            static::addGlobalScope('company_id', function (Builder $builder) {
                if (backpack_user()->role == User::ROLE_COMPANY) {
                    $builder->where('company_id', '=', backpack_user()->company->id);
                }
            });
        }
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function view_invoice()
    {
        return '<a class="btn btn-xs btn-default" href="'.backpack_url('transaction'). '/' . $this->id . '/invoice" data-toggle="tooltip"><i class="fa fa-money"></i> ' . trans('fields.invoice') . '</a>';
    }
}
