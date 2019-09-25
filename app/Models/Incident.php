<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Incident extends Model
{
    use CrudTrait;

    protected $table = 'incident';

    protected $fillable = ['status', 'employee_id', 'event_type_id', 'photo', 'comment', 'amount', 'reject_message', 'company_is_read', 'employee_is_read'];

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

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }

    public function event_type()
    {
        return $this->belongsTo('App\Models\EventMandatoryType');
    }

    public function getDocAttribute($value)
    {
        return Storage::disk('uploads')->url($value);
    }

    public function setDocAttribute($value)
    {
        $this->uploadFileToDisk($value, 'photo', 'uploads', 'incidents/' . backpack_user()->employee->id);
    }

    public function setPhotoAttribute($value)
    {
        $attribute_name = "photo";
        $disk = "uploads";
        $destination_path = 'incidents/' . $this->id;

        if ($value == null) {
            Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        if (starts_with($value, 'data:image')) {
            $image = Image::make($value)->encode('jpg', 90);
            $filename = md5($value . time()) . '.jpg';
            Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
            $this->attributes[$attribute_name] = $destination_path . '' . $filename;
        }
    }

    public function getPhotoAttribute($value)
    {
        return $value ? Storage::disk('uploads')->url($value) : null;
    }

    public function getEmployeeNameAttribute()
    {
        return $this->employee->user->name;
    }
}
