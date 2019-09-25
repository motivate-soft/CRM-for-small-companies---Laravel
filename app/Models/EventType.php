<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\CrudTrait;

class EventType extends Model
{
    use CrudTrait;

    protected $table = 'employee_event_type';
    // protected $primaryKey = 'id';

    protected $fillable = ['name', 'color', 'company_id', 'has_to_appear', 'is_working_day'];

    public function employee_event()
    {
        return $this->hasMany('App\Models\EmployeeEvent');
    }
}
