<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'currency_id' => 'unique:plans,currency_id',
            'data' => 'required',
//            'data.*.name' => 'required|filled',
            'data.*.min' => 'required|filled|numeric',
            'data.*.max' => 'required|filled|numeric',
            'data.*.price' => 'required|filled|numeric',
            'free_month' => 'required|numeric'
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
//            'data.*.name' => trans('fields.name'),
            'data.*.min' => trans('fields.min'),
            'data.*.max' => trans('fields.max'),
            'data.*.price' => trans('fields.price'),
        ];
    }
}
