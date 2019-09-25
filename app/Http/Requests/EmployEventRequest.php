<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployEventRequest extends FormRequest
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
            'name'     => 'required|max:255',
//            'comment'    => 'required',
            'start_date' => 'date',
            'end_date' => 'date',
//            'event_type_id' => 'required'
        ];
    }
}
