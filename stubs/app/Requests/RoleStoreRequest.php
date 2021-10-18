<?php

namespace App\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role.name' => [
                'sometimes',
                'required',
                Rule::unique('roles', 'name')->ignore(request()->input('role.id')),
            ],
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
            'role.name.required' => 'Role name is required.',
            'role.name.unique' => 'There is another role with the same name.',
        ];
    }
}
