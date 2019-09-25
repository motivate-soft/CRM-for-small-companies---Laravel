<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Action;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleRequest extends FormRequest
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
            'department_id' => 'required|unique:schedules,department_id|exists:departments,id',
            'department_id' => 'required|exists:departments,id',
            'data.months' => 'required|filled',
            'data.months.*' => [
                Rule::in(Action::getMonths(false, true)),
            ],
//            'data.days.*.check' => 'required_with_all:data.days.*.from,data.days.*.to',
//            'data.days.*.from' => 'required_with:data.days.*.check|required_with:data.days.*.to',
//            'data.days.*.to' => 'required_with:data.days.*.check|required_with:data.days.*.from',
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
            'data.months' => 'months',
            'data.days.1.check' => 'day',
            'data.days.2.check' => 'day',
            'data.days.3.check' => 'day',
            'data.days.4.check' => 'day',
            'data.days.5.check' => 'day',
            'data.days.6.check' => 'day',
            'data.days.7.check' => 'day',
            'data.days.*.*.from' => 'from',
            'data.days.*.*.to' => 'to',
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
