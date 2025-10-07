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
            <p class="mb-0 text-muted">Berikut ringkasan aktivitas dan progres tugasmu.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="col-6 col-md-3">
    <div class="card text-center shadow-sm h-100">
      <div class="card-body">
        <div class="mb-2"><i class="bx bx-task bx-lg text-primary"></i></div>
        <h3 class="fw-bold mb-1">12</h3>
        <p class="text-muted mb-0">Tugas Aktif</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center shadow-sm h-100">
      <div class="card-body">
        <div class="mb-2"><i class="bx bx-check-shield bx-lg text-success"></i></div>
        <h3 class="fw-bold mb-1">8</h3>
        <p class="text-muted mb-0">Tugas Selesai</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center shadow-sm h-100">
      <div class="card-body">
        <div class="mb-2"><i class="bx bx-error-alt bx-lg text-warning"></i></div>
        <h3 class="fw-bold mb-1">4</h3>
        <p class="text-muted mb-0">Tugas Tertunda</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center shadow-sm h-100">
      <div class="card-body">
        <div class="mb-2"><i class="bx bx-bell bx-lg text-danger"></i></div>
        <h3 class="fw-bold mb-1">3</h3>
        <p class="text-muted mb-0">Notifikasi Baru</p>
      </div>
    </div>
  </div>
</div>

<!-- Row: Task Table + Progress -->
<div class="row mt-4 g-4">
  <!-- Recent Tasks Table -->
  <div class="col-12 col-lg-7">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom-0">
        <h5 class="mb-0 fw-semibold">📋 Tugas Terbaru</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="tasksTable" class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Nama Tugas</th>
                <th>Status</th>
                <th>Deadline</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Buat API Login</td>
                <td><span class="badge bg-warning text-dark">Pending</span></td>
                <td>2025-10-08</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Design Dashboard</td>
                <td><span class="badge bg-success">Selesai</span></td>
                <td>2025-10-05</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Integrasi Firebase</td>
                <td><span class="badge bg-warning text-dark">Pending</span></td>
                <td>2025-10-09</td>
              </tr>
              <tr>
                <td>4</td>
                <td>Upload Dokumen</td>
                <td><span class="badge bg-danger">Tertunda</span></td>
                <td>2025-10-07</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="fw-semibold mb-4">📈 Progress Mingguan</h5>

        @php
        $tasks = [
          ['label' => 'Bugs Fix', 'percent' => 70, 'color' => 'bg-success'],
          ['label' => 'Frontend', 'percent' => 50, 'color' => 'bg-primary'],
          ['label' => 'Backend', 'percent' => 30, 'color' => 'bg-warning'],
          ['label' => 'Dokumentasi', 'percent' => 90, 'color' => 'bg-info'],
        ];
        @endphp

        @foreach($tasks as $task)
          <div class="mb-3">
            <div class="d-flex justify-content-between">
              <span>{{ $task['label'] }}</span>
              <span class="fw-semibold">{{ $task['percent'] }}%</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar {{ $task['color'] }}" role="progressbar" style="width: {{ $task['percent'] }}%"></div>
            </div>
          </div>
        @endforeach

      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
  $('#tasksTable').DataTable({
    paging: false,
    searching: false,
    info: false,
    ordering: false,
  });
});
</script>
@endsection
