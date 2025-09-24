<?php

namespace App\Http\Requests\Attendance;

use App\Http\Requests\BaseRequest;
use App\Enums\Attendance\AttendanceStatus;

class UpdateAttendanceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'check_in' => ['sometimes', 'date_format:H:i:s'],
            'check_out' => ['sometimes', 'date_format:H:i:s', 'after:check_in'],
            'is_work_from_home' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'in:' . implode(',', array_column(AttendanceStatus::cases(), 'value'))],
            'notes' => ['sometimes', 'string', 'nullable', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'check_in.date_format' => 'The check in time must be in 24-hour format (HH:MM:SS).',
            'check_out.date_format' => 'The check out time must be in 24-hour format (HH:MM:SS).',
            'check_out.after' => 'The check out time must be after check in time.',
        ];
    }
}
