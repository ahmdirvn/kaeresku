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
        <table id="courseTable" class="table table-striped text-center">
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
    //  Init DataTable
    let table = $('#courseTable').DataTable({
        ajax: '/api/courses',
        processing: true,
        serverSide: false,
        columns: [
            {
              data: null,
              render: function (data, type, row, meta) {
                  return meta.row + 1;
              },
              orderable: false,
              searchable: false
            },
            { data: 'name' },
            { data: 'code' },
            { data: 'sks' },
            { data: 'category' },
            { data: 'description' },
            {
                data: 'id',
                render: function (data) {
                    return `
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
                      </div>
                    `;
                }
            }
        ]
    });

    //  Tambah Course
    $('#addCourseForm').on('submit', function (e) {
        e.preventDefault();
        let formData = {
            name: $('input[name="name"]').val(),
            code: $('input[name="code"]').val(),
            sks: $('input[name="sks"]').val(),
            category: $('input[name="category"]').val(),
            description: $('textarea[name="description"]').val(),
        };

        $.ajax({
            url: '/api/courses',
            type: 'POST',
            credentials: 'include',
            data: formData,
            xhrFields: {
                withCredentials: true 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                Swal.fire('Berhasil!', 'Mata kuliah berhasil ditambahkan.', 'success');
                $('#addCourseForm')[0].reset();
                table.ajax.reload();
            },
            error: function () {
                Swal.fire('Gagal!', 'Mata kuliah gagal ditambahkan.', 'error');
            }
        });
    });

    //  Delete Course
    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/courses/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        Swal.fire('Dihapus!', 'Mata kuliah berhasil dihapus.', 'success');
                        table.ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Gagal!', 'Mata kuliah gagal dihapus.', 'error');
                    }
                });
            }
        });
    });

    //  Edit Course
    $(document).on('click', '.editBtn', function () {
        let id = $(this).data('id');
        let row = table.row($(this).parents('tr')).data();

        Swal.fire({
            title: 'Edit Mata Kuliah',
            html: `
                <input id="editName" class="swal2-input" placeholder="Nama" value="${row.name}">
                <input id="editCode" class="swal2-input" placeholder="Kode" value="${row.code}">
                <input id="editSks" class="swal2-input" type="number" placeholder="SKS" value="${row.sks}">
                <input id="editCategory" class="swal2-input" placeholder="Kategori (Opsional)" value="${row.category}">
                <textarea id="editDescription" class="swal2-textarea" placeholder="Deskripsi (Opsional)">${row.description}</textarea>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/courses/' + id,
                    type: 'PUT',
                    data: {
                        name: $('#editName').val(),
                        code: $('#editCode').val(),
                        sks: $('#editSks').val(),
                        category: $('#editCategory').val(),
                        description: $('#editDescription').val(),
                        _token: $('meta[name="csrf-token"]').attr('content') 
                    },
                    success: function () {
                        Swal.fire('Berhasil!', 'Mata kuliah berhasil diupdate.', 'success');
                        table.ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Gagal!', 'Update gagal.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
