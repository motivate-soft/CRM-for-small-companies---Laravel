<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebToken extends Model
{
    protected $table = 'web_token';

    protected $fillable = ['token', 'user_id'];

    public function notification()
    {
        return $this->hasMany('App\Models\Notification', 'token_id', 'id');
    }

    public function employees()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
