@extends('layouts/contentNavbarLayout')
@section('title', 'Daftar Dosen')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Daftar Dosen</h2>
    <button class="btn btn-primary" id="btnAddLecturer">
      <i class="bx bx-plus"></i> Tambah Dosen
    </button>
  </div>

  <div class="card px-5 py-4">
    <h5 class="card-header px-5">Daftar Dosen</h5>
    <div class="table-responsive text-nowrap px-5">
      <table id="lecturerTable" class="table table-striped text-center align-middle" style="width:100%">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Kode</th>
            <th class="text-center">Deskripsi</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0"></tbody>
      </table>
    </div>
  </div>
</div>

@include('lecturers.modal')
@endsection

@section('page-script2')
<script>
$(document).ready(function () {
    let table = $('#lecturerTable').DataTable({
        ajax: '/api/lecturer',
        columns: [
            { data: null, render: (d,t,r,m)=>m.row+1 },
            { data: 'lecturer_name' },
            { data: 'lecturer_code' },
            { data: 'description' },
            {
                data: 'id',
                render: data => `
                    <div class="dropdown">
                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item editBtn" href="javascript:void(0);" data-id="${data}">
                          <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                        <a class="dropdown-item deleteBtn" href="javascript:void(0);" data-id="${data}">
                          <i class="bx bx-trash me-1"></i> Hapus
                        </a>
                      </div>
                    </div>`
            }
        ]
    });

    // === ADD ===
    $('#btnAddLecturer').on('click', function() {
        $('#lecturerForm')[0].reset();
        $('#lecturerIdHidden').val('');
        $('#lecturerModalLabel').text('Tambah Dosen');
        $('#saveLecturerBtn').text('Simpan');
        $('#lecturerModal').modal('show');
    });

    // === SAVE / UPDATE ===
    $('#lecturerForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#lecturerIdHidden').val();
        let method = id ? 'PUT' : 'POST';
        let url = id ? `/api/lecturer/${id}` : '/api/lecturer';

        $.ajax({
            url: url,
            type: method,
            data: {
                lecturer_name: $('#lecturerName').val(),
                lecturer_code: $('#lecturerCode').val(),
                lecturer_description: $('#lecturerDescription').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                $('#lecturerModal').modal('hide');
                Swal.fire('Berhasil!', 'Data dosen berhasil disimpan.', 'success');
                table.ajax.reload();
            },
            error: function() {
                Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan data.', 'error');
            }
        });
    });

    // === EDIT ===
    $(document).on('click', '.editBtn', function() {
        let row = table.row($(this).parents('tr')).data();
        $('#lecturerIdHidden').val(row.id);
        $('#lecturerId').val(row.lecturer_id);
        $('#lecturerName').val(row.lecturer_name);
        $('#lecturerCode').val(row.lecturer_code);
        $('#lecturerDescription').val(row.description);

        $('#lecturerModalLabel').text('Edit Dosen');
        $('#saveLecturerBtn').text('Update');
        $('#lecturerModal').modal('show');
    });

    // === DELETE ===
    $(document).on('click', '.deleteBtn', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            text: 'Data ini tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/lecturer/${id}`,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success');
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Gagal menghapus data.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
