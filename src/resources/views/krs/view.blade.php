@extends('layouts/contentNavbarLayout')

@section('content')
<div class="flex w-full h-screen overflow-hidden bg-gray-50">
    <!-- Left: Course List -->
    <div class="w-1/5 bg-white border-r overflow-y-auto p-4">
        <h2 class="text-lg font-semibold mb-3">All Courses</h2>
        <div id="course-list" class="space-y-3">
            @foreach ($result as $id => $course)
                <div class="p-3 bg-blue-50 border rounded cursor-grab course-card"
                    draggable="true"
                    data-id="{{ $id }}"
                    data-name="{{ $course['name'] }}"
                    data-sks="{{ $course['sks'] }}">
                    <p class="font-semibold text-gray-800">{{ $course['name'] }}</p>
                    <p class="text-sm text-gray-600">{{ $course['code'] ?? '' }}</p>
                    <p class="text-xs text-gray-500">SKS: {{ $course['sks'] ?? 0 }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right: Semester Area -->
    <div class="w-4/5 p-6 overflow-x-auto" id="semester-board">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Semester Planning</h2>
            <button id="add-semester" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + Add Semester
            </button>
        </div>

        <div id="semester-container" class="flex gap-6">
            <!-- Semester columns will appear here -->
        </div>
    </div>
</div>

<script>
    let semesterCounter = 0;

    document.getElementById('add-semester').addEventListener('click', function() {
        semesterCounter++;
        const semesterId = `semester-${semesterCounter}`;
        const semesterColumn = `
            <div class="bg-white w-80 border rounded-lg shadow p-3 flex-shrink-0"
                 data-id="${semesterId}" data-max-sks="24" ondragover="allowDrop(event)" ondrop="dropCourse(event, '${semesterId}')">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold">Semester ${semesterCounter}</h3>
                    <span class="text-xs text-gray-500" id="sks-${semesterId}">Max SKS: 24</span>
                </div>
                <div class="min-h-[200px] bg-gray-50 p-2 rounded space-y-2" id="semester-body-${semesterId}">
                    <p class="text-gray-400 text-sm text-center">Drop courses here</p>
                </div>
            </div>
        `;
        document.getElementById('semester-container').insertAdjacentHTML('beforeend', semesterColumn);
    });

    // drag/drop
    let draggedCourse = null;

    document.querySelectorAll('.course-card').forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedCourse = this;
            e.dataTransfer.effectAllowed = 'move';
        });
    });

    function allowDrop(ev) {
        ev.preventDefault();
    }

    function dropCourse(ev, semesterId) {
        ev.preventDefault();

        if (!draggedCourse) return;

        const courseSks = parseInt(draggedCourse.dataset.sks);
        const sksLabel = document.getElementById(`sks-${semesterId}`);
        const semesterBody = document.getElementById(`semester-body-${semesterId}`);

        let currentSks = Array.from(semesterBody.querySelectorAll('.course-card'))
            .reduce((sum, el) => sum + parseInt(el.dataset.sks), 0);

        const maxSks = 24;

        if (currentSks + courseSks > maxSks) {
            alert('Max SKS exceeded!');
            return;
        }

        semesterBody.appendChild(draggedCourse.cloneNode(true));
        currentSks += courseSks;
        sksLabel.innerText = `Used SKS: ${currentSks}/${maxSks}`;
    }
</script>
@endsection
