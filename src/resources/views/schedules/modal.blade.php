<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="scheduleModalLabel" class="modal-title fw-bold">Tambah Jadwal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="scheduleForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="scheduleId">

          <div class="mb-3">
            <label class="form-label">Mata Kuliah</label>
            <select id="courseSelect" class="form-control" required></select>
          </div>

          <div class="mb-3">
            <label class="form-label">Dosen Pengampu</label>
            <select id="lecturerSelect" class="form-control" required></select>
          </div>

          <div class="mb-3">
            <label class="form-label">Hari</label>
            <select id="day" class="form-control" required>
              <option value="">Pilih Hari</option>
              <option>Senin</option>
              <option>Selasa</option>
              <option>Rabu</option>
              <option>Kamis</option>
              <option>Jumat</option>
              <option>Sabtu</option>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Jam Mulai</label>
              <input type="time" id="startTime" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Jam Selesai</label>
              <input type="time" id="endTime" class="form-control" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Ruangan</label>
            <input type="text" id="room" class="form-control" placeholder="Misal: Lab 201" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="saveScheduleBtn">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
