<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use Illuminate\Support\Facades\Log;

class AnggotaController extends Controller
{

    public function index()
    {
        $anggotas = Anggota::latest()->paginate(15);
       return view('anggota.index', compact('anggotas'));
    }
    public function create()
    {
        return view('anggota.create');
    }

    /**
     * Show a self-service form for the current user to create their Anggota card.
     */
    public function createSelfForm(Request $request)
    {
        // If user already has anggota, redirect back
        $user = $request->user();
        if ($user && Anggota::where('user_id', $user->id)->exists()) {
            return redirect()->route('peminjaman.create')->with('success', 'Kartu anggota sudah ada.');
        }
        return view('anggota.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            // accept either 'nomor' or 'telepon' from form
            'nomor' => 'nullable|numeric',
            'telepon' => 'nullable|string|max:50',
            // admin may assign anggota to a user
            'user_id' => 'nullable|exists:users,id',
        ]);

        // map telepon -> nomor if nomor not provided
        if (empty($data['nomor']) && ! empty($data['telepon'])) {
            $data['nomor'] = preg_replace('/[^0-9]/', '', $data['telepon']);
        }

        // Ensure nomor has a value for DB (integer column)
        $data['nomor'] = isset($data['nomor']) ? (int) $data['nomor'] : 0;

        $current = $request->user();

        // Determine user association: only admins may set arbitrary user_id.
        if (! empty($data['user_id']) && $current && isset($current->role) && $current->role === 'admin') {
            $userId = $data['user_id'];
        } else {
            $userId = $current ? $current->id : null;
        }

        if ($userId) {
            $exists = Anggota::where('user_id', $userId)->first();
            if ($exists) {
                // If admin was creating for another user, redirect to anggota index with message
                if ($current && isset($current->role) && $current->role === 'admin') {
                    return redirect()->route('anggota.index')->with('success', 'Kartu anggota untuk user tersebut sudah ada.');
                }
                return redirect()->route('peminjaman.create')->with('success', 'Kartu anggota sudah ada.');
            }
        }

        $anggota = Anggota::create([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'] ?? '',
            'nomor' => $data['nomor'],
            'user_id' => $userId,
        ]);

        if ($current && isset($current->role) && $current->role === 'admin') {
            return redirect()->route('anggota.index')->with('success','Anggota berhasil ditambahkan.');
        }

        return redirect()->route('peminjaman.create')->with('success','Kartu anggota berhasil dibuat.');
    }
    public function show($id)
    {
        $anggota = Anggota::where('uuid', $id)->orWhere('id_anggota', $id)->firstOrFail();
        return view('anggota.show', compact('anggota'));
    }
    public function edit($id)
    {
        $anggota = Anggota::where('uuid', $id)->orWhere('id_anggota', $id)->firstOrFail();
        return view('anggota.edit', compact('anggota'));
    }
    public function update(Request $request, $id)
    {
        $anggota = Anggota::where('uuid', $id)->orWhere('id_anggota', $id)->firstOrFail();

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'nomor' => 'nullable|numeric',
            'telepon' => 'nullable|string|max:50',
        ]);

        if (empty($data['nomor']) && ! empty($data['telepon'])) {
            $data['nomor'] = preg_replace('/[^0-9]/', '', $data['telepon']);
        }
        $data['nomor'] = isset($data['nomor']) ? (int) $data['nomor'] : $anggota->nomor ?? 0;

        $anggota->update([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'] ?? '',
            'nomor' => $data['nomor'],
        ]);

        return redirect()->route('anggota.index')->with('success','Anggota berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $anggota = Anggota::where('uuid', $id)->orWhere('id_anggota', $id)->firstOrFail();
        $anggota->delete();
        return redirect()->route('anggota.index')->with('success','Anggota berhasil dihapus.');
    }


    /**
     * Create a simple Anggota record for the currently authenticated user.
     * This is intended for regular users who need a member card created for them.
     */
    public function createSelf(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->back()->withErrors(['error' => 'User tidak terautentikasi']);
        }

        // If anggota already exists, redirect
        $exists = Anggota::where('user_id', $user->id)->first();
        if ($exists) {
            return redirect()->back()->with('success','Kartu anggota sudah ada.');
        }

        try {
            Anggota::create([
                'nama' => $user->name,
                'user_id' => $user->id,
                // populate non-nullable fields with safe defaults
                'alamat' => '',
                'nomor' => 0,
            ]);
        } catch (\Exception $e) {
            // log and show friendly message
            Log::error("Failed creating Anggota for user {$user->id}: {$e->getMessage()}");
            return redirect()->back()->withErrors(['error' => 'Gagal membuat kartu anggota: ' . $e->getMessage()]);
        }

        return redirect()->back()->with('success','Kartu anggota berhasil dibuat.');
    }

    /**
     * Store anggota created from self-service form (with full details).
     */
    public function storeSelf(Request $request)
    {
        
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }
        // Minimal validation to avoid failing on unexpected inputs
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'nomor' => 'nullable|numeric',
        ]);

        try {
            // Use updateOrCreate to be resilient: if an anggota already exists for this user, update it;
            // otherwise create a new record. This avoids duplicate-key errors and makes the flow "auto-berhasil".
            $anggota = Anggota::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $request->input('nama'),
                    'alamat' => $request->input('alamat') ?: '-',
                    'nomor' => $request->input('nomor') ? (int)$request->input('nomor') : rand(1000, 9999),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed creating/updating Anggota self for user '.$user->id.': '.$e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal membuat kartu anggota: ' . $e->getMessage()]);
        }

        // After creating, redirect to where user came from if provided
        $redirect = $request->input('redirect') ?: route('peminjaman.create');
        return redirect($redirect)->with('success', 'Kartu anggota berhasil dibuat.');
    }

}   