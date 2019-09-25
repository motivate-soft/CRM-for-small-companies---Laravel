<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class WorkingDays extends Model
{
    use CrudTrait;

    protected $table = 'working_days';

    protected $fillable = ['name'];

    public function departments()
    {
        return $this->belongsToMany('App\Models\Department', 'department_has_working_days', 'working_day_id', 'department_id');
    }

//
//    public function users()
//    {
//        return $this->belongsToMany('App\User', 'user_has_working_days', 'working_day_id', 'user_id');
//    }

    public function workcenters()
    {
        return $this->belongsToMany('App\Models\Workcenter', 'workcenter_has_working_days', 'working_day_id', 'workcenter_id');
    }
}
