<?php

namespace App\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user.name' => 'sometimes|required',
            'user.email' => [
                'sometimes',
                'required',
                Rule::unique('users', 'email')->ignore($this->input('user.id')),
            ],
            'user.role_id' => 'sometimes|required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'user.name.required' => 'Name is required.',
            'user.email.required' => 'Login email is required.',
            'user.email.unique' => 'There is already another account with the same email.',
            'user.role_id.required' => 'Please assign a role to user.',
        ];
    }
}
