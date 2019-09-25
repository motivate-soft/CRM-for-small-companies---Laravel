<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class CalendarEvent extends Model
{
    use CrudTrait;

    protected $table = 'calendar_event';
    // protected $primaryKey = 'id';

    protected $fillable = ['name', 'company_id', 'comment', 'start_date', 'end_date'];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function workcenters()
    {
        return $this->belongsToMany('App\Models\Workcenter', 'workcenter_has_events', 'event_id', 'workcenter_id');
    }

    public function departments()
    {
        return $this->belongsToMany('App\Models\Department', 'department_has_events', 'event_id', 'department_id');
    }
}
