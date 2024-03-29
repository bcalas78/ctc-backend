<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
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
            'firstname' => 'required|string|min:2|max:60',
            'lastname' => 'required|string|min:2|max:80',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => [
                'string',
                'required',
                'min:12',
                'max:255',
                'regex:^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{12,}$^',
                'confirmed'
            ]
        ];
    }
}
