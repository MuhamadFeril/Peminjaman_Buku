<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ubah jadi true agar request diizinkan
        return true; 
    }

    public function rules(): array
    {
        return [
            'judul'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'persediaan'   => 'required|integer|min:0',
        ];

    }
    public function messages(): array{
        return [
            'judul.required' => 'Judul buku wajib diisi.',
            'penulis.required' => 'Penulis buku wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.min' => 'Tahun terbit tidak valid.',
            'tahun_terbit.max' => 'Tahun terbit tidak valid.',
            'persediaan.required' => 'Persediaan buku wajib diisi.',
            'persediaan.min' => 'Persediaan tidak boleh kurang dari 0.',
        ];
    }
}
