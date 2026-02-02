<?php
namespace App\Handler;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthHandler
{
    /**
     * Logika Login Tanpa Repository
     */
    public function login(array $data)
    {
        // 1. Cari user berdasarkan email langsung ke Model
        $user = User::where('email', $data['email'])->first();

        // 2. Jika user tidak ditemukan, "lempar" peringatan
        if (!$user) {
            throw new Exception('Maaf, akun dengan email tersebut tidak kami temukan.'); 
        }

        // 3. Jika password salah, "lempar" peringatan lagi
        if (!Hash::check($data['password'], $user->password)) {
            throw new Exception('Password yang Anda masukkan tidak cocok!'); 
        }

        // 4. Jika semua oke, kembalikan data user
        return $user;
    }
}