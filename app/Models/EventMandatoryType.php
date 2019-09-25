<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EventMandatoryType extends Model
{
    use CrudTrait;
    public $timestamps = false;
    protected $table = 'employee_event_mandatory_type';
    // protected $primaryKey = 'id';

    protected $fillable = ['name', 'color', 'has_confirmation', 'has_file', 'has_comment', 'has_amount', 'has_to_appear', 'company_id', 'type'];

    protected static function boot()
    {
        parent::boot();
        if(backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {
            static::addGlobalScope('company_id', function (Builder $builder) {
                $builder->where('company_id', '=', backpack_user()->company->id);
            });
        }
        else if(backpack_user() && backpack_user()->role == User::ROLE_EMPLOYEE) {
            static::addGlobalScope('company_id', function (Builder $builder) {
                $builder->where('company_id', '=', backpack_user()->employee->company->id);
            });
        }
    }
}
