<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\DB;

class App extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'apps';
    // protected $primaryKey = 'id';
//     public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['app_id', 'description', 'employee_id', 'temporal_code'];
    // protected $hidden = [];
    // protected $dates = [];


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('employee_id', function (Builder $builder) {
            if(backpack_user() && backpack_user()->role == User::ROLE_COMPANY) {
                $builder->whereIn('apps.employee_id', backpack_user()->company->employees->pluck('id')->all());
            }
        });
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

    public function fcmToken()
    {
        return $this->hasMany('App\Models\FcmToken');
    }

    public function appState($crud = false)
    {
        $item = DB::table('apps')->find($this->id);
        $created_date = new Carbon($item->created_at);
        $now_date = Carbon::now();
        $difference = $created_date->diff($now_date)->days;

        if ($item->app_id) {
            return trans('fields.approved');
        } else {
          if ($difference > config('backpack_config.app_link_expire_date'))  {
              return trans('fields.expired');
          } else {
              return trans('fields.pending');
          }
        }
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
