<x-app-layout>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>{{$course->Name}} Course Settings 
                <span style="color: {{ $delivery == 'Distance' ? 'green' : ($delivery == 'Fulltime' ? 'blue' : 'black') }}">
                    {{ $delivery }} @if($hasComponents){{$hasComponents}}@endif
                    
                </span>
            </h1>
            @include('layouts.alerts')
            
            <div class="alert alert-info" role="alert">
                <strong>Note:</strong> You cannot reduce the number of assessments below the count of existing uploads. You must delete existing uploads first.
            </div>
            
            <nav>
                {{ Breadcrumbs::render() }}
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <form method="POST" action="{{ route('coordinator.updateCourseCASetings', encrypt($course->ID)) }}" class="row g-3">
                            {{-- @method('patch') --}}
                            @csrf
                            <div class="card-body">
                                @php
                                    $total_marks = 40;
                                @endphp
                                
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title">{{$course->CourseDescription}} @if($hasComponents){{$hasComponents}}@endif</h5>
                                    <h5 class="card-title">Marks Available : <span id="remainingMarks" style="font-weight: bold;">{{$total_marks - $marksToDeduct}}</span></h5>
                                </div>
                                
                                <table id="myTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Select</th>
                                        <th scope="col">Assessment Type</th>
                                        <th scope="col">Marks Allocated</th>
                                        <th scope="col">Number of Assessments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allAssesmentTypes as $assesmentType)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>
                                                
                                                    <input type="hidden" name="courseId" value="{{$course->ID}}">
                                                    <input type="hidden" name="basicInformationId" value="{{$basicInformationId}}">
                                                    <input type="hidden" name="delivery" value="{{$delivery}}">
                                                    <input type="hidden" name="studyId" value="{{$studyId}}">
                                                    <input type="hidden" name="componentId" value="{{$componentId}}">
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
                                                <td>
                                                    @php
                                                        $currentUploads = $existingUploads[$assesmentType->id] ?? 0;
                                                        $minAllowed = max(1, $currentUploads);
                                                    @endphp
                                                    
                                                    <select 
                                                        name="assessment_counts[{{ $assesmentType->id }}]" 
                                                        class="form-select" 
                                                        {{ array_key_exists($assesmentType->id, $courseAssessmenetTypes) 
                                                            ? ''
                                                            : 'disabled' }}>
                                                        @for($i = $minAllowed; $i <= 10; $i++)
                                                            <option value="{{ $i }}" {{ isset($assessmentCounts[$assesmentType->id]) && $assessmentCounts[$assesmentType->id] == $i ? 'selected' : '' }}>
                                                                {{ $i }} {{ $i > 1 ? Str::plural($assesmentType->assesment_type_name) : $assesmentType->assesment_type_name }}
                                                                @if($i == $minAllowed && $currentUploads > 0)
                                                                    ({{ $currentUploads }} uploads exist)
                                                                @endif
                                                            </option>
                                                        @endfor
                                                    </select>
                                                    
                                                    @if($currentUploads > 0)
                                                        <div class="mt-1 small text-muted">
                                                            Currently {{ $currentUploads }} upload(s). Cannot reduce below this limit.
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="text-center">                                    
                                    <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">Save Changes</button>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </section><!-- End Section -->
    </main><!-- End #main -->
    
    <script>
        // Existing code
        var totalMarks = {{$total_marks - $marksToDeduct}};
        var initialTotalMarks = {{$total_marks}};
        var previousValues = {};

        document.addEventListener('DOMContentLoaded', function() {
            var inputs = document.querySelectorAll('input[name^="marks_allocated"]');
            for (var i = 0; i < inputs.length; i++) {
                previousValues[inputs[i].name] = parseInt(inputs[i].value) || 0;
            }
            updateRemainingMarksColor();
        });

        function toggleInput(checkbox, value) {
            // Find the closest tr (table row)
            var tr = checkbox.closest('tr');
            
            // Find the input element for marks allocation
            var inputMarks = tr.querySelector('input[name^="marks_allocated"]');
            
            // Find the select element for assessment count
            var selectCount = tr.querySelector('select[name^="assessment_counts"]');
            
            if (checkbox.checked) {
                inputMarks.disabled = false;
                selectCount.disabled = false;
                
                // Only update marks if they are 0 (newly checked)
                if (parseInt(inputMarks.value) === 0) {
                    inputMarks.value = 5; // Default value
                    updateTotalMarks(inputMarks);
                }
            } else {
                // Update total marks before disabling
                if (parseInt(inputMarks.value) > 0) {
                    var oldValue = parseInt(inputMarks.value);
                    inputMarks.value = 0;
                    previousValues[inputMarks.name] = 0;
                    totalMarks += oldValue;
                    document.getElementById('remainingMarks').textContent = totalMarks;
                    updateRemainingMarksColor();
                    updateMaxValues();
                }
                
                inputMarks.disabled = true;
                selectCount.disabled = true;
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
    </script>
</x-app-layout>
