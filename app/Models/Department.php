<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Department extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'departments';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'company_id'];
    // protected $hidden = [];
    // protected $dates = [];

    protected static function boot()
    {
        parent::boot();
        if(backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {
            static::addGlobalScope('company_id', function (Builder $builder) {
                $builder->where('company_id', '=', backpack_user()->company->id);
            });
        }
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

//    public function employees()
//    {
//        return $this->belongsToMany('App\Models\Employee', 'department_has_employees', 'department_id', 'employee_id');
//    }
    public function employees()
    {
        return $this->hasMany('App\Models\Employee');
    }

    public function workingDays()
    {
        return $this->belongsToMany('App\Models\WorkingDays', 'department_has_working_days', 'department_id', 'working_day_id');
    }

    public function holidays()
    {
        return $this->belongsToMany('App\Models\WorkingPlaceHoliday', 'department_has_holidays', 'department_id', 'holiday_id');
    }

    public function events()
    {
        return $this->belongsToMany('App\Models\CalendarEvent', 'department_has_events', 'department_id', 'event_id');
    }

    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule', 'department_id', 'id');
    }

    public function devicesSchedules()
    {
        return $this->hasMany('App\Models\DevicesSchedule', 'department_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function holidaySetting()
    {
        return $this->hasMany('App\Models\HolidaySetting');
    }

    public function get_calendar_button($crud = false)
    {
        return '<a class="btn btn-xs btn-default" href="'.backpack_url('departments'). '/' . $this->id . '/calendar" data-toggle="tooltip" title="Just a demo custom button."><i class="fa fa-calendar"></i> Calendar</a>';
    }

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
