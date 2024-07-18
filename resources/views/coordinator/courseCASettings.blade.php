<x-app-layout>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>{{$course->Name}} Course Settings</h1>
            @include('layouts.alerts')
            <nav>
                {{ Breadcrumbs::render() }}
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <form method="POST" action="{{ route('coordinator.updateCourseCASetings', $course->ID) }}" class="row g-3">
                            {{-- @method('patch') --}}
                            @csrf
                            <div class="card-body">
                                @php
                                    $total_marks = 40;
                                @endphp
                                
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title">{{$course->CourseDescription}}</h5>
                                    <h5 class="card-title">Marks Available: <span id="remainingMarks" style="font-weight: bold;">{{$total_marks - $marksToDeduct}}</span></h5>
                                </div>
                                
                                <table id="myTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                        <th scope="col">Select</th>
                                        <th scope="col">Assessment Type</th>
                                        <th scope="col">Marks Allocated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allAssesmentTypes as $assesmentType)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="courseId" value="{{$course->ID}}">
                                                    <input type="checkbox" 
                                                        name="assessmentType[{{ $assesmentType->id }}]"
                                                        value="{{ $assesmentType->id }}"
                                                        class='assessmentType'
                                                        {{ array_key_exists($assesmentType->id, $courseAssessmenetTypes) 
                                                            ? 'checked'
                                                            : '' }}
                                                        onclick="toggleInput(this, {{ $courseAssessmenetTypes[$assesmentType->id] ?? 0 }})">
                                                </td>
                                                <td>{{ $assesmentType->assesment_type_name}}</td>
                                                <td>
                                                    <input 
                                                        value="{{ array_key_exists($assesmentType->id, $courseAssessmenetTypes) 
                                                            ? $courseAssessmenetTypes[$assesmentType->id]
                                                            : 0 }}" 
                                                        type="number" 
                                                        name="marks_allocated[{{ $assesmentType->id }}]" 
                                                        oninput="updateTotalMarks(this)" 
                                                        max="{{$total_marks}}" 
                                                        min="0" 
                                                        {{ array_key_exists($assesmentType->id, $courseAssessmenetTypes) 
                                                            ? ''
                                                            : 'disabled' }}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="text-center">                                    
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <script>
        var initialTotalMarks = {{$total_marks}};
        var totalMarks = initialTotalMarks - {{$marksToDeduct}};
        var previousValues = {};

        function toggleInput(checkbox, initialValue) {
            var input = checkbox.parentElement.nextElementSibling.nextElementSibling.firstElementChild;
            if (checkbox.checked) {
                input.disabled = false;
                input.value = 0;
                previousValues[input.name] = 0;
                updateTotalMarks(input, false);
            } else {
                var previousValue = previousValues[input.name] || 0;
                totalMarks += previousValue;
                previousValues[input.name] = 0;
                input.value = 0;
                input.disabled = true;
                document.getElementById('remainingMarks').textContent = totalMarks;
                updateRemainingMarksColor();
                updateMaxValues();
            }
        }

        function updateTotalMarks(input) {
            var allocatedMarks = parseInt(input.value);
            var previousValue = previousValues[input.name] || 0;

            if (!isNaN(allocatedMarks)) {
                totalMarks += previousValue - allocatedMarks;
                previousValues[input.name] = allocatedMarks;
            }

            document.getElementById('remainingMarks').textContent = totalMarks;
            updateRemainingMarksColor();
            updateMaxValues();
        }

        function updateMaxValues() {
            var inputs = document.querySelectorAll('input[name^="marks_allocated"]');
            for (var i = 0; i < inputs.length; i++) {
                var maxVal = totalMarks + (previousValues[inputs[i].name] || 0);
                inputs[i].max = maxVal > initialTotalMarks ? initialTotalMarks : maxVal;
            }
        }

        function updateRemainingMarksColor() {
            var remainingMarksElement = document.getElementById('remainingMarks');
            if (totalMarks === 0) {
                remainingMarksElement.style.color = 'red';
            } else {
                remainingMarksElement.style.color = 'green';
            }
        }

        window.onload = function() {
            var inputs = document.querySelectorAll('input[name^="marks_allocated"]');
            for (var i = 0; i < inputs.length; i++) {
                if (!inputs[i].disabled) {
                    previousValues[inputs[i].name] = parseInt(inputs[i].value);
                }
            }
            updateMaxValues();
            updateRemainingMarksColor();
        };
    </script>
</x-app-layout>
