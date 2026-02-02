<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ubah jadi true agar request diizinkan
        return true; 
    }

    public function rules(): array
    {
        return [
            'anggota_id' => 'required|exists:table_anggota,id_anggota',
            'buku_id'    => 'required|exists:table_buku,id_buku',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ];
    }
    public function messages(): array
    {
        return [
            'anggota_id.required' => 'ID anggota wajib diisi.',
            'buku_id.required' => 'ID buku wajib diisi.', 
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali harus setelah atau sama dengan tanggal pinjam.',
        ];
    }
}