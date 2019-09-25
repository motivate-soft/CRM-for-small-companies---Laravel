<?php

namespace App;

use Backpack\CRUD\CrudTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use CrudTrait;

    const ROLE_ADMIN = 'admin';
    const ROLE_COMPANY = 'company';
    const ROLE_EMPLOYEE = 'employee';

    const STATUS_APPROVED = 'approved';
    const STATUS_DISABLED = 'disabled';
    const STATUS_BANNED = 'banned';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'role', 'status', 'registration_code', 'registration_code_expired', 'last_login_at', 'holiday_days'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
//        'password', 'remember_token',
        'remember_token',
    ];

    public function company()
    {
        return $this->hasOne('App\Models\Company');
    }

    public function employee()
    {
        return $this->hasOne('App\Models\Employee');
    }

    public function workingDays()
    {
        return $this->belongsToMany('App\Models\WorkingDays', 'user_has_working_days', 'user_id', 'working_day_id');
    }

    public function employeeHolidayDays()
    {
        return $this->hasMany('App\Models\EmployeeHolidayDays');
    }

    public function getAvatar()
    {
        if($this->role == self::ROLE_EMPLOYEE && $this->employee->photo) {
            return $this->employee->photo;
        }

        return 'https://placehold.it/160x160/605ca8/ffffff/&text=' . strtoupper($this->name[0]);
    }
}
