<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role'     => ['required', 'in:admin,owner'],
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
            'password.required'   => 'Kata sandi wajib diisi.',
            'password.confirmed'  => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'        => 'Kata sandi minimal harus :min karakter.',
            'role.required'       => 'Peran wajib dipilih.',
            'role.in'             => 'Peran yang dipilih tidak valid.',
        ];
    }
}
