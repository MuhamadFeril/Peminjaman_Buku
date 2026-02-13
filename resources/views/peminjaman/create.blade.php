@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>Tambah Peminjaman</h3>
        <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Hidden form to create Anggota for authenticated users (avoids nested forms) -->
    <form id="createAnggotaForm" method="POST" action="{{ route('anggota.createSelf') }}" style="display:none">
        @csrf
    </form>

    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Anggota</label>
            @php $anggota = auth()->check() ? auth()->user()->anggota ?? null : null; @endphp
            @if($anggota)
                <input type="text" class="form-control" value="{{ $anggota->nama }}" disabled>
                <small class="text-muted">Otomatis dari user yang login</small>
            @else
                <input type="text" class="form-control" value="Anggota tidak ditemukan" disabled>
                <small class="text-muted">Anda harus memiliki kartu anggota sebelum meminjam.</small>
                @auth
                    <div class="mt-2">
                        <a href="{{ route('anggota.createSelfForm') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-sm btn-outline-primary">Buat Kartu Anggota</a>
                    </div>
                @else
                    <div class="mt-2">
                        <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Daftar Sekarang</a>
                    </div>
                @endauth
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Buku</label>
            <select name="buku_id" id="bukuSelect" class="form-control @error('buku_id') is-invalid @enderror" required>
                <option value="">-- Pilih Buku --</option>
                @foreach($bukus as $buku)
                    <option value="{{ $buku->uuid ?? $buku->id_buku }}" {{ old('buku_id') == ($buku->uuid ?? $buku->id_buku) ? 'selected' : (isset($selected) && $selected == ($buku->uuid ?? $buku->id_buku) ? 'selected' : '') }}>{{ $buku->judul }}</option>
                @endforeach
            </select>
            @error('buku_id') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" name="tanggal_pinjam" class="form-control @error('tanggal_pinjam') is-invalid @enderror" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
            @error('tanggal_pinjam') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Kembali</label>
            <input type="date" name="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror" value="{{ old('tanggal_kembali') }}" required>
            @error('tanggal_kembali') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        @if($anggota)
            <button type="submit" class="btn btn-primary shadow-sm">Simpan Peminjaman</button>
        @else
            <button type="button" class="btn btn-primary shadow-sm" disabled title="Buat kartu anggota terlebih dahulu">Simpan Peminjaman</button>
            <div class="mt-2 small text-muted">Anda belum memiliki kartu anggota — klik "Buat Kartu Anggota" terlebih dahulu.</div>
        @endif
    </form>
</div>

<style>
    .form-control { background-color: #f8f9fa; border: 1px solid #ced4da; padding: 10px; }
    .container { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        // If server provided a selected book via query, ensure select shows it
        @if(isset($selected) && $selected)
            try { document.getElementById('bukuSelect').value = '{{ $selected }}'; } catch(e){}
        @endif

        // Show simple selected book title below select (mobile UX)
        var sel = document.getElementById('bukuSelect');
        var info = document.createElement('div');
        info.className = 'mt-2 text-muted small';
        sel.parentNode.insertBefore(info, sel.nextSibling);
        function updateInfo(){
            var opt = sel.options[sel.selectedIndex];
            info.textContent = opt && opt.value ? ('Dipilih: ' + opt.text) : '';
        }
        sel.addEventListener('change', updateInfo);
        updateInfo();
    });
</script>
@endsection
<div>
                        var opt = sel.options[sel.selectedIndex];
                        info.textContent = opt && opt.value ? ('Dipilih: ' + opt.text) : '';

                        if(card && opt && opt.value){
                            card.style.display = 'block';
                            cardImg.src = opt.getAttribute('data-cover') || 'https://via.placeholder.com/150?text=No+Image';
                            cardTitle.textContent = opt.getAttribute('data-judul') || '';
                            var metaParts = [];
                            var penulis = opt.getAttribute('data-penulis');
                            var tahun = opt.getAttribute('data-tahun');
                            if(penulis) metaParts.push(penulis);
                            if(tahun) metaParts.push(tahun);
                            cardMeta.textContent = metaParts.join(' | ');
                            cardExtra.textContent = '';
                        } else if(card){
                            card.style.display = 'none';
                        }
                    }

                    sel.addEventListener('change', updateInfo);
                    updateInfo();
                }
            });
            </script>
            </select>
            @error('buku_id') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label