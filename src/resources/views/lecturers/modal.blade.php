<div class="modal fade" id="lecturerModal" tabindex="-1" aria-labelledby="lecturerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="lecturerModalLabel">Tambah Dosen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="lecturerForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="lecturerIdHidden">
          <input type="hidden" id="lecturerId" name="lecturer_id">

          <div class="col-md-12 mb-3">
            <label for="lecturerName" class="form-label fw-semibold">Nama Dosen</label>
            <input type="text" id="lecturerName" class="form-control" placeholder="Contoh: Dr. Andi Prasetyo" required>
          </div>

          <div class="col-md-12 mb-3">
            <label for="lecturerCode" class="form-label fw-semibold">Kode Dosen</label>
            <input type="text" id="lecturerCode" class="form-control" placeholder="CT-L001" required>
          </div>

          <div class="col-md-12 mb-3">
            <label for="lecturerDescription" class="form-label fw-semibold">Deskripsi (Opsional)</label>
            <textarea id="lecturerDescription" class="form-control" rows="2" placeholder="Bisa diisi no telepon atau keterangan lainnya"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="saveLecturerBtn">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
