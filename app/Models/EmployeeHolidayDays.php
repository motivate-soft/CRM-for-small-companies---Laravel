<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\CrudTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class EmployeeHolidayDays extends Model
{
    use CrudTrait;

    protected $table = 'employee_holiday_days';

    protected $fillable = ['user_id', 'spend_holidays', 'year'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('user_id', function (Builder $builder) {
            if(backpack_user() && backpack_user()->role == User::ROLE_EMPLOYEE) {
                $builder->where('user_id', backpack_user()->id);
            }
            if(backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {
                $employee_ids = backpack_user()->company->employees->pluck('id');
                $user_ids = [];
                foreach ($employee_ids as $employee_id){
                    array_push($user_ids, Employee::find($employee_id)->user_id);
                }
                $builder->whereIn('user_id', $user_ids);
            }
        });
    }


    public function user()
    {
        return $this->belongsTo('App\User');
    }

//    public static function update_holiday_days($holiday_id, $type) {
//        $holiday = Holiday::find($holiday_id);
//        $start_date = $holiday->start_date;
//        $end_date = $holiday->end_date;
//
//        $start = new DateTime($start_date);
//        $end = new DateTime($end_date);
//        $interval = $start->diff($end);
//        $days = $interval->format('%a');
//
//        $employee_holidayDays = EmployeeHolidayDays::where('employee_id', $holiday->employee_id)->where('year', date('Y'))->first();
//        if (!$employee_holidayDays) {
//            $employee_holidayDays = new EmployeeHolidayDays();
//            $employee_holidayDays->employee_id = $holiday->employee_id;
//            $employee_holidayDays->spend_holidays = 0;
//            $employee_holidayDays->year = date('Y');
//            $employee_holidayDays->save();
//        }
//
//        if ($type == 'add') {
//            $employee_holidayDays->spend_holidays = (int)$employee_holidayDays->spend_holidays + (int)$days + 1;
//        } else {
//            $employee_holidayDays->spend_holidays = (int)$employee_holidayDays->spend_holidays - (int)$days - 1;
//        }
//        $employee_holidayDays->save();
//        return $employee_holidayDays;
//    }

    public static function getDefaultHolidays($item) {
        $employee_entered_year = Carbon::createFromFormat('Y-m-d H:i:s', $item->employee->created_at)->year;
        return $employee_entered_year;
    }

    public function getHolidaysAttribute()
    {
        return $this->user->holiday_days;
    }

    public function workingDays()
    {
        return $this->belongsToMany('App\Models\WorkingDays', 'user_has_working_days', 'user_id', 'working_day_id', 'user_id');
    }
	
	public function show_detail($crud = false){
        return '<a class="btn btn-xs btn-default" href="'.backpack_url('employee'). '/' . $this->user_id . '/detail" data-toggle="tooltip" title="Detail"><i class="fa fa-navicon"></i>'.trans('fields.detail').'</a>';
    }

}
