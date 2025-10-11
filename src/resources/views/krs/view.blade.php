@extends('layouts/contentNavbarLayout')

@section('title', 'KRS Management')

@section('content')
<div class="d-flex w-100 h-100 overflow-hidden bg-body">
  <!-- Left: Course List -->
  <div class="border-end p-3" style="width: 22%; overflow-y: auto;">
    <h6 class="fw-bold text-primary mb-3">All Courses</h6>
    <div id="course-list" class="d-flex flex-column gap-2">
      @foreach ($result as $course)
      <div class="course-card border rounded-2 px-2 py-2 bg-light cursor-pointer position-relative"
           draggable="true"
           data-id="{{ $course['id'] }}"
           data-name="{{ $course['name'] }}"
           data-sks="{{ $course['sks'] }}"
           style="transition: all 0.2s ease; font-size: 13px;">
        <div class="fw-semibold text-dark">{{ $course['name'] }}</div>
        <div class="text-muted small">SKS: {{ $course['sks'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

  <!-- Right: Semester Area -->
  <div class="flex-grow-1 p-4 overflow-x-auto" id="semester-board">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold mb-0">Semester Planning</h5>
      <div>
        <button id="save-krs" class="btn btn-primary btn-sm me-2">
          <i class="bx bx-save"></i> Save
        </button>
        <button id="add-semester" class="btn btn-success btn-sm">
          <i class="bx bx-plus"></i> Semester
        </button>
      </div>
    </div>

    <div id="semester-container" class="d-flex flex-nowrap gap-3 pb-3"></div>
  </div>
</div>
@endsection

@section('page-script2')
<script>
  let semesterCounter = 0;
  let draggedCourse = null;

  // === DRAG START ===
  $('.course-card').on('dragstart', function (e) {
    draggedCourse = this;
    e.originalEvent.dataTransfer.effectAllowed = 'move';
  });

  // === ADD SEMESTER ===
  $('#add-semester').on('click', function () {
    semesterCounter++;
    const semesterId = `semester-${semesterCounter}`;
    const semesterColumn = `
      <div class="border rounded-3 shadow-sm bg-white p-2" style="width: 220px; flex-shrink: 0;"
          data-id="${semesterId}" data-max-sks="24"
          ondragover="allowDrop(event)"
          ondrop="dropCourse(event, '${semesterId}')">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <small class="fw-semibold">Semester ${semesterCounter}</small>
          <small class="text-muted" id="sks-${semesterId}">0/24 SKS</small>
        </div>
        <div class="p-2 rounded bg-body-tertiary min-vh-20" id="semester-body-${semesterId}"
             style="display: flex; flex-direction: column; gap: 6px;">
          <p class="text-muted text-center small mb-0">Drop courses here</p>
        </div>
      </div>
    `;
    $('#semester-container').append(semesterColumn);
  });

  function allowDrop(ev) { ev.preventDefault(); }

  // === DROP COURSE ===
  function dropCourse(ev, semesterId) {
    ev.preventDefault();
    if (!draggedCourse) return;

    const courseSks = parseInt(draggedCourse.dataset.sks);
    const semesterBody = document.getElementById(`semester-body-${semesterId}`);
    const sksLabel = document.getElementById(`sks-${semesterId}`);

    let currentSks = Array.from(semesterBody.querySelectorAll('.course-card'))
      .reduce((sum, el) => sum + parseInt(el.dataset.sks), 0);

    if (currentSks + courseSks > 24) {
      Swal.fire('Warning', 'Melebihi batas 24 SKS!', 'warning');
      return;
    }

    const clone = draggedCourse.cloneNode(true);
    if (!clone.querySelector('.delete-btn')) {
      const delBtn = document.createElement('button');
      delBtn.className = 'btn btn-link text-danger p-0 position-absolute delete-btn';
      delBtn.style.bottom = '3px';
      delBtn.style.right = '3px';
      delBtn.innerHTML = '<i class="bx bx-trash fs-5"></i>';
      clone.appendChild(delBtn);
    }

    clone.classList.add('position-relative');
    semesterBody.querySelector('p')?.remove();
    semesterBody.appendChild(clone);
    draggedCourse.remove();
    draggedCourse = null;
    updateSksLabel(semesterBody, sksLabel);

    // delete handler
    $(clone).find('.delete-btn').on('click', function () {
      const newCard = clone.cloneNode(true);
      $(newCard).find('.delete-btn').remove();
      $('#course-list').append(newCard);

      $(newCard).on('dragstart', function (e) {
        draggedCourse = this;
        e.originalEvent.dataTransfer.effectAllowed = 'move';
      });

      clone.remove();
      updateSksLabel(semesterBody, sksLabel);

      if (semesterBody.children.length === 0) {
        semesterBody.innerHTML = `<p class="text-muted text-center small mb-0">Drop courses here</p>`;
        semesterBody.style.display = 'flex';
        semesterBody.style.flexDirection = 'column';
        semesterBody.style.gap = '6px';
      }
    });
  }

  function updateSksLabel(semesterBody, sksLabel) {
    let total = Array.from(semesterBody.querySelectorAll('.course-card'))
      .reduce((sum, el) => sum + parseInt(el.dataset.sks), 0);
    sksLabel.textContent = `${total}/24 SKS`;
  }

  // === SAVE TO BACKEND ===
  $('#save-krs').on('click', function () {
    const semesters = {};
    $('#semester-container > div').each(function () {
      const id = $(this).data('id');
      const name = $(this).find('small.fw-semibold').text();
      const body = $(this).find('.course-card');
      const courses = [];

      body.each(function () {
        courses.push({
          id: $(this).data('id'),
          name: $(this).data('name'),
          sks: parseInt($(this).data('sks'))
        });
      });

      semesters[id] = { name, courses };
    });

    Swal.fire({
      title: 'Saving KRS...',
      text: 'Please wait',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    $.ajax({
      url: '/api/krs',
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      contentType: 'application/json',
      data: JSON.stringify(semesters),
      success: () => {
        Swal.close();
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'KRS berhasil disimpan.',
          confirmButtonClass: 'btn btn-primary'
        });
      },
      error: () => {
        Swal.close();
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Gagal menyimpan KRS.',
          confirmButtonClass: 'btn btn-danger'
        });
      }
    });
  });
</script>
@endsection
