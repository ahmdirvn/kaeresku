@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Kaeresku')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('vendor-script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  console.log("jQuery version (Sneat):", typeof jQuery !== "undefined" ? jQuery.fn.jquery : "NOT LOADED");
</script>
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
<div class="row g-4">
  <!-- Greeting Card -->
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center justify-content-between flex-wrap p-4">
        <div class="d-flex align-items-center mb-3 mb-md-0">
          <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" alt="User" height="80" class="me-3" />
          <div>
            <h5 class="mb-1 fw-semibold">Selamat datang, Ipan 👋</h5>
            <p class="mb-0 text-muted">Berikut ringkasan aktivitas dan statistikmu</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Summary Cards (Realtime Firebase) -->
  <div class="col-6 col-md-3">
    <div class="card text-center shadow-sm h-100">
      <div class="card-body">
        <div class="mb-2"><i class="bx bx-book bx-lg text-primary"></i></div>
        <h3 id="totalCourses" class="fw-bold mb-1">-</h3>
        <p class="text-muted mb-0">Total Courses</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center shadow-sm h-100">
      <div class="card-body">
        <div class="mb-2"><i class="bx bx-user bx-lg text-success"></i></div>
        <h3 id="totalLecturers" class="fw-bold mb-1">-</h3>
        <p class="text-muted mb-0">Total Lecturers</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center shadow-sm h-100">
      <div class="card-body">
        <div class="mb-2"><i class="bx bx-calendar bx-lg text-warning"></i></div>
        <h3 id="totalSchedules" class="fw-bold mb-1">-</h3>
        <p class="text-muted mb-0">Total Schedules</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center shadow-sm h-100">
      <div class="card-body">
        <div class="mb-2"><i class="bx bx-task bx-lg text-danger"></i></div>
        <h3 id="totalKrs" class="fw-bold mb-1">-</h3>
        <p class="text-muted mb-0">Total KRS</p>
      </div>
    </div>
  </div>
</div>


@endsection
@section('page-script2')
<!-- jQuery AJAX for Firebase Dashboard Data -->
<script>
$(document).ready(function () {
  console.log('test');
  // DataTable init
  $('#tasksTable').DataTable({
    paging: false,
    searching: false,
    info: false,
    ordering: false,
  });

  // Ambil data total dari Firebase via controller
  $.ajax({
    url: "{{ route('dashboard.stats') }}",
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response.status === 'success') {
        $('#totalCourses').text(response.data.total_courses);
        $('#totalLecturers').text(response.data.total_lecturers);
        $('#totalSchedules').text(response.data.total_schedules);
        $('#totalKrs').text(response.data.total_krs);
      } else {
        Swal.fire('Gagal', 'Tidak dapat memuat data dashboard', 'error');
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
    }
  });
});
</script>
@endsection
