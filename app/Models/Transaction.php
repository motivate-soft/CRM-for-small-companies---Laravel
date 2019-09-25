<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = ['company_id', 'invoice_number', 'payment_type', 'amount'];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }
}
