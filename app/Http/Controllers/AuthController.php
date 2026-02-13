<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Anggota;
use Illuminate\Support\Facades\Log;
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard.index'));
        }

        return back()->withErrors(['email' => 'Credentials do not match our records'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,admin',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
        ]);

        // Create a default Anggota (member card) for the newly registered user
        try {
            Anggota::create([
                'nama' => $data['name'],
                'user_id' => $user->id,
                'alamat' => '',
                'nomor' => 0,
            ]);
        } catch (\Exception $e) {
            // If anggota table/schema is not present or creation fails, log and continue
            Log::warning('Failed to auto-create Anggota for user: ' . $user->id . ' - ' . $e->getMessage());
        }

        // Do not auto-login after registration. Redirect to login page so user can
        // explicitly authenticate (prevents confusion where to go after registering).
        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
