<!-- Modal Add/Edit Course -->
<div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="courseModalLabel">Tambah Mata Kuliah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Body -->
      <form id="courseForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="courseId">

          <div class="col-md-12 mb-3">
            <div class="col-md-12 mb-3">
              <label for="courseName" class="form-label fw-semibold">Nama Mata Kuliah</label>
              <input type="text" id="courseName" name="name" class="form-control" placeholder="Contoh: Pemrograman Web" required>
            </div>
            <div class="col-md-12 mb-3">
              <label for="courseCode" class="form-label fw-semibold">Kode</label>
              <input type="text" id="courseCode" name="code" class="form-control" placeholder="CT101" required>
            </div>
            <div class="col-md-12 mb-3">
              <label for="courseSks" class="form-label fw-semibold">SKS</label>
              <input type="number" id="courseSks" name="sks" class="form-control" placeholder="3" required>
            </div>
            {{-- <div class="col-md-12 mb-3">
              <label for="lecturer" class="form-label fw-semibold">Dosen Pengampu</label>
              <input type="text" id="le" name="category" class="form-control" placeholder="Misal: Wajib / Pilihan">
            </div> --}}
            <div class="col-md-12 mb-3">
            <label for="lecturerSupervise" class="form-label">Dosen Pengampu</label>
            <select class="form-select" id="lecturerSupervise" aria-label="Default select example">

            </select>
          </div>
            <div class="col-md-12 mb-3">
              <label for="courseDescription" class="form-label fw-semibold">Deskripsi (Opsional)</label>
              <textarea id="courseDescription" name="description" class="form-control" rows="2" placeholder="Tulis deskripsi singkat mata kuliah..."></textarea>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn " data-bs-dismiss="modal">
             Batal
          </button>
          <button type="submit" class="btn btn-primary" id="saveCourseBtn">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
