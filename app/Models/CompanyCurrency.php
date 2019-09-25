<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCurrency extends Model
{
    protected $table = 'company_currency';

    protected $fillable = ['company_id', 'currency_table'];

    public function company()
    {
        return $this->hasOne('App\Models\Company');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }
}
