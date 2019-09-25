<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;

class WorkingPlaceRequest extends FormRequest
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
            'name' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|gte:start_date',
//            'status' => backpack_user()->role == User::ROLE_EMPLOYEE ? 'required|in:pending' : 'required|in:pending,approved,rejected',
        ];
    }
}
