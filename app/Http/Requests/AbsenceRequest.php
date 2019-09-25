<?php

namespace App\Http\Requests;

use App\Models\EventMandatoryType;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AbsenceRequest extends FormRequest
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
//        return [
//            'start_date' => 'required|date',
//            'end_date' => 'required|date|gte:start_date',
//            'doc' => 'required|file|mimes:doc,pdf,docx|max:50000',
//            'employee_id' => backpack_user()->role == User::ROLE_EMPLOYEE ? 'required|in:' . backpack_user()->employee->id : 'required|in:' . backpack_user()->company->employees->implode('id', ',')
//        ];

        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $event_type = EventMandatoryType::where('type', 'absence')->where('company_id', backpack_user()->employee->company->id)->first();
        } else if (backpack_user()->role == User::ROLE_COMPANY) {
            $event_type = EventMandatoryType::where('type', 'absence')->where('company_id', backpack_user()->company->id)->first();
        }

        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|gte:start_date',

            'employee_id' => backpack_user()->role == User::ROLE_EMPLOYEE ? 'required|in:' . backpack_user()->employee->id : 'required|in:' . backpack_user()->company->employees->implode('id', ',')
        ];

        if ($event_type && $event_type->has_file == true) {
            $rules['doc'] = 'required|file|mimes:doc,pdf,docx|max:50000';
        }

        if ($event_type && $event_type->has_confirmation == true) {
            $rules['status'] = backpack_user()->role == User::ROLE_EMPLOYEE ? 'in:pending' : 'required|in:pending,approved,rejected';
        }

        if ($event_type && $event_type->has_amount == true) {
            $rules['amount'] = 'required|numeric';
        }

        if ($event_type && $event_type->has_comment == true) {
            $rules['comment'] = 'required|max:255';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'doc.max' => 'The document may not be greater than 50 megabytes'
        ];
    }
}
