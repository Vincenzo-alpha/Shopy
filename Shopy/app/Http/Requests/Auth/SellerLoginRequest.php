<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SellerLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|max:150',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Please enter your business email address.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
            'password.min'      => 'Password must be at least 6 characters.',
        ];
    }
}
