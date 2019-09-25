<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionsOptionRequest extends FormRequest
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
            'name' => 'required|max:255',
            'key' => [
                'required',
                'numeric',
                Rule::unique('actions_options', 'key')->where(function ($query) {
                    return $query
                        ->where('company_id', backpack_user()->company->id)
                        ->where('device_id', $this->get('device_id'));
                })->ignore($this->get('id'), 'id')
            ],
            'event_id' => 'required|numeric',
            'type' => 'required|in:in,out',
            'device_id' => 'required|exists:devices,id',
            'company_id' => 'required|exists:companies,id',
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
