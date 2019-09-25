<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Schedule extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'schedules';
    // protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'data', 'department_id', 'is_device'];
    // protected $hidden = [];
    // protected $dates = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('schedule_id', function (Builder $builder) {
            if(backpack_user()->role == 'company') {
                $builder->whereIn('department_id', backpack_user()->company->departments->pluck('id')->all());
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

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeDeviceSchedules($query)
    {
        return $query->where('is_device', 1);
    }

    public function scopeNotDeviceSchedules($query)
    {
        return $query->where('is_device', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    public function getDataAttribute($value)
    {
        return unserialize($value);
    }

    public function getWorkingTimesAttribute()
    {
        $data = $this->data;

        $result = [];

        if($data) {
            foreach ($data['days'] as $day_key => $day) {
                $seconds = 0;

                foreach ($day['times'] as $time) {
                    $seconds += (int)Carbon::parse($time['from'])->diffInSeconds(Carbon::parse($time['to']));
                }

                $result[$day_key] = Carbon::now()->diff(Carbon::now()->addSeconds($seconds))->format('%H:%I');
            }
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setDataAttribute($value)
    {
        foreach ($value['days'] as $key => &$item) {
            if(!isset($item['check']) || !$item['check'] || (!$item['times'][0]['from'] && !$item['times'][0]['to']) || (!$item['times'][1]['from'] && !$item['times'][1]['to'])) {
                unset($value['days'][$key]);
            }
        }

        $this->attributes['data'] = serialize($value);
    }
}
