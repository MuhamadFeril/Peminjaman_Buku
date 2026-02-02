<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;

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
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
        ]);

        Anggota::create($data);

        return redirect()->route('anggota.index')->with('success','Anggota berhasil ditambahkan.');
    }
    public function show($id)
    {
        $anggota = Anggota::findOrFail($id);
        return view('anggota.show', compact('anggota'));
    }
    public function edit($id)
    {
        $anggota = Anggota::findOrFail($id);
        return view('anggota.edit', compact('anggota'));
    }
    public function update(Request $request, $id)
    {
        $anggota = Anggota::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
        ]);

        $anggota->update($data);

        return redirect()->route('anggota.index')->with('success','Anggota berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete();
        return redirect()->route('anggota.index')->with('success','Anggota berhasil dihapus.');
    }


}   