<?php

namespace App\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TeamStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'team.name' => [
                'sometimes',
                'required',
                Rule::unique('teams', 'name')->ignore(request()->input('team.id')),
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
            'team.name.required' => 'Team name is required.',
            'team.name.unique' => 'There is another team with the same name.',
        ];
    }
}
