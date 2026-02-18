<div class="modal fade" id="guestRequestModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajukan Peminjaman sebagai Tamu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="guestRequestForm">
          <input type="hidden" name="buku_id" id="guest_buku_id" value="">
          <div class="mb-2">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" name="tanggal_pinjam" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Tanggal Kembali</label>
            <input type="date" name="tanggal_kembali" class="form-control" required>
          </div>
        </form>
        <div id="guestRequestAlert" class="mt-2" style="display:none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="guestRequestSubmit" class="btn btn-primary">Kirim Permintaan</button>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var modalEl = document.getElementById('guestRequestModal');
  var bsModal = new bootstrap.Modal(modalEl);

  // open modal helper
  window.openGuestRequest = function(bukuId){
    document.getElementById('guest_buku_id').value = bukuId || '';
    document.getElementById('guestRequestAlert').style.display = 'none';
    bsModal.show();
  }

  document.getElementById('guestRequestSubmit').addEventListener('click', function(){
    var form = document.getElementById('guestRequestForm');
    var data = new FormData(form);

    fetch("{{ url('peminjaman/guest-request') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: data
    }).then(function(res){
      return res.json();
    }).then(function(json){
      var alert = document.getElementById('guestRequestAlert');
      alert.style.display = 'block';
      if(json.success){
        alert.className = 'alert alert-success';
        alert.textContent = json.message || 'Berhasil dikirim';
        setTimeout(function(){ bsModal.hide(); }, 1200);
      } else {
        alert.className = 'alert alert-danger';
        alert.textContent = json.message || 'Terjadi kesalahan';
      }
    }).catch(function(err){
      var alert = document.getElementById('guestRequestAlert');
      alert.style.display = 'block';
      alert.className = 'alert alert-danger';
      alert.textContent = 'Terjadi kesalahan jaringan';
    });
  });
});
</script>