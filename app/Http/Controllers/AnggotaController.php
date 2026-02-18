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
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // If anggota exists for this user, show the same self-service form pre-filled
        $anggota = Anggota::where('user_id', $user->id)->first();

        // pass existing anggota (if any) to the create view so user can complete it
        return view('anggota.create', compact('anggota'));
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

        // Require the user to fill full details via the self-service form (alamat & nomor).
        // Redirect to the self-service create form. Preserve redirect param if provided.
        $redirect = $request->input('redirect') ?: route('peminjaman.create');
        return redirect()->route('anggota.createSelfForm', ['redirect' => $redirect]);
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
        // Require full details for self-service creation. Accept formatted phone input and
        // sanitize it before saving (allow spaces, +, dashes in input).
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'nomor' => 'required|string|min:6|max:40',
        ]);

        try {
            // Use updateOrCreate to be resilient: if an anggota already exists for this user, update it;
            // otherwise create a new record. This avoids duplicate-key errors and makes the flow "auto-berhasil".
            // sanitize phone: keep digits only
            $raw = $request->input('nomor');
            $digits = preg_replace('/\D+/', '', (string) $raw);
            if (empty($digits)) {
                return redirect()->back()->withErrors(['nomor' => 'Nomor telepon tidak valid'])->withInput();
            }

            $anggota = Anggota::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $request->input('nama'),
                    'alamat' => $request->input('alamat') ?: '-',
                    'nomor' => (int) $digits,
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