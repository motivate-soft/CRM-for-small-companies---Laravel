<?php

namespace App\Models;

use App\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Storage;

class Holiday extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'holidays';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
//    protected $fillable = ['status', 'start_date', 'end_date', 'employee_id', 'event_type_id'];
    protected $fillable = ['status', 'start_date', 'end_date', 'employee_id', 'event_type_id', 'doc', 'comment', 'amount', 'reject_message', 'cancel_state', 'company_is_read', 'employee_is_read', 'real_holiday_days'];
    // protected $hidden = [];
    // protected $dates = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('employee_id', function (Builder $builder) {
            if(backpack_user() && backpack_user()->role == User::ROLE_EMPLOYEE) {
                $builder->where('employee_id', backpack_user()->employee->id);
            }
            if(backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {
                $builder->whereIn('employee_id', backpack_user()->company->employees->pluck('id')->all());
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

    /*public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }*/
	
	public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function event_type()
    {
        return $this->belongsTo('App\Models\EventMandatoryType');
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

    public function getEmployeeNameAttribute()
    {
        return $this->employee->user->name;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getDocAttribute($value)
    {
        return Storage::disk('uploads')->url($value);
    }

    public function setDocAttribute($value)
    {
        $this->uploadFileToDisk($value, 'doc', 'uploads', 'holidays/' . backpack_user()->employee->id);
    }

}
