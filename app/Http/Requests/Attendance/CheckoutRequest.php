<?php

namespace App\Http\Requests\Attendance;

use App\Http\Requests\BaseRequest;

class CheckoutRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'notes' => 'nullable|string',
            'is_half_day' => 'required|boolean',
            'is_overtime' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'notes.string' => 'The notes must be a string.',
            'is_half_day.required' => 'The is_half_day field is required.',
            'is_overtime.required' => 'The is_overtime field is required.',
        ];
    }
}
