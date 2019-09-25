<?php

namespace App\Http\Requests;

use App\Models\EventMandatoryType;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class MedicalDayRequest extends FormRequest
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
        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $event_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->employee->company->id)->first();
        } else if (backpack_user()->role == User::ROLE_COMPANY) {
            $event_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->company->id)->first();
        }

        $rules = [
            'date' => 'required|date',
            'employee_id' => backpack_user()->role == User::ROLE_EMPLOYEE ? 'required|in:' . backpack_user()->employee->id : 'required|in:' . backpack_user()->company->employees->implode('id', ',')
        ];

        if ($event_type && $event_type->has_file == true) {
            $rules['photo'] = 'required|max:1024|mimes:jpg,png,pdf,doc,docx';
        }

        if ($event_type && $event_type->has_confirmation == true) {
           $rules['status'] = backpack_user()->role == User::ROLE_EMPLOYEE ? 'in:pending' : 'required|in:pending,approved,rejected';
        }

        if ($event_type && $event_type->has_amount == true) {
            $rules['amount'] = 'required|numeric';
        }

        if ($event_type && $event_type->has_comment == true) {
            $rules['comment'] = 'max:255';
        }

        return $rules;
    }
}
