<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidaysettingRequest extends FormRequest
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
            'year' => 'required|numeric',
            'expire_date' => 'required|date',
            'default_holidays_per_employee' => 'required|numeric|min:1|max:365',
        ];
    }
}
