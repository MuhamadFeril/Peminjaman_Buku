<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
        public function rules()
        {
            return [
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:8',
                'password_confirmation'=>'required|string|min:8',
                'role' => 'nullable|in:admin,user,peminjam',
            ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'password.required' => 'Password wajib diisi.',
            
            'password.min' => 'Password minimal 8 karakter.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'password_confirmation.string' => 'Konfirmasi password harus berupa teks.',
            'password_confirmation.min' => 'Konfirmasi password minimal 8 karakter.',
        ];
    }
}