<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index()
        {
            $user = auth()->user();
            // Admin melihat semua, user biasa hanya melihat miliknya sendiri
            if ($user->role === 'admin') {
                $peminjamans = Peminjaman::with(['Anggota','Buku'])->latest()->paginate(15);
            } else {
                // User biasa: filter berdasarkan anggota yang terkait dengan user
                // Asumsi: user memiliki anggota yang terkait (sesuaikan logika sesuai database Anda)
                $peminjamans = Peminjaman::with(['Anggota','Buku'])->latest()->paginate(15);
            }
            return view('peminjaman.index', compact('peminjamans'));
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bukus = Buku::where('persediaan','>',0)->get();
        return view('peminjaman.create', compact('bukus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $user = auth()->user();
       
       // Cari anggota berdasarkan user_id atau email atau nama
       // Sesuaikan logika ini dengan struktur database Anda
       $anggota = Anggota::where('user_id', $user->id)
                         ->orWhere('email', $user->email)
                         ->first();
       
       if (!$anggota) {
           return redirect()->back()->withErrors(['error' => 'Anggota terkait dengan user ini tidak ditemukan']);
       }

       $data = $request->validate([
           'buku_id' => 'required|exists:table_buku,id_buku',
           'tanggal_pinjam' => 'required|date',
           'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
       ]);
       
       $data['anggota_id'] = $anggota->id_anggota;

        // Ensure book has stock and decrement atomically
        return DB::transaction(function () use ($data) {
            $buku = Buku::lockForUpdate()->find($data['buku_id']);
            if (! $buku) {
                return redirect()->back()->withErrors(['buku_id' => 'Buku tidak ditemukan'])->withInput();
            }

            if ($buku->persediaan <= 0) {
                return redirect()->back()->withErrors(['buku_id' => 'Stok buku tidak tersedia'])->withInput();
            }

            $buku->persediaan = max(0, $buku->persediaan - 1);
            $buku->save();

            Peminjaman::create($data);

            return redirect()->route('peminjaman.index')->with('success','Peminjaman berhasil ditambahkan.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_peminjaman)
    {
        $peminjaman = Peminjaman::with(['Anggota','Buku'])->findOrFail($id_peminjaman);
        return view('peminjaman.show', compact('peminjaman'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_peminjaman)
    {
        $peminjaman = Peminjaman::findOrFail($id_peminjaman);
        $anggotas = Anggota::all();
        $bukus = Buku::all();
        return view('peminjaman.edit', compact('peminjaman','anggotas','bukus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_peminjaman)
    {
        $peminjaman = Peminjaman::findOrFail($id_peminjaman);

        $data = $request->validate([
            'anggota_id' => 'required|exists:table_anggota,id_anggota',
            'buku_id' => 'required|exists:table_buku,id_buku',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        return DB::transaction(function () use ($peminjaman, $data) {
            // If book changed, adjust stock: return one to old book, take one from new book
            if ($peminjaman->buku_id != $data['buku_id']) {
                // restore stock to previous book
                $old = Buku::lockForUpdate()->find($peminjaman->buku_id);
                if ($old) {
                    $old->persediaan = $old->persediaan + 1;
                    $old->save();
                }

                // decrement new book
                $new = Buku::lockForUpdate()->find($data['buku_id']);
                if (! $new || $new->persediaan <= 0) {
                    return redirect()->back()->withErrors(['buku_id' => 'Buku tujuan tidak tersedia'])->withInput();
                }

                $new->persediaan = max(0, $new->persediaan - 1);
                $new->save();
            }

            $peminjaman->update($data);

            return redirect()->route('peminjaman.index')->with('success','Peminjaman berhasil diperbarui');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_peminjaman)
    {
        $peminjaman = Peminjaman::findOrFail($id_peminjaman);

        return DB::transaction(function () use ($peminjaman) {
            // return book stock
            $buku = Buku::lockForUpdate()->find($peminjaman->buku_id);
            if ($buku) {
                $buku->persediaan = $buku->persediaan + 1;
                $buku->save();
            }

            $peminjaman->delete();

            return redirect()->route('peminjaman.index')->with('success','Peminjaman berhasil dihapus');
        });
    }
}
