<?php

namespace App\Http\Requests\UsersRequest;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class UpdateUserRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->input('id');
        $authUser = auth()->user();
        $rules = [
            'name' => 'sometimes|string|max:50',
            'phone_number' => ['sometimes', 'string', 'max:20', Rule::unique('users')->ignore($userId)],
            'password' => 'sometimes|string|min:6',
        ];

        if ($authUser && !in_array($authUser->role, [UserRole::ADMIN->value, UserRole::HR->value])) {
            $rules['old_password'] = 'required_with:password|string';
            $rules['role'] = 'prohibited';
            $rules['email'] = 'prohibited';
            $rules['status'] = 'prohibited';
        }

        if ($authUser && in_array($authUser->role, [UserRole::ADMIN->value, UserRole::HR->value])) {
            $rules = array_merge($rules, [
                'email' => ['sometimes', 'string', 'email', 'max:50', Rule::unique('users')->ignore($userId)],
                'role' => 'sometimes|in:' . implode(',', UserRole::values()),
                'status' => 'sometimes|in:' . implode(',', UserStatus::values()),
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'old_password.required_with' => 'You must provide your current password to set a new one.',
            'email.prohibited' => 'You do not have permission to update user email.',
            'role.prohibited' => 'You do not have permission to update user role.',
            'status.prohibited' => 'You do not have permission to update user status.',
        ];
    }
}
