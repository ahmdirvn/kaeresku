@extends('layouts/contentNavbarLayout')

@section('title', 'Daftar Mata Kuliah')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Daftar Mata Kuliah</h2>
    <button class="btn btn-primary" id="btnAddCourse">
      <i class="bx bx-plus"></i> Tambah Mata Kuliah
    </button>
  </div>
  {{-- Tabel Daftar Mata Kuliah --}}
  <div class="card px-5 py-4">
    <h5 class="card-header px-5">Daftar Mata Kuliah</h5>
    <div class="table-responsive text-nowrap px-5">
      <table id="courseTable" class="table table-striped text-center align-middle">
        <thead>
          <tr>
            <th class="text-center" style="width: 5%;">No</th>
            <th class="text-center" style="width: 20%;">Nama</th>
            <th class="text-center" style="width: 10%;">Kode</th>
            <th class="text-center" style="width: 8%;">SKS</th>
            <th class="text-center" style="width: 12%;">Kategori</th>
            <th class="text-center" style="width: 25%;">Deskripsi</th>
            <th class="text-center" style="width: 10%;">Aksi</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0"></tbody>
      </table>

    </div>
  </div>
</div>
{{-- Include Modal --}}
@include('courses.modal')
@endsection


{{--  Page Script --}}
@section('page-script2')
<script>
$(document).ready(function () {
    let table = $('#courseTable').DataTable({
        ajax: '/api/courses',
        columns: [
            { data: null, render: (d,t,r,m)=>m.row+1 },
            { data: 'name' },
            { data: 'code' },
            { data: 'sks' },
            { data: 'category' },
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
                      <i class="bx bx-trash me-1"></i> Delete
                    </a>
                  </div>
                </div>`
            }
        ]
    });

    // Open Modal for Add
    $('#btnAddCourse').on('click', function() {
        $('#courseForm')[0].reset();
        $('#courseId').val('');
        $('#courseModalLabel').text('Tambah Mata Kuliah');
        $('#saveCourseBtn').text('Simpan');
        $('#courseModal').modal('show');
    });

    // Save (Add or Edit)
    $('#courseForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#courseId').val();
        let method = id ? 'PUT' : 'POST';
        let url = id ? `/api/courses/${id}` : '/api/courses';

        $.ajax({
            url: url,
            type: method,
            data: {
                name: $('#courseName').val(),
                code: $('#courseCode').val(),
                sks: $('#courseSks').val(),
                category: $('#courseCategory').val(),
                description: $('#courseDescription').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                $('#courseModal').modal('hide');
                Swal.fire('Berhasil!', 'Data berhasil disimpan.', 'success');
                table.ajax.reload();
            },
            error: function() {
                Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan data.', 'error');
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function() {
        let row = table.row($(this).parents('tr')).data();
        $('#courseId').val(row.id);
        $('#courseName').val(row.name);
        $('#courseCode').val(row.code);
        $('#courseSks').val(row.sks);
        $('#courseCategory').val(row.category);
        $('#courseDescription').val(row.description);

        $('#courseModalLabel').text('Edit Mata Kuliah');
        $('#saveCourseBtn').text('Update');
        $('#courseModal').modal('show');
    });

    // Delete
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
                    url: `/api/courses/${id}`,
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