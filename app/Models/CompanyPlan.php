<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPlan extends Model
{
    protected $table = 'company_plan';

    protected $fillable = ['company_id', 'company_plan_id', 'plan_id', 'free_days', 'billing_status'];

    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }
}
