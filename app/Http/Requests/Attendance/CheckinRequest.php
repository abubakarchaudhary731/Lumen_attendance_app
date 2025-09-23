<?php

namespace App\Http\Requests\Attendance;

use App\Http\Requests\BaseRequest;

class CheckinRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'notes' => 'nullable|string',
            'is_work_from_home' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'notes.string' => 'The notes must be a string.',
            'is_work_from_home.required' => 'The is_work_from_home field is required.',
        ];
    }
}
