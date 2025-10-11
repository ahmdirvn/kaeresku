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
            <th class="text-center">No. HP</th>
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
            { data: 'lecturer_phone' },
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

    // === ADD (buka modal) ===
    $('#btnAddLecturer').on('click', function() {
        // loader singkat saat open modal (UX)
        Swal.fire({
          title: 'Menyiapkan form...',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });

        // karena tidak ada fetch, tutup loader segera dan tampilkan modal
        setTimeout(() => {
          Swal.close();
          $('#lecturerForm')[0].reset();
          $('#lecturerIdHidden').val('');
          $('#lecturerModalLabel').text('Tambah Dosen');
          $('#saveLecturerBtn').text('Simpan');
          $('#lecturerModal').modal('show');
        }, 150); // 150ms agar loader kelihatan sebentar (opsional)
    });

    // === SAVE / UPDATE ===
    $('#lecturerForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#lecturerIdHidden').val();
        let method = id ? 'PUT' : 'POST';
        let url = id ? `/api/lecturer/${id}` : '/api/lecturer';

        // tampilkan loader saat submit
        Swal.fire({
          title: id ? 'Memperbarui data...' : 'Menyimpan data...',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: url,
            type: method,
            data: {
                lecturer_name: $('#lecturerName').val(),
                lecturer_code: $('#lecturerCode').val(),
                lecturer_phone: $('#lecturerPhone').val(),
                lecturer_description: $('#lecturerDescription').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                Swal.close();
                $('#lecturerModal').modal('hide');
                Swal.fire('Berhasil!', 'Data dosen berhasil disimpan.', 'success');
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.close();
                if (xhr.status === 409) {
                    Swal.fire('Gagal!', 'Kode dosen sudah digunakan.', 'warning');
                } else {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan data.', 'error');
                }
            }
        });
    });

    // === EDIT (buka modal & isi) ===
    $(document).on('click', '.editBtn', function() {
        let row = table.row($(this).parents('tr')).data();

        // tampilkan loader saat menyiapkan data edit
        Swal.fire({
          title: 'Memuat data dosen...',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });

        // karena data sudah ada di table row, kita tutup loader lalu tampilkan modal
        setTimeout(() => {
          Swal.close();

          $('#lecturerIdHidden').val(row.id);
          $('#lecturerName').val(row.lecturer_name);
          $('#lecturerCode').val(row.lecturer_code);
          $('#lecturerPhone').val(row.lecturer_phone);
          $('#lecturerDescription').val(row.description);

          $('#lecturerModalLabel').text('Edit Dosen');
          $('#saveLecturerBtn').text('Update');
          $('#lecturerModal').modal('show');
        }, 150);
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
                // tampilkan loader saat proses delete
                Swal.fire({
                  title: 'Menghapus data...',
                  allowOutsideClick: false,
                  didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: `/api/lecturer/${id}`,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        Swal.close();
                        Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success');
                        table.ajax.reload();
                    },
                    error: function() {
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
