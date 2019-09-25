<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExceedTime extends Model
{
    protected $table = 'exceedtime';

    protected $fillable = ['exceed_time', 'employee_id'];

    public function employees()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
