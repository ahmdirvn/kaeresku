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
            <th class="text-center" style="width: 12%;">Dosen</th>
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
    let lecturerList = [];
    let table = $('#courseTable').DataTable({
        ajax: {
        url: '/api/courses',
        dataSrc: function (json) {
            console.log('🔥 Response dari API /api/courses:', json);
            return json.data; // pastikan tetap mengembalikan data ke DataTables
        }
        },
        columns: [
            { data: null, render: (d,t,r,m)=>m.row+1 },
            { data: 'name' },
            { data: 'code' },
            { data: 'sks' },
            { data: 'lecturer_name' },
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
// Open Modal for Add
$('#btnAddCourse').on('click', function() {
  // Populate dropdown dosen dari API 
  $.ajax({
    url: '/api/lecturer',
    type: 'GET',
    success: function (response) {
      console.log('📘 Lecturer list:', response);
      let data = Array.isArray(response) ? response : response.data;

      if (!data || data.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Dosen Tidak Ditemukan',
          text: 'Tidak ada data dosen yang tersedia. Silakan periksa kembali data dosen.',
        });
        return; // hentikan eksekusi berikutnya
      }

      let lecturerMap = {};
      data.forEach(item => {
        if (item.lecturer_id && !lecturerMap[item.lecturer_id]) {
          lecturerMap[item.lecturer_id] = item.lecturer_name || 'Belum Diisi';
        }
      });

      let uniqueLecturers = Object.entries(lecturerMap).map(([id, name]) => ({
        id,
        name
      }));

      // Kosongkan dropdown dulu
      $('#lecturerSupervise').empty().append('<option value="">Pilih Dosen Pengampu</option>');

      // Isi dropdown
      uniqueLecturers.forEach(lecturer => {
        $('#lecturerSupervise').append(
          `<option id="lecturer_id" value="${lecturer.id}">${lecturer.name}</option>`
        );
      });

      console.log('✅ Unique lecturers:', uniqueLecturers);

      $('#courseForm')[0].reset();
      $('#courseId').val('');
      $('#courseModalLabel').text('Tambah Mata Kuliah');
      $('#saveCourseBtn').text('Simpan');
      $('#courseModal').modal('show');
    },
    error: function () {
      console.error('Gagal memuat daftar dosen');
      Swal.fire({
        icon: 'error',
        title: 'Gagal Memuat Dosen',
        text: 'Terjadi kesalahan saat mengambil data dosen. Silakan coba lagi nanti.',
      });
    }
  });
});
    // Save (Add or Edit)
    $('#courseForm').on('submit', function(e) {
        e.preventDefault();
         console.log($('#lecturerSupervise').val());
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
                lecturerId : $('#lecturerSupervise').val(),
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
  console.log(row);

  // Load daftar dosen dulu
  $.ajax({
    url: '/api/lecturer',
    type: 'GET',
    success: function (response) {
      let data = Array.isArray(response) ? response : response.data;

      if (!data || data.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Dosen Tidak Ditemukan',
          text: 'Tidak ada data dosen yang tersedia. Silakan periksa kembali data dosen.',
        });
        return;
      }

      // Mapping dosen
      let lecturerMap = {};
      data.forEach(item => {
        if (item.lecturer_id && !lecturerMap[item.lecturer_id]) {
          lecturerMap[item.lecturer_id] = item.lecturer_name || 'Belum Diisi';
        }
      });

      let uniqueLecturers = Object.entries(lecturerMap).map(([id, name]) => ({ id, name }));

      // Kosongkan dropdown dan isi ulang
      $('#lecturerSupervise').empty().append('<option value="">Pilih Dosen Pengampu</option>');
      uniqueLecturers.forEach(lecturer => {
        $('#lecturerSupervise').append(
          `<option value="${lecturer.id}">${lecturer.name}</option>`
        );
      });

      // Isi field data course
      $('#courseId').val(row.id);
      $('#courseName').val(row.name);
      $('#courseCode').val(row.code);
      $('#courseSks').val(row.sks);
      $('#courseCategory').val(row.category);
      $('#courseDescription').val(row.description);

      // Set lecturer dropdown ke dosen yang sesuai
      $('#lecturerSupervise').val(row.lecturer_id);

      $('#courseModalLabel').text('Edit Mata Kuliah');
      $('#saveCourseBtn').text('Update');
      $('#courseModal').modal('show');
    },
    error: function() {
      Swal.fire({
        icon: 'error',
        title: 'Gagal Memuat Dosen',
        text: 'Terjadi kesalahan saat mengambil data dosen. Silakan coba lagi nanti.',
      });
    }
  });
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

    // Dropdown onchange untuk dosen pengampu
$('#lecturerSupervise').on('change', function() {
  let selectedLecturerId = $(this).val();
  let selectedLecturerName = $('#lecturerSupervise option:selected').text();
  console.log('📘 Lecturer selected:', {
    id: selectedLecturerId,
    name: selectedLecturerName
  });
});

});
</script>
@endsection