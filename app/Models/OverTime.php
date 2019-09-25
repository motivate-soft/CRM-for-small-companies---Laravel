<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverTime extends Model
{
    protected $table = 'overtime';

    protected $fillable = ['overtime', 'employee_id'];

    public function employees()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
