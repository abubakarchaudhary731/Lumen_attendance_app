<?php

namespace App\Http\Requests\UsersRequest;

use App\Enums\UserRole;
use App\Http\Requests\BaseRequest;

class CreateUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:' . implode(',', UserRole::values()),
            'phone_number' => 'string|max:20|unique:users',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'role.required' => 'The role field is required.',
            'role.in' => 'The selected role is invalid.',
            'phone_number.unique' => 'This phone number is already registered.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
        ];
    }
}
