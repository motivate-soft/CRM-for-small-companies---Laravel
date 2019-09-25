<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalModel extends Model
{
    protected $table = 'paypal_model';

    protected $fillable = ['company_id', 'profile_id', 'status'];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }
}
