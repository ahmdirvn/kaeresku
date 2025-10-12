@extends('layouts/contentNavbarLayout')

@section('title', 'Jadwal Kuliah')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Jadwal Kuliah</h2>
    <button class="btn btn-primary" id="btnAddSchedule">
      <i class="bx bx-plus"></i> Tambah Jadwal
    </button>
  </div>

  <div class="card px-5 py-4">
    <h5 class="card-header px-5">Daftar Jadwal</h5>
    <div class="table-responsive text-nowrap px-5">
      <table id="scheduleTable" class="table table-striped text-center align-middle">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th class="text-center">Mata Kuliah</th>
            <th class="text-center">Dosen</th>
            <th class="text-center">Hari</th>
            <th class="text-center">Jam Mulai</th>
            <th class="text-center">Jam Selesai</th>
            <th class="text-center">Ruangan</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

@include('schedules.modal')
@endsection

@section('page-script2')
<script>
$(document).ready(function () {
    let table = $('#scheduleTable').DataTable({
        ajax: '/api/schedule',
        columns: [
            { data: null, render: (d,t,r,m)=>m.row+1 },
            { data: 'course_name' },
            { data: 'lecturer_name' },
            { data: 'day' },
            { data: 'start_time' },
            { data: 'end_time' },
            { data: 'room' },
            {
                data: 'id',
                render: data => `
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item editBtn" data-id="${data}">
                      <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>
                    <a class="dropdown-item deleteBtn" data-id="${data}">
                      <i class="bx bx-trash me-1"></i> Delete
                    </a>
                  </div>
                </div>`
            }
        ]
    });

    // open modal add
    $('#btnAddSchedule').click(() => {
        $('#scheduleForm')[0].reset();
        $('#scheduleId').val('');
        $('#scheduleModalLabel').text('Tambah Jadwal');
        $('#saveScheduleBtn').text('Simpan');

        Swal.fire({
          title: 'Memuat Data...',
          text: 'Harap tunggu.',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });

        Promise.all([
          $.getJSON('/api/courses'),
          $.getJSON('/api/lecturer')
        ]).then(([courses, lecturers]) => {
            Swal.close();

            $('#courseSelect').empty().append('<option value="">Pilih Mata Kuliah</option>');
            courses.data.forEach(c => $('#courseSelect').append(`<option value="${c.id}">${c.name}</option>`));

            $('#lecturerSelect').empty().append('<option value="">Pilih Dosen</option>');
            lecturers.data.forEach(l => $('#lecturerSelect').append(`<option value="${l.lecturer_id}">${l.lecturer_name}</option>`));

            $('#scheduleModal').modal('show');
        }).catch(() => {
            Swal.fire('Gagal!', 'Tidak bisa memuat data dosen atau mata kuliah.', 'error');
        });
    });

    // submit form
    $('#scheduleForm').submit(function(e){
        e.preventDefault();
        let id = $('#scheduleId').val();
        let method = id ? 'PUT' : 'POST';
        let url = id ? `/api/schedule/${id}` : '/api/schedule';

        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url, type: method,
            data: {
                course_id: $('#courseSelect').val(),
                lecturer_id: $('#lecturerSelect').val(),
                day: $('#day').val(),
                start_time: $('#startTime').val(),
                end_time: $('#endTime').val(),
                room: $('#room').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(){
                Swal.close();
                $('#scheduleModal').modal('hide');
                Swal.fire('Berhasil!', 'Data berhasil disimpan.', 'success');
                table.ajax.reload();
            },
            error: function(){
                Swal.close();
                Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan data.', 'error');
            }
        });
    });

    // edit
    $(document).on('click', '.editBtn', function() {
        let row = table.row($(this).parents('tr')).data();
        $('#scheduleId').val(row.id);
        $('#day').val(row.day);
        $('#startTime').val(row.start_time);
        $('#endTime').val(row.end_time);
        $('#room').val(row.room);

        Swal.fire({
          title: 'Memuat Data...',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });

        Promise.all([
          $.getJSON('/api/courses'),
          $.getJSON('/api/lecturer')
        ]).then(([courses, lecturers]) => {
            Swal.close();

            $('#courseSelect').empty().append('<option value="">Pilih Mata Kuliah</option>');
            courses.data.forEach(c => {
                $('#courseSelect').append(`<option value="${c.id}" ${c.id===row.course_id?'selected':''}>${c.name}</option>`);
            });

            $('#lecturerSelect').empty().append('<option value="">Pilih Dosen</option>');
            lecturers.data.forEach(l => {
                $('#lecturerSelect').append(`<option value="${l.lecturer_id}" ${l.lecturer_id===row.lecturer_id?'selected':''}>${l.lecturer_name}</option>`);
            });

            $('#scheduleModalLabel').text('Edit Jadwal');
            $('#saveScheduleBtn').text('Update');
            $('#scheduleModal').modal('show');
        });
    });

    // delete
    $(document).on('click', '.deleteBtn', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            text: 'Data tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.ajax({
                    url: `/api/schedule/${id}`,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(){
                        Swal.close();
                        Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success');
                        table.ajax.reload();
                    },
                    error: function(){
                        Swal.close();
                        Swal.fire('Gagal!', 'Gagal menghapus data.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
