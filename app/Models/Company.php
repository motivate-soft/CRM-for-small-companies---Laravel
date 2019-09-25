<?php

namespace App\Models;

use App\GlobalConstant;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Laravel\Cashier\Billable;
use Stripe\Stripe;
use TheSeer\Tokenizer\Token;

class Company extends Model
{
    use CrudTrait;

    use Billable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'companies';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['user_id', 'vat_number', 'address', 'country', 'signatory', 'language_id', 'timezone', 'access_company_token', 'client_id'];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function createMandatoryEntities($company_id)
    {
        $event = Event::create([
            'event_id' => 1,
            'name' => trans('fields.work'),
            'company_id' => $company_id,
            'mandatory' => '1'
        ]);

        $device_1 = Device::create([
            'name' => trans('fields.web'),
            'device_id' => '1',
            'sub_device_id' => '1',
            'ip' => '0.0.0.0',
            'port' => '4444',
            'company_id' => $company_id,
            'mandatory' => '1'
        ]);

        $device_2 = Device::create([
            'name' => trans('fields.app'),
            'device_id' => '2',
            'sub_device_id' => '1',
            'ip' => '0.0.0.0',
            'port' => '4444',
            'company_id' => $company_id,
            'mandatory' => '1'
        ]);

        ActionsOption::create([
            'name' => trans('fields.entering_work'),
            'key' => '0',
            'event_id' => $event->id,
            'type' => 'in',
            'company_id' => $company_id,
            'device_id' => $device_1->id,
            'mandatory' => '1'
        ]);

        ActionsOption::create([
            'name' => trans('fields.leaving_work'),
            'key' => '1',
            'event_id' => $event->id,
            'type' => 'out',
            'company_id' => $company_id,
            'device_id' => $device_1->id,
            'mandatory' => '1'
        ]);

        ActionsOption::create([
            'name' => trans('fields.entering_work'),
            'key' => '0',
            'event_id' => $event->id,
            'type' => 'in',
            'company_id' => $company_id,
            'device_id' => $device_2->id,
            'mandatory' => '1'
        ]);

        ActionsOption::create([
            'name' => trans('fields.leaving_work'),
            'key' => '1',
            'event_id' => $event->id,
            'type' => 'out',
            'company_id' => $company_id,
            'device_id' => $device_2->id,
            'mandatory' => '1'
        ]);


        EventMandatoryType::create([
            'name' => trans('fields.holiday'),
            'company_id' => $company_id,
            'has_to_appear' => true,
            'has_confirmation' => true,
            'has_file' => false,
            'has_comment' => true,
            'has_amount' => false,
            'color' => '#008080',
            'type' => 'holiday'
        ]);

        EventMandatoryType::create([
            'name' => trans('fields.expense'),
            'company_id' => $company_id,
            'has_to_appear' => false,
            'has_confirmation' => true,
            'has_file' => true,
            'has_comment' => true,
            'has_amount' => true,
            'color' => '#ff8000',
            'type' => 'expense'
        ]);

        EventMandatoryType::create([
            'name' => trans('fields.medical_day'),
            'company_id' => $company_id,
            'has_to_appear' => true,
            'has_confirmation' => false,
            'has_file' => true,
            'has_comment' => true,
            'has_amount' => false,
            'color' => '#8080c0',
            'type' => 'medical_day'
        ]);

        EventMandatoryType::create([
            'name' => trans('fields.absence'),
            'company_id' => $company_id,
            'has_to_appear' => true,
            'has_confirmation' => false,
            'has_file' => false,
            'has_comment' => true,
            'has_amount' => false,
            'color' => '#ff80c0',
            'type' => 'absence'
        ]);

        EventMandatoryType::create([
            'name' => trans('fields.incident'),
            'company_id' => $company_id,
            'has_to_appear' => false,
            'has_confirmation' => true,
            'has_file' => true,
            'has_comment' => true,
            'has_amount' => false,
            'color' => '#0000a0',
            'type' => 'incident'
        ]);
    }

    public static function createAccessToken()
    {
        $token = str_random(50);

        if(Company::where('access_company_token', $token)->first()) {
            self::createAccessToken();
        }

        return $token;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo('App\User');
    }
	
	public function alert(){
        return $this->hasOne('App\Models\Alert');
    }
	
    public function workcenters()
    {
        return $this->hasMany('App\Models\Workcenter');
    }

    public function employees()
    {
        return $this->hasMany('App\Models\Employee');
    }

    public function holidays()
    {
        return $this->hasMany('App\Models\WorkingPlaceHoliday');
    }

    public function workingDays()
    {
        return $this->belongsToMany('App\Models\WorkingDays', 'company_has_working_days', 'company_id', 'working_day_id');
    }

    public function devices()
    {
        return $this->hasMany('App\Models\Device');
    }

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }


    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function departments()
    {
        return $this->hasMany('App\Models\Department');
    }

    public function actionsOptions()
    {
        return $this->hasMany('App\Models\ActionsOption', 'company_id', 'id');
    }

    public function employee_event()
    {
        return $this->hasMany('App\Models\EmployeeEvent');
    }

    public function companyPlan()
    {
        return $this->hasOne('App\Models\CompanyPlan');
    }

    public function transaction()
    {
        return $this->hasMany('App\Models\PaymentTransaction');
    }

    public function stripeModel()
    {
        return $this->hasOne('App\Models\StripeModel');
    }
    
    public function web_token(){
        return $this->hasMany('App\Models\WebToken', 'user_id', 'user_id');
    }

    public function paypalModel()
    {
        return $this->hasOne('App\Models\PaypalModel');
    }

    protected function removeTableFromKey($key)
    {
        return parent::removeTableFromKey($key); // TODO: Change the autogenerated stub
    }

    public function company_currency()
    {
        return $this->hasOne('App\Models\CompanyCurrency');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
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

    public function getSignatoryAttribute($value)
    {
        return $value ? Storage::disk('uploads')->url($value) : null;
    }

    public function getDevicesCountAttribute()
    {
        $count = $this->devices->count();
        return $count ?: '-';
    }

    public function getNameAttribute()
    {
        return $this->user->name;
    }

    public function getStatusAttribute()
    {
        return $this->user->status;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function change_password_button($crud = false){
        return '<a class="btn btn-xs btn-default" href="'.backpack_url('companies'). '/' . $this->id . '/change_password" data-toggle="tooltip" title="Change Password"><i class="fa fa-cog"></i> Change Password</a>';
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setSignatoryAttribute($value)
    {
        $attribute_name = "signatory";
        $disk = "uploads";
        $destination_path = 'signatures/' . $this->id;

        if (request()->file('signatory_file')) {
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


    public static function getMaxEmployees()
    {
        $plan = backpack_user()->company->companyPlan;
        if ($plan) {
            $plan_type = explode('_', explode('-', $plan->company_plan_id)[1])[1];

            if ($plan->billing_status == GlobalConstant::COMPANY_PLAN_STATUS_UNLIMITED) {
                return array(
                    'billing_status' =>  GlobalConstant::COMPANY_PLAN_STATUS_UNLIMITED
                );
            } else {
                $plan_data = json_decode($plan->plan->data, true);
                $max_employees = $plan_data[$plan_type]['max'];
                return array(
                    'billing_status' =>  $plan->billing_status,
                    'employee_num' => $max_employees
                );
            }
        } else {
            return false;
        }
    }
}
