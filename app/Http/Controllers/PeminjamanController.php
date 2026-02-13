<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index()
        {
                $user = auth()->user();
                // Admin melihat semua, user biasa atau tamu juga melihat daftar (sesuaikan jika ingin filter)
                if ($user && isset($user->role) && $user->role === 'admin') {
                    $peminjamans = Peminjaman::with(['Anggota','Buku'])->latest()->paginate(15);
                } else {
                    // Untuk user biasa atau tamu, tampilkan daftar peminjaman umum
                    $peminjamans = Peminjaman::with(['Anggota','Buku'])->latest()->paginate(15);
                }
            return view('peminjaman.index', compact('peminjamans'));
        }

    /*
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bukus = Buku::where('persediaan','>',0)->get();
        $selected = $request->query('buku');
        return view('peminjaman.create', compact('bukus','selected'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\PeminjamanStoreRequest $request)
    {
       $user = auth()->user();

       // Cari anggota berdasarkan user_id terlebih dahulu.
       // Beberapa schema tidak menyimpan email di table_anggota, jadi jangan query kolom yang tidak ada.
       $anggota = null;
       if ($user) {
           $anggota = Anggota::where('user_id', $user->id)->first();
           // fallback: coba cari berdasarkan nama anggota yang cocok dengan nama user
           if (! $anggota && ! empty($user->name)) {
               $anggota = Anggota::where('nama', $user->name)->first();
           }
       }
       
       if (!$anggota) {
           return redirect()->back()->withErrors(['error' => 'Anggota terkait dengan user ini tidak ditemukan']);
       }

       $data = $request->validated();

       $data['anggota_id'] = $anggota->id_anggota;

        // Ensure book has stock and decrement atomically
        return DB::transaction(function () use ($data) {
            // accept uuid or numeric id
            $buku = Buku::where('uuid', $data['buku_id'])->orWhere('id_buku', $data['buku_id'])->lockForUpdate()->first();
            if (! $buku) {
                return redirect()->back()->withErrors(['buku_id' => 'Buku tidak ditemukan'])->withInput();
            }

            if ($buku->persediaan <= 0) {
                return redirect()->back()->withErrors(['buku_id' => 'Stok buku tidak tersedia'])->withInput();
            }

            $buku->persediaan = max(0, $buku->persediaan - 1);
            $buku->save();

            Peminjaman::create([
                'anggota_id' => $data['anggota_id'],
                'buku_id' => $buku->id_buku,
                'tanggal_pinjam' => $data['tanggal_pinjam'],
                'tanggal_kembali' => $data['tanggal_kembali'],
            ]);

            return redirect()->route('peminjaman.index')->with('success','Peminjaman berhasil ditambahkan.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_peminjaman)
    {
        $peminjaman = Peminjaman::with(['Anggota','Buku'])
            ->where('uuid', $id_peminjaman)
            ->orWhere('id_peminjaman', $id_peminjaman)
            ->firstOrFail();
        return view('peminjaman.show', compact('peminjaman'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_peminjaman)
    {
        $peminjaman = Peminjaman::where('uuid', $id_peminjaman)->orWhere('id_peminjaman', $id_peminjaman)->firstOrFail();
        $user = auth()->user();
        if (!($user && isset($user->role) && $user->role === 'admin')) {
            // non-admin can only edit their own peminjaman
            if (! $peminjaman->Anggota || $peminjaman->Anggota->user_id !== $user->id) {
                abort(403);
            }
            $bukus = Buku::all();
            return view('peminjaman.edit', compact('peminjaman','bukus'));
        }

        $anggotas = Anggota::all();
        $bukus = Buku::all();
        return view('peminjaman.edit', compact('peminjaman','anggotas','bukus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_peminjaman)
    {
        $peminjaman = Peminjaman::where('uuid', $id_peminjaman)->orWhere('id_peminjaman', $id_peminjaman)->firstOrFail();

        $user = auth()->user();
        if (!($user && isset($user->role) && $user->role === 'admin')) {
            if (! $peminjaman->Anggota || $peminjaman->Anggota->user_id !== $user->id) {
                abort(403);
            }
        }

        $data = $request->validate([
            'anggota_id' => 'required',
            'buku_id' => 'required',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        return DB::transaction(function () use ($peminjaman, $data) {
            // resolve anggota and buku (accept uuid or id)
            $anggota = Anggota::where('uuid', $data['anggota_id'])->orWhere('id_anggota', $data['anggota_id'])->first();
            if (! $anggota) {
                return redirect()->back()->withErrors(['anggota_id' => 'Anggota tidak ditemukan'])->withInput();
            }

            $newBuku = Buku::where('uuid', $data['buku_id'])->orWhere('id_buku', $data['buku_id'])->lockForUpdate()->first();
            if (! $newBuku) {
                return redirect()->back()->withErrors(['buku_id' => 'Buku tidak ditemukan'])->withInput();
            }

            // If book changed, adjust stock: return one to old book, take one from new book
            if ($peminjaman->buku_id != $newBuku->id_buku) {
                // restore stock to previous book
                $old = Buku::lockForUpdate()->find($peminjaman->buku_id);
                if ($old) {
                    $old->persediaan = $old->persediaan + 1;
                    $old->save();
                }

                if ($newBuku->persediaan <= 0) {
                    return redirect()->back()->withErrors(['buku_id' => 'Buku tujuan tidak tersedia'])->withInput();
                }

                $newBuku->persediaan = max(0, $newBuku->persediaan - 1);
                $newBuku->save();
            }

            $peminjaman->update([
                'anggota_id' => $anggota->id_anggota,
                'buku_id' => $newBuku->id_buku,
                'tanggal_pinjam' => $data['tanggal_pinjam'],
                'tanggal_kembali' => $data['tanggal_kembali'],
            ]);

            return redirect()->route('peminjaman.index')->with('success','Peminjaman berhasil diperbarui');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_peminjaman)
    {
        $peminjaman = Peminjaman::where('uuid', $id_peminjaman)->orWhere('id_peminjaman', $id_peminjaman)->firstOrFail();
        $user = auth()->user();
        if (!($user && isset($user->role) && $user->role === 'admin')) {
            if (! $peminjaman->Anggota || $peminjaman->Anggota->user_id !== $user->id) {
                abort(403);
            }
        }

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

    /**
     * Accept a guest borrow request (does not modify stock) and store in session for admin review.
     */
    public function guestRequest(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:200',
            'buku_id' => 'required',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        // resolve buku title for convenience (do not change stock)
        $buku = Buku::where('uuid', $data['buku_id'])->orWhere('id_buku', $data['buku_id'])->first();
        $data['buku_title'] = $buku ? $buku->judul : null;

        // push to session as guest requests (lightweight, for demo/admin review)
        $request->session()->push('guest_requests', $data);

        // log for server-side visibility
        Log::info('Guest borrow request added', $data);

        return response()->json(['success' => true, 'message' => 'Permintaan peminjaman telah dikirim. Kami akan menghubungi Anda.']);
    }
}
