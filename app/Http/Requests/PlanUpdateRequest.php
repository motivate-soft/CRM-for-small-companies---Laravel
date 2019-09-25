<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class PlanUpdateRequest extends FormRequest
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
//            'currency_id' => 'unique:plans,currency_id',
            'data' => 'required',
            'currency_id' => [
                'required',
                Rule::unique('plans', 'currency_id')->ignore($this->get('id'), 'id')
            ],
            'data.*.min' => 'required|filled|numeric',
            'data.*.max' => 'required|filled|numeric',
            'data.*.price' => 'required|filled|numeric',
            'free_month' => 'required|numeric'
        ];
    }

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
