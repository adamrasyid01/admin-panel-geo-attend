<?php

namespace App\Http\Requests\Api;

class LoginRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus berupa alamat email yang valid',
            'email.exists' => 'Email tidak terdaftar',
            'password.required' => 'Password wajib diisi',
        ];
    }
}
