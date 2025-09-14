<!DOCTYPE html>
<html>
<head>
    <title>Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- ✅ DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- ✅ SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="container mt-5">

    <h2 class="mb-4">Daftar Mata Kuliah</h2>

    {{-- Form Tambah Mata Kuliah --}}
    <form id="addCourseForm" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Nama Mata Kuliah" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="code" class="form-control" placeholder="Kode" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="sks" class="form-control" placeholder="SKS" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tambah</button>
            </div>
        </div>
    </form>

    {{-- Tabel Daftar Mata Kuliah --}}
    <table id="courseTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kode</th>
                <th>SKS</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    {{-- ✅ jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- ✅ DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            // ✅ Init DataTable
            let table = $('#courseTable').DataTable({
                ajax: '/api/courses',
                processing: true,
                serverSide: false,
                columns: [
                    { data: 'name' },
                    { data: 'code' },
                    { data: 'sks' },
                    {
                        data: 'id',
                        render: function (data, type, row) {
                            return `
                                <button class="btn btn-warning btn-sm editBtn" data-id="${data}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteBtn" data-id="${data}">Hapus</button>
                            `;
                        }
                    }
                ]
            });

            // ✅ Tambah Course
            $('#addCourseForm').on('submit', function (e) {
                e.preventDefault();

                let formData = {
                    name: $('input[name="name"]').val(),
                    code: $('input[name="code"]').val(),
                    sks: $('input[name="sks"]').val(),
                };

                $.ajax({
                    url: '/api/courses',
                    type: 'POST',
                    data: formData,
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

            // ✅ Delete Course
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

            // ✅ Edit Course
            $(document).on('click', '.editBtn', function () {
                let id = $(this).data('id');
                let row = table.row($(this).parents('tr')).data();

                Swal.fire({
                    title: 'Edit Mata Kuliah',
                    html: `
                        <input id="editName" class="swal2-input" placeholder="Nama" value="${row.name}">
                        <input id="editCode" class="swal2-input" placeholder="Kode" value="${row.code}">
                        <input id="editSks" class="swal2-input" type="number" placeholder="SKS" value="${row.sks}">
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
</body>
</html>
