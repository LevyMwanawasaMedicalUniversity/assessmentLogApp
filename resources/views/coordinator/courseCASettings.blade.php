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
                                    <div>
                                        <h5 class="card-title">
                                            Marks Available: <span id="remainingMarks" style="font-weight: bold;">{{$total_marks - $marksToDeduct}}</span>
                                            <span id="marksStatus" class="badge bg-danger ms-2" style="display: {{ $total_marks - $marksToDeduct == 0 ? 'inline' : 'none' }}">
                                                All marks allocated
                                            </span>
                                        </h5>
                                        <div id="marksGuidance" class="text-muted small mt-1" style="display: {{ $total_marks - $marksToDeduct == 0 ? 'block' : 'none' }}">
                                            {{-- <i class="bi bi-info-circle"></i> To add more assessment types, reduce marks from existing ones. --}}
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="allMarksGuidanceAlert" class="alert alert-warning d-flex align-items-center mb-3" style="display: {{ $total_marks - $marksToDeduct == 0 ? 'flex' : 'none' }}">
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                    <div>
                                        <strong>All marks allocated:</strong> To add more assessment types, first reduce marks from existing ones.
                                        <div class="small mt-1">Click on any marks input field with a <span class="text-info">highlighted border</span> to adjust.</div>
                                    </div>
                                </div>
                                
                                <table id="myTable" class="table table-hover" style="opacity: 0; transition: opacity 0.3s">
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
                                                    <div class="input-group">
                                                        <input 
                                                            value="{{ array_key_exists($assesmentType->id, $courseAssessmenetTypes) 
                                                                ? max(1, $courseAssessmenetTypes[$assesmentType->id])
                                                                : 0 }}" 
                                                            type="number" 
                                                            class="form-control {{ array_key_exists($assesmentType->id, $courseAssessmenetTypes) && $courseAssessmenetTypes[$assesmentType->id] == 0 ? 'border-warning' : '' }}"
                                                            name="marks_allocated[{{ $assesmentType->id }}]" 
                                                            oninput="updateTotalMarks(this)" 
                                                            max="{{$total_marks}}" 
                                                            min="{{ array_key_exists($assesmentType->id, $courseAssessmenetTypes) ? '1' : '0' }}" 
                                                            {{ array_key_exists($assesmentType->id, $courseAssessmenetTypes) 
                                                                ? 'placeholder="Enter marks"'
                                                                : 'disabled' }}>
                                                        <span class="input-group-text">/{{$total_marks}}</span>
                                                    </div>
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
                                                        <div class="mt-2 fw-bold" style="font-size: 0.9rem; color: {{ $currentUploads == $minAllowed ? '#E67E22' : '#2980B9' }};">
                                                            <i class="bi {{ $currentUploads == $minAllowed ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill' }} me-1"></i>
                                                            Currently {{ $currentUploads }} upload(s). 
                                                            @if($currentUploads == $minAllowed)
                                                                <strong>Cannot reduce below this limit</strong> without first deleting existing uploads.
                                                            @else
                                                                Can upload {{ isset($assessmentCounts[$assesmentType->id]) ? $assessmentCounts[$assesmentType->id] - $currentUploads : $minAllowed - $currentUploads }} more.
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="mt-2 fw-bold" style="font-size: 0.9rem; color: #2980B9;">
                                                            <i class="bi bi-info-circle-fill me-1"></i>
                                                            No uploads yet. You can upload up to {{ isset($assessmentCounts[$assesmentType->id]) ? $assessmentCounts[$assesmentType->id] : 1 }} assessment(s).
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="text-center">                                    
                                    <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0" id="saveChangesBtn">Save Changes</button>
                                    <div class="mt-2 text-danger" id="validationMessage" style="display: none;">
                                        Please enter marks greater than 0 for all selected assessment types.
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </section><!-- End Section -->
    </main><!-- End #main -->
    
    <script>
        // Declare variables and functions but don't run initialization yet
        var totalMarks = {{$total_marks - $marksToDeduct}};
        var initialTotalMarks = {{$total_marks}};
        var previousValues = {};
        var tableInitialized = false;

        // Add a function to initialize the table after all validation is ready
        function initializeTable() {
            if (tableInitialized) return; // Prevent multiple initializations
            
            var inputs = document.querySelectorAll('input[name^="marks_allocated"]');
            for (var i = 0; i < inputs.length; i++) {
                previousValues[inputs[i].name] = parseInt(inputs[i].value) || 0;
            }
            
            // Check if all marks are allocated and update UI accordingly
            updateRemainingMarksColor();
            
            // Mark the table as initialized
            tableInitialized = true;
            
            // Show the table when everything is ready
            document.getElementById('myTable').style.opacity = '1';
            
            console.log('Course CA Settings table initialized successfully');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Hide table until it's fully initialized
            document.getElementById('myTable').style.opacity = '0';
            document.getElementById('myTable').style.transition = 'opacity 0.3s';
            
            // Add form validation
            document.querySelector('form').addEventListener('submit', function(event) {
                let isValid = true;
                const checkboxes = document.querySelectorAll('input.assessmentType:checked');
                
                checkboxes.forEach(function(checkbox) {
                    const tr = checkbox.closest('tr');
                    const inputMarks = tr.querySelector('input[name^="marks_allocated"]');
                    const marksValue = parseInt(inputMarks.value);
                    
                    if (isNaN(marksValue) || marksValue <= 0) {
                        isValid = false;
                        inputMarks.classList.add('is-invalid');
                        // Set minimum value to 1 to prevent 0 values
                        if (marksValue <= 0) {
                            inputMarks.value = '';
                        }
                    } else {
                        inputMarks.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    event.preventDefault();
                    document.getElementById('validationMessage').style.display = 'block';
                    window.scrollTo(0, document.body.scrollHeight);
                }
            });
            
            // Initialize the table after all validation is set up
            initializeTable();
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
                
                // Enable inputs but don't automatically assign a value
                // Let the user enter their desired value
                if (parseInt(inputMarks.value) <= 0) {
                    // Add visual indicator that this field needs a value
                    inputMarks.classList.add('border-warning');
                    // Focus on this input to prompt the user to enter a value
                    inputMarks.focus();
                    // Show a tooltip or hint
                    inputMarks.setAttribute('placeholder', 'Enter marks (min: 1)');
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
                
                // Clear any validation styling
                inputMarks.classList.remove('border-warning');
                inputMarks.classList.remove('is-invalid');
                inputMarks.removeAttribute('placeholder');
                
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
            var marksStatusElement = document.getElementById('marksStatus');
            var marksGuidanceElement = document.getElementById('marksGuidance');
            var allMarksGuidanceAlertElement = document.getElementById('allMarksGuidanceAlert');
            
            if (totalMarks === 0) {
                remainingMarksElement.style.color = 'red';
                marksStatusElement.style.display = 'inline'; // Show the "All marks allocated" badge
                marksGuidanceElement.style.display = 'block'; // Show guidance message
                allMarksGuidanceAlertElement.style.display = 'flex'; // Show alert guidance
                // Disable all unchecked checkboxes when no marks are remaining
                disableUncheckedCheckboxes();
            } else {
                remainingMarksElement.style.color = 'green';
                marksStatusElement.style.display = 'none'; // Hide the badge
                marksGuidanceElement.style.display = 'none'; // Hide guidance message
                allMarksGuidanceAlertElement.style.display = 'none'; // Hide alert guidance
                // Enable all checkboxes when marks are available
                enableUncheckedCheckboxes();
            }
        }
        
        function disableUncheckedCheckboxes() {
            var checkboxes = document.querySelectorAll('input.assessmentType:not(:checked)');
            checkboxes.forEach(function(checkbox) {
                checkbox.disabled = true;
                // Add visual indicator that selection is disabled
                var tr = checkbox.closest('tr');
                tr.classList.add('text-muted');
                
                // Add a tooltip with guidance on how to free up marks
                tr.title = 'No marks remaining. Reduce marks from existing assessment types to enable this option.';
                
                // Add a visual indicator to the checkbox
                var tdElement = checkbox.closest('td');
                if (!tdElement.querySelector('.no-marks-indicator')) {
                    var indicator = document.createElement('span');
                    indicator.className = 'no-marks-indicator text-danger ms-2';
                    indicator.innerHTML = '<i class="bi bi-lock-fill" title="Reduce marks from other assessments to enable"></i>';
                    tdElement.appendChild(indicator);
                }
            });
            
            // Highlight rows with allocated marks to indicate they can be reduced
            highlightAllocatedRows();
        }
        
        function highlightAllocatedRows() {
            // Add highlight to rows that have marks allocated (to show they can be reduced)
            var checkedCheckboxes = document.querySelectorAll('input.assessmentType:checked');
            checkedCheckboxes.forEach(function(checkbox) {
                var tr = checkbox.closest('tr');
                var inputMarks = tr.querySelector('input[name^="marks_allocated"]');
                
                // Add a subtle highlight and tooltip to show these can be reduced
                if (parseInt(inputMarks.value) > 1) {
                    inputMarks.classList.add('border-info');
                    inputMarks.title = 'You can reduce this value to free up marks for other assessment types';
                }
            });
        }
        
        function enableUncheckedCheckboxes() {
            var checkboxes = document.querySelectorAll('input.assessmentType:not(:checked)');
            checkboxes.forEach(function(checkbox) {
                checkbox.disabled = false;
                // Remove visual indicator
                var tr = checkbox.closest('tr');
                tr.classList.remove('text-muted');
                tr.removeAttribute('title');
                
                // Remove lock icon if it exists
                var lockIcon = checkbox.closest('td').querySelector('.no-marks-indicator');
                if (lockIcon) {
                    lockIcon.remove();
                }
            });
            
            // Remove highlights from allocated rows
            var allocatedInputs = document.querySelectorAll('input[name^="marks_allocated"]');
            allocatedInputs.forEach(function(input) {
                input.classList.remove('border-info');
                input.title = '';
            });
        }
    </script>
</x-app-layout>
