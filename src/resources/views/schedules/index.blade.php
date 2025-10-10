@extends('layouts/contentNavbarLayout')

@section('title', 'Jadwal Mengajar')

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
            <th style="width: 5%;">No</th>
            <th style="width: 25%;">Mata Kuliah</th>
            <th style="width: 10%;">Hari</th>
            <th style="width: 15%;">Jam Mulai</th>
            <th style="width: 15%;">Jam Selesai</th>
            <th style="width: 15%;">Ruangan</th>
            <th style="width: 15%;">Aksi</th>
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

    $('#btnAddSchedule').click(() => {
        $('#scheduleForm')[0].reset();
        $('#scheduleId').val('');
        $('#scheduleModalLabel').text('Tambah Jadwal');
        $('#saveScheduleBtn').text('Simpan');
        $('#scheduleModal').modal('show');
    });

    $('#scheduleForm').submit(function(e){
        e.preventDefault();
        let id = $('#scheduleId').val();
        let method = id ? 'PUT' : 'POST';
        let url = id ? `/api/schedule/${id}` : '/api/schedule';

        $.ajax({
            url, type: method,
            data: {
                course_name: $('#courseName').val(),
                day: $('#day').val(),
                start_time: $('#startTime').val(),
                end_time: $('#endTime').val(),
                room: $('#room').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(){
                $('#scheduleModal').modal('hide');
                Swal.fire('Berhasil!', 'Data disimpan.', 'success');
                table.ajax.reload();
            },
            error: function(){
                Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
            }
        });
    });

    $(document).on('click', '.editBtn', function() {
        let row = table.row($(this).parents('tr')).data();
        $('#scheduleId').val(row.id);
        $('#courseName').val(row.course_name);
        $('#day').val(row.day);
        $('#startTime').val(row.start_time);
        $('#endTime').val(row.end_time);
        $('#room').val(row.room);
        $('#scheduleModalLabel').text('Edit Jadwal');
        $('#saveScheduleBtn').text('Update');
        $('#scheduleModal').modal('show');
    });

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
                $.ajax({
                    url: `/api/schedule/${id}`,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(){
                        Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success');
                        table.ajax.reload();
                    },
                    error: function(){
                        Swal.fire('Gagal!', 'Gagal menghapus data.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
