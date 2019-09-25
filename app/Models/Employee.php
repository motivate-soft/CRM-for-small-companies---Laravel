<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Employee extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'employees';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['user_id', 'company_id', 'employee_uid', 'photo', 'nif', 'workcenter_id', 'department_id', 'affiliation', 'alias', 'auth_type', 'holiday_days', 'language_id'];
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

    public function user()
    {
        return $this->belongsTo('App\User');
    }

//    public function workcenters()
//    {
//        return $this->belongsToMany('App\Models\Workcenter', 'workcenter_has_employees', 'employee_id', 'workcenter_id');
//    }
//
//    public function departments()
//    {
//        return $this->belongsToMany('App\Models\Department', 'department_has_employees', 'employee_id', 'department_id');
//    }
    /*public function holidays()
    {
        return $this->belongsTo('App\Models\Holiday');
    }*/
	
	public function holidays(){
        return $this->hasMany('App\Model\Holiday');
    }

    public function workcenter()
    {
        return $this->belongsTo('App\Models\Workcenter');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function actions()
    {
        return $this->hasMany('App\Models\Action');
    }

    public function apps()
    {
        return $this->hasMany('App\Models\App');
    }

    public function employee_event()
    {
        return $this->hasMany('App\Models\EmployeeEvent');
    }

    public function webToken()
    {
        return $this->hasMany('App\Models\WebToken', 'user_id', 'user_id');
    }

    public function workingDays()
    {
        return $this->belongsToMany('App\Models\WorkingDays', 'user_has_working_days', 'user_id', 'working_day_id', 'user_id');
    }
	
	public function language()
    {
        return $this->belongsTo('App\Models\Language');
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

    public function getNameAttribute()
    {
        return $this->user->name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getPhotoAttribute($value)
    {
        return $value ? Storage::disk('uploads')->url($value) : null;
    }

    public function get_calendar_button($crud = false)
    {
        return '<a class="btn btn-xs btn-default" href="'.backpack_url('employees'). '/' . $this->id . '/calendar" data-toggle="tooltip" title="Calendar"><i class="fa fa-calendar"></i>' .trans('fields.calendar').'</a>';
    }

    public function change_password_button($crud = false){
        return '<a class="btn btn-xs btn-default" href="'.backpack_url('employees'). '/' . $this->id . '/change_password" data-toggle="tooltip"><i class="fa fa-cog"></i>'.trans('fields.change_password').' </a>';
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

     public function setPhotoAttribute($value)
     {
         $attribute_name = "photo";
         $disk = "uploads";
         $destination_path = 'employees/' . $this->id;
         if (request()->file('photo_file')) {
             $this->attributes[$attribute_name] = $value;
         } else {
             if ($value == null) {
                 Storage::disk($disk)->delete($this->{$attribute_name});
                 $this->attributes[$attribute_name] = null;
             }

             if (starts_with($value, 'data:image')) {
                 $image = Image::make($value)->encode('jpg', 90);
                 $filename = md5($value . time()) . '.jpg';
                 Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
                 $this->attributes[$attribute_name] = $destination_path . '/' . $filename;
             }
         }
     }

    public function getHolidaysAttribute()
    {
        return $this->user->holiday_days;
    }
}
