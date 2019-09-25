<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
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
            'name'     => 'required|max:255',
            'email'    => 'required|max:255|unique:users,email,' . $this->get('user_id'),
            'password' => 'confirmed',
            'employee_uid' => [
                'required',
                'max:255',
                Rule::unique('employees', 'employee_uid')->where(function ($query) {
                    return $query->where('company_id', backpack_user()->company->id);
                })->ignore($this->get('id'), 'id')
            ],
//            'photo' => 'max:255',
            'nif' => 'max:255',
            'affiliation' => 'max:255',
//            'holiday_days' => 'numeric|max:365',
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
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
