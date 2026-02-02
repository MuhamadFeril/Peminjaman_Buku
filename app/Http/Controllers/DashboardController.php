<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Buku;
use App\Models\Anggota;
use App\Models\Peminjaman;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $users = User::count();
        $totalBuku = Buku::count();
        $totalAnggota = Anggota::count();
        $totalPeminjaman = Peminjaman::count();

        $recentPeminjaman = Peminjaman::with('Anggota')->latest()->take(5)->get();

        return view('dashboard.index', compact('totalAnggota', 'totalBuku', 'totalPeminjaman', 'users', 'recentPeminjaman'));
    }
}