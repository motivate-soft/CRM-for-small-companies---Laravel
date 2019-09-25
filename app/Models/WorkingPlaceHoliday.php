<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class WorkingPlaceHoliday extends Model
{
    use CrudTrait;
    protected $table = 'workingplace_hollidays';

//    protected $fillable = ['name', 'status', 'start_date', 'end_date'];
    protected $fillable = ['name', 'start_date', 'end_date', 'company_id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company_id', function (Builder $builder) {
            if (backpack_user()->role == User::ROLE_COMPANY) {
                $builder->where('company_id', backpack_user()->company->id);
            }
        });
    }

    public function workcenters()
    {
        return $this->belongsToMany('App\Models\Workcenter', 'workcenter_has_holidays', 'holiday_id', 'workcenter_id');
    }

    public function departments()
    {
        return $this->belongsToMany('App\Models\Department', 'department_has_holidays', 'holiday_id', 'department_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function get_calendar_button($crud = false)
    {
        return '<a class="btn btn-xs btn-default" href="'.backpack_url('workcenters/'). $this->id . '/calendar" data-toggle="tooltip" title="Just a demo custom button."><i class="fa fa-calendar"></i> Calendar</a>';
    }

}

