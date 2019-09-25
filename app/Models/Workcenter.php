<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\CrudTrait;

class Workcenter extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'workcenters';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'country', 'state', 'city', 'zip_code', 'address', 'location', 'wifi', 'cell', 'company_id'];
    // protected $hidden = [];
    // protected $dates = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company_id', function (Builder $builder) {
            if(backpack_user() && backpack_user()->role == 'company') {
                $builder->where('company_id', '=', backpack_user()->company->id);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

//    public function employees()
//    {
//        return $this->belongsToMany('App\Models\Employee', 'workcenter_has_employees', 'workcenter_id', 'employee_id');
//    }

    public function employee()
    {
        return $this->hasMany('App\Models\Employee');
    }

    public function holidays()
    {
        return $this->belongsToMany('App\Models\WorkingPlaceHoliday', 'workcenter_has_holidays', 'workcenter_id', 'holiday_id');
    }

    public function events()
    {
        return $this->belongsToMany('App\Models\CalendarEvent', 'workcenter_has_events', 'workcenter_id', 'event_id');
    }

    public function workingDays()
    {
        return $this->belongsToMany('App\Models\WorkingDays', 'workcenter_has_working_days', 'workcenter_id', 'working_day_id');
    }

    public function get_calendar_button($crud = false)
    {
        return '<a class="btn btn-xs btn-default" href="'.backpack_url('workcenters'). '/' . $this->id . '/calendar" data-toggle="tooltip" title="Just a demo custom button."><i class="fa fa-calendar"></i> Calendar</a>';
    }


//    public function devices()
//    {
//        return $this->hasMany('App\Models\Device', 'workcenter_id', 'id');
//    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
