<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Sinopsis;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
     {
         $q = $request->input('q');
         $query = Buku::query();

         if ($q) {
             $query->where(function($sub) use ($q) {
                 $sub->where('judul', 'like', "%{$q}%")
                     ->orWhere('penulis', 'like', "%{$q}%")
                     ->orWhere('tahun_terbit', 'like', "%{$q}%");
             });
         }

         $bukus = $query->latest()->paginate(15)->appends($request->only('q'));

         return view('buku.index', compact('bukus', 'q'));
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/buku')->with('error', 'Anda tidak bisa menambah buku.');
        }
        return view("buku.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $data = $request->validate([
           'judul' => 'required|string|max:255',
           'penulis' => 'nullable|string|max:255',
           'tahun_terbit' => 'nullable|integer',
           'persediaan' => 'nullable|integer|min:0',
           'cover_buku' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
       ]);

       // Set default persediaan if not provided
       if (!isset($data['persediaan']) || $data['persediaan'] === null) {
           $data['persediaan'] = 0;
       }

       // handle cover upload if present
       if ($request->hasFile('cover_buku')) {
           $path = $request->file('cover_buku')->store('covers', 'public');
           $data['cover_buku'] = $path;
       } else {
           unset($data['cover_buku']);
       }

       Buku::create($data);

       return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_buku)
    {
        $buku = Buku::where('uuid', $id_buku)->orWhere('id_buku', $id_buku)->firstOrFail();
        return view('buku.show', compact('buku'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_buku)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/buku')->with('error', 'Anda tidak bisa mengedit buku.');
        }
        $buku = Buku::where('uuid', $id_buku)->orWhere('id_buku', $id_buku)->firstOrFail();
        return view('buku.edit', compact('buku'));
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, string $id_buku)
     {
         $buku = Buku::where('uuid', $id_buku)->orWhere('id_buku', $id_buku)->firstOrFail();

         $data = $request->validate([
              'judul' => 'required|string|max:255',
              'penulis' => 'nullable|string|max:255',
              'tahun_terbit' => 'nullable|integer',
              'persediaan' => 'nullable|integer|min:0',
              'cover_buku' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
         ]);

         // Set default persediaan if not provided
         if (!isset($data['persediaan']) || $data['persediaan'] === null) {
             $data['persediaan'] = $buku->persediaan ?? 0;
         }

         if ($request->hasFile('cover_buku')) {
             $path = $request->file('cover_buku')->store('covers', 'public');
             
             // delete old cover
             if ($buku->cover_buku && \Illuminate\Support\Facades\Storage::disk('public')->exists($buku->cover_buku)) {
                 \Illuminate\Support\Facades\Storage::disk('public')->delete($buku->cover_buku);
             }
             
             $data['cover_buku'] = $path;
         } else {
             // Remove cover_buku from data if no file was uploaded
             unset($data['cover_buku']);
         }

         $buku->update($data);

         return redirect()->route('buku.index')->with('success','Buku berhasil diperbarui.');
     }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_buku)
    {
     $buku = Buku::where('uuid', $id_buku)->orWhere('id_buku', $id_buku)->first();
     if ($buku) {
         $buku->delete();
     }
     return redirect()->route("buku.index")->with("success","buku.deleted");
    }

    /** Sinopsis: show create form */
    public function createSinopsis(string $id_buku)
    {
        $buku = Buku::where('uuid', $id_buku)->orWhere('id_buku', $id_buku)->firstOrFail();
        return view('buku.sinopsis.create', compact('buku'));
    }

    /** Store sinopsis */
    public function storeSinopsis(Request $request, string $id_buku)
    {
        $buku = Buku::where('uuid', $id_buku)->orWhere('id_buku', $id_buku)->firstOrFail();

        $data = $request->validate([
            'konten' => 'required|string',
        ]);

        $sinopsis = Sinopsis::create([
            'buku_id' => $buku->id_buku,
            'konten' => $data['konten'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('buku.show', $buku->uuid ?? $buku->id_buku)->with('success', 'Sinopsis berhasil dibuat.');
    }

    /** Edit sinopsis form */
    public function editSinopsis(string $id_buku)
    {
        $buku = Buku::where('uuid', $id_buku)->orWhere('id_buku', $id_buku)->firstOrFail();
        $sinopsis = $buku->sinopsis;
        if (!$sinopsis) {
            return redirect()->route('buku.sinopsis.create', $buku->uuid ?? $buku->id_buku);
        }
        return view('buku.sinopsis.edit', compact('buku', 'sinopsis'));
    }

    /** Update sinopsis */
    public function updateSinopsis(Request $request, string $id_buku)
    {
        $buku = Buku::where('uuid', $id_buku)->orWhere('id_buku', $id_buku)->firstOrFail();
        $sinopsis = $buku->sinopsis;
        if (!$sinopsis) {
            return redirect()->route('buku.sinopsis.create', $buku->uuid ?? $buku->id_buku);
        }

        $data = $request->validate([
            'konten' => 'required|string',
        ]);

        $sinopsis->update(['konten' => $data['konten']]);

        return redirect()->route('buku.show', $buku->uuid ?? $buku->id_buku)->with('success', 'Sinopsis berhasil diperbarui.');
    }
}
