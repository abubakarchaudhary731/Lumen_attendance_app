<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BaseRequest extends Request
{
    protected array $rules = [];
    protected array $messages = [];

    public function validated(): array
    {
        $data = $this->json()->all();

        $validator = Validator::make(
            $data,
            $this->rules(),
            $this->messages()
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function messages(): array
    {
        return $this->messages;
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        // If it's a JSON request, merge the JSON data
        if ($this->isJson()) {
            $jsonData = $this->json()->all();
            $data = array_merge($data, $jsonData);
        }

        return $data;
    }
}
