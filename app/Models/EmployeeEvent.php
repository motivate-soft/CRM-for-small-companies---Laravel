<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\CrudTrait;

class EmployeeEvent extends Model
{
    use CrudTrait;

    protected $table = 'employee_event';
    // protected $primaryKey = 'id';

    protected $fillable = ['name', 'employee_id', 'event_type_id', 'start_date', 'end_date', 'file', 'comment', 'amount', 'status', 'company_is_read', 'employee_is_read'];


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

    public function event_type() {
        return $this->belongsTo('App\Models\EventType');
    }

    public function employee() {
        return $this->belongsTo('App\Models\Employee');
    }

    public function company() {
            return $this->belongsTo('App\Models\Company');
    }

}
