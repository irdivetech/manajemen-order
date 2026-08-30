<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['sometimes', 'required', 'string', 'max:255'],
            'email'    => [
                'sometimes', 
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($this->route('user'))
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role'     => ['sometimes', 'required', 'in:admin,owner'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'name.max'            => 'Nama maksimal 255 karakter.',
            'email.required'      => 'Alamat email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah digunakan oleh pengguna lain.',
            'password.confirmed'  => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'        => 'Kata sandi minimal harus :min karakter.',
            'role.required'       => 'Peran wajib dipilih.',
            'role.in'             => 'Peran yang dipilih tidak valid.',
        ];
    }
}
