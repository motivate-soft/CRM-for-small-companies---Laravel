<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertConfig extends Model
{
    protected $table = 'alert';

    protected $fillable = ['company_id', 'punctuality_time', 'exceed_working_time', 'notify_employee_punctuality', 'notify_company_punctuality', 'notify_employee_exceed', 'notify_company_exceed'];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

}
