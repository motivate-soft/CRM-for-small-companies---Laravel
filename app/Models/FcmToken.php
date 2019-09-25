<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $table = 'fcm_token';

    protected $fillable = ['token', 'app_id'];

    public function notification()
    {
        return $this->hasMany('App\Models\Notification', 'token_id', 'id');
    }

    public function apps()
    {
        return $this->belongsTo('App\Models\App');
    }
}
