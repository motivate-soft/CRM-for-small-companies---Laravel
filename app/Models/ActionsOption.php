<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class ActionsOption extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'actions_options';
    // protected $primaryKey = 'id';
     public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'key', 'event_id', 'type', 'company_id', 'device_id', 'mandatory'];
    // protected $hidden = [];
    // protected $dates = [];

    protected static function boot()
    {
        parent::boot();

        if(backpack_user()) {
            static::addGlobalScope('company_id', function (Builder $builder) {
                if (backpack_user()->role == User::ROLE_COMPANY) {
                    $builder->where('company_id', '=', backpack_user()->company->id);
                }
                if (backpack_user()->role == User::ROLE_EMPLOYEE) {
                    $builder->where('company_id', '=', backpack_user()->employee->company->id);
                }
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

    public function action()
    {
        return $this->belongsTo('App\Models\Action', 'action_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo('App\Models\Device', 'device_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Event', 'event_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeWeb($query)
    {
        return $query->whereHas('device', function($q) {
            $q->where('device_id', 1);
        });
    }

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
