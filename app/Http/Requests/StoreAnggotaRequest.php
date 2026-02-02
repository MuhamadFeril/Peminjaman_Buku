<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ubah jadi true agar request diizinkan
        return true; 
    }

    public function rules(): array
    {
        return [
            'nama'    => 'required|string|max:255',
            'alamat'  => 'nullable|string|max:500',
            'nomor'   => 'nullable|string|regex:/^[0-9]{8,15}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama anggota wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'nomor.regex' => 'Nomor telepon harus berupa angka 8-15 digit.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
        ];
    }
}
