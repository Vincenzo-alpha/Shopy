<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|min:3|max:150',
            'email'         => 'required|email|max:150|unique:sk_customer_master,email',
            'contact_no'    => ['required', 'string', 'max:20', 'regex:/^[\+0-9\s\-\(\)]{7,20}$/'],
            'password'      => 'required|string|min:6|confirmed',
            'address'       => 'required|string|min:10|max:500',
            'city'          => 'required|string|min:2|max:100',
            'state'         => 'required|string|min:2|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Full name is required.',
            'customer_name.min'      => 'Name must be at least 3 characters.',
            'email.required'         => 'Email address is required.',
            'email.email'            => 'Please enter a valid email address.',
            'email.unique'           => 'This email is already registered. Please sign in.',
            'contact_no.required'    => 'Contact number is required.',
            'contact_no.regex'       => 'Enter a valid contact number (digits, spaces, +, -, ()).',
            'password.required'      => 'Password is required.',
            'password.min'           => 'Password must be at least 6 characters.',
            'password.confirmed'     => 'Passwords do not match.',
            'address.required'       => 'Please enter your delivery/collection address.',
            'address.min'            => 'Address must be at least 10 characters.',
            'city.required'          => 'City is required.',
            'state.required'         => 'State is required.',
        ];
    }
}
