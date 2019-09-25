<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class HolidaySetting extends Model
{
    use CrudTrait;

    protected $table = 'department_holiday_setting';

    protected $fillable = ['department_id', 'year', 'expire_date', 'default_holidays_per_employee'];

    public static function getYearOption()
    {
        $year_list = range(2018, 2050);
        $res_object = array();
        foreach ($year_list as $item){
            $res_object[$item] = $item;
        }

        return $res_object;
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }
}
