<?php

namespace App\Http\Requests\DailyStandup;

use App\Http\Requests\BaseRequest;

class CreateDailyStandupRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'day_start' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'day_start.required' => 'The day start field is required.',
            'day_start.max' => 'The day start field must be a string.',
        ];
    }
}
