<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWebPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect('/login');
        }

        // Check permission based on role
        $allowed = $this->checkPermission($user->role, $permission);

        if (!$allowed) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }

    private function checkPermission($role, $permission)
    {
        $permissions = [
            'buku.view' => ['admin', 'user'],
            'buku.manage' => ['admin'],
            
            'anggota.view' => ['admin'],
            'anggota.manage' => ['admin'],
            
            'peminjaman.view' => ['admin', 'user'],
            'peminjaman.manage' => ['admin', 'user'],
        ];

        return isset($permissions[$permission]) && in_array($role, $permissions[$permission]);
    }
}
