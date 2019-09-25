<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use CrudTrait;

    protected $table = 'currencies';

    protected $fillable = ['name', 'short_key', 'symbol'];

    public static function getCurrencyList()
    {
        return self::all();
    }

    public function plan()
    {
        return $this->hasOne('App\Models\Plan');
    }

    public static function getPlanData($short_key)
    {
        $currency = self::where('short_key', $short_key)->first();
        if ($currency) {
            $plan = $currency->plan;
        }

        if (!$plan) {
            $currency = self::where('short_key', 'USD')->first();
            $plan = $currency->plan;
        }

        return $plan;
    }

    public static function getCompanyCurrencyArray()
    {
        $list = self::select('id', 'short_key')->get();

        foreach ($list as $value) {
            $currency_lists[$value->id] = $value->short_key;
        }

        return $currency_lists;
    }
}
