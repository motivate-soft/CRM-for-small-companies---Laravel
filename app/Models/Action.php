<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'actions';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['datetime', 'option_id', 'employee_id', 'gps', 'ip', 'cell', 'auth_type'];
    // protected $hidden = [];
    // protected $dates = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('employee_id', function (Builder $builder) {
            if (backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {
                $builder->whereIn('actions.employee_id', backpack_user()->company->employees->pluck('id')->all());
            }
            if (backpack_user() && backpack_user()->role == User::ROLE_EMPLOYEE) {
                $builder->where('employee_id', backpack_user()->employee->id);
            }
        });

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('datetime', 'desc');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function convertToDecimal($time)
    {
        $hms = explode(":", $time);
        return number_format(($hms[0] + ($hms[1] / 60) + ($hms[2] / 3600)), 2);
    }

    public static function getPartialHoursByEmployeeActions($actions)
    {
        $res = '';
        for ($i = 0; $i < $actions->count(); $i++) {
            if ($actions[$i]->option->type == 'in' && isset($actions[$i + 1])) {
                $res .= Carbon::parse($actions[$i]->datetime)->diff(Carbon::parse($actions[++$i]->datetime))->format('%H:%I:%S') . '<br>';
            }
        }

        return $res;
    }

    public static function getTotalPausesByEmployeeActions($actions)
    {
        $seconds = 0;
        $actions = $actions->reverse()->values();
        for ($i = 0; $i < $actions->count(); $i++) {
            if ($actions[$i]->option->type == 'out' && isset($actions[$i + 1]) && $actions[$i + 1]->option->type == 'in' && $actions->last()->id != $actions[$i + 1]->id) {
                $seconds += (int)Carbon::parse($actions[$i]->datetime)->diffInSeconds(Carbon::parse($actions[++$i]->datetime));
            }
        }
        return Carbon::now()->diff(Carbon::now()->addSeconds($seconds))->format('%H:%I:%S');
    }

    public static function getDailyTotalByEmployeeActions($actions)
    {
        $seconds = 0;
        for ($i = 0; $i < $actions->count(); $i++) {
            if ($actions[$i]->option->type == 'in' && isset($actions[$i + 1])) {
                $seconds += (int)Carbon::parse($actions[$i]->datetime)->diffInSeconds(Carbon::parse($actions[++$i]->datetime));
            }
        }

        return Carbon::now()->diff(Carbon::now()->addSeconds($seconds))->format('%H:%I:%S');
    }

    public static function getTotalByEmployeeActions($actions)
    {
        $seconds = 0;
        $actions = $actions->reverse()->values();
        for ($i = 0; $i < $actions->count(); $i++) {
            if ($actions[$i]->option->type == 'in' && isset($actions[$i + 1]) && $actions[$i + 1]->option->type == 'out' && Carbon::parse($actions[$i]->datetime)->toDateString() == Carbon::parse($actions[$i + 1]->datetime)->toDateString()) {
                $seconds += (int)Carbon::parse($actions[$i]->datetime)->diffInSeconds(Carbon::parse($actions[++$i]->datetime));
            }
        }

        $time = Carbon::now()->addSeconds($seconds);

        $hours = $time->diffInHours();
        $minutes = $time->subHours($hours)->diffInMinutes();

        return "{$hours}h{$minutes}m";
    }

    public static function getTotalTimeByEmployees($collection, $group = true)
    {
        if ($group) {
            $collection = $collection->groupBy([
                'employee_id'
            ]);
        }

        $total_seconds = 0;

        foreach ($collection as $employee_id => $actions) {
            $seconds = 0;
            $actions = $actions->reverse()->values();
            for ($i = 0; $i < $actions->count(); $i++) {
                if ($actions[$i]->option->type == 'in' && isset($actions[$i + 1]) && $actions[$i + 1]->option->type == 'out' && Carbon::parse($actions[$i]->datetime)->toDateString() == Carbon::parse($actions[$i + 1]->datetime)->toDateString()) {
                    $seconds += (int)Carbon::parse($actions[$i]->datetime)->diffInSeconds(Carbon::parse($actions[++$i]->datetime));
                }
            }
            $total_seconds += $seconds;
        }

        $time = Carbon::now()->addSeconds($total_seconds);

        $hours = $time->diffInHours();
        $minutes = $time->subHours($hours)->diffInMinutes();

        return "{$hours}h{$minutes}m";
    }

    public static function getHoursWorkedInSchedule($items, $day, $schedule, $filters)
    {
        $schedule = $schedule->data;

        $seconds = 0;

        if (isset($items[$day])) {

            $actions = $items[$day]->reverse()->values();
            $date = Carbon::createFromFormat('d/m/Y', $day . '/' . $filters['date']);
            $day_of_week = $date->dayOfWeekIso;
            $month = $date->month;

            if (in_array($month, $schedule['months']) && isset($schedule['days'][$day_of_week])) {
                for ($i = 0; $i < $actions->count(); $i++) {
                    foreach ($schedule['days'][$day_of_week]['times'] as $time) {

                        $schedule_start_time = Carbon::parse($time['from']);
                        $schedule_end_time = Carbon::parse($time['to']);

                        $action_time = Carbon::parse(Carbon::parse($actions[$i]->datetime)->toTimeString());

                        if ($actions[$i]->option->type == 'in' && isset($actions[$i + 1]) && $actions[$i + 1]->option->type == 'out') {
                            $next_action_time = Carbon::parse(Carbon::parse($actions[$i + 1]->datetime)->toTimeString());
                            if ($next_action_time->between($schedule_start_time, $schedule_end_time) && $action_time->between($schedule_start_time, $schedule_end_time)) {
                                $seconds += $action_time->diffInRealSeconds($next_action_time);
                            } else if ($action_time->between($schedule_start_time, $schedule_end_time)) {
                                $seconds += $action_time->diffInRealSeconds($schedule_end_time);
                            } else if ($action_time->lt($schedule_start_time) && $next_action_time->gt($schedule_end_time)) {
                                $seconds += $schedule_start_time->diffInRealSeconds($schedule_end_time);
                            }
                        }

                        if ($actions[$i]->option->type == 'out' && isset($actions[$i - 1]) && $actions[$i - 1]->option->type == 'in') {
                            $prev_action_time = Carbon::parse(Carbon::parse($actions[$i - 1]->datetime)->toTimeString());
                            if (!$prev_action_time->between($schedule_start_time, $schedule_end_time) && $action_time->between($schedule_start_time, $schedule_end_time)) {
                                $seconds += $schedule_start_time->diffInRealSeconds($action_time);
                            }
                        }
                    }
                }
            }
        }

        return Carbon::now()->diff(Carbon::now()->addSeconds($seconds))->format('%H:%I:%S');
    }

    public static function getMonths($month = false, $getKeys = false)
    {
        $months = array(
            1 => trans('fields.january'),
            2 => trans('fields.february'),
            3 => trans('fields.march'),
            4 => trans('fields.april'),
            5 => trans('fields.may'),
            6 => trans('fields.june'),
            7 => trans('fields.july'),
            8 => trans('fields.august'),
            9 => trans('fields.september'),
            10 => trans('fields.october'),
            11 => trans('fields.november'),
            12 => trans('fields.december'),
        );

        if ($month) {
            return $months[$month];
        }

        if ($getKeys) {
            return array_keys($months);
        }

        return $months;
    }

    public static function getDaysOfWeek($day = false, $getKeys = false)
    {
        $days = array(
            1 => trans('fields.monday'),
            2 => trans('fields.tuesday'),
            3 => trans('fields.wednesday'),
            4 => trans('fields.thursday'),
            5 => trans('fields.friday'),
            6 => trans('fields.saturday'),
            7 => trans('fields.sunday'),
        );

        if ($day) {
            return $days[$day];
        }

        return $days;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function option()
    {
        return $this->belongsTo('App\Models\ActionsOption', 'option_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeWorkcenter($query, $id)
    {
        if (strtolower($id) != 'all' && $id != null) {
            return $query->whereIn('employee_id', Workcenter::find($id)->employees->pluck('id')->all());
        } else {
            return null;
        }
    }

    public function scopeSchedule($query, $id)
    {
        if (strtolower($id) != 'all' && $id != null) {
            return $query->whereIn('employee_id', Schedule::find($id)->department->employees->pluck('id')->all());
        } else {
            return null;
        }
    }

    public function scopeEmployee($query, $id)
    {
        if (strtolower($id) != 'all' && $id != null) {
            return $query->where('employee_id', $id);
        } else {
            return null;
        }
    }

    public function scopeDateRange($query, $date)
    {
        if ($date) {
            $start_date = date('Y-m-d', strtotime(trim(explode('-', $date)[0])));
            $end_date = date('Y-m-d', strtotime(trim(explode('-', $date)[1])));

            return $query
                ->whereDate('datetime', '>=', $start_date)
                ->whereDate('datetime', '<=', $end_date);
        } else {
            return null;
        }
    }

    public function scopeDate($query, $date)
    {
        if ($date) {
            $m = trim(explode('/', $date)[0]);
            $y = trim(explode('/', $date)[1]);

            return $query
                ->whereMonth('datetime', '=', $m)
                ->whereYear('datetime', '=', $y);
        } else {
            return null;
        }
    }

    public function scopeYear($query, $date)
    {
        if ($date) {
            return $query->whereYear('datetime', '=', $date);
        } else {
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    public function getDeviceAttribute()
    {
        if ($this->option != null) {
            return $this->option->device->name;
        } else {
            return "Tracking";
        }        
    }

    public function getMap()
    {
        return '<a href="javascript:void(0)">' . $this->getGPS() . '</a>';
    }

    public function getGPS()
    {

        if ($this->gps == "")
            return $this->ip;
        else
            return $this->gps;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
