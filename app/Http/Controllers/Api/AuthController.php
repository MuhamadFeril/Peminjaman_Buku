<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan ini di-import
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input dari user
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. DI SINI TEMPATNYA: Mencoba mencocokkan email & password ke database
        if (Auth::attempt($credentials)) {
            
            // Jika cocok, ambil data user tersebut
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Buat token Passport (OAuth2)
            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token
            ], 200);
        }

        // Jika tidak cocok, kirim pesan error
        return response()->json([
            'message' => 'Email atau Password salah'
        ], 401);
    }

    public function register(Request $request)
    {
        // Validation: allow role optionally but enforce it only when authorized
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:admin,peminjam,user',
            'admin_secret' => 'nullable|string'
        ]);

        // Default role for API-created users
        $role = 'user';

        // Allow setting role only if requester is an authenticated admin
        // or if running in local environment and admin_secret matches env
        if ($request->filled('role')) {
            $requestedRole = strtolower(trim($request->input('role')));

            $canSetRole = (auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin')
                || (app()->environment('local') && $request->input('admin_secret') === env('ADMIN_SECRET'));

            if ($canSetRole && in_array($requestedRole, ['admin', 'peminjam', 'user'])) {
                $role = $requestedRole;
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
        // Mencabut (revoke) token akses yang sedang digunakan
        $request->user()->token()->revoke();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    } 
}