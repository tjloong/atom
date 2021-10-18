<?php

namespace Jiannius\Atom\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AuthResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
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
            'token.required' => 'Token is missing.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',        
            'password.confirmed' => 'Please confirm your password.',
        ];
    }
}
