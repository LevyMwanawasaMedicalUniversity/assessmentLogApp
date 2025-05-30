<div class="container">
    <div class="modal fade" id="uploadCourseModal{{ $result->course_components_id }}{{ $result->delivery_mode }}{{$result->study_id}}" tabindex="-1" role="dialog" aria-labelledby="uploadCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="uploadCourseModalLabel">
                        <b>Select Assessment Type To Upload 
                            <b style="color: {{ $result->delivery_mode == 'Fulltime' ? 'blue' : ($result->delivery_mode == 'Distance' ? 'green' : 'black') }}">
                                {{$result->delivery_mode}} {{$result->component_name}}
                            </b>
                        </b>
                    </h3>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @php
                    $courseAssessmenetTypes = \App\Models\CATypeMarksAllocation::where('course_id', $result->course_id)
                        ->where('delivery_mode', $result->delivery_mode)
                        ->where('study_id', $result->study_id)
                        ->when($result->course_components_id, function ($query, $component_id) {
                            return $query->where('component_id', $component_id);
                        })
                        ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                        ->select('assessment_types.id', 'assessment_types.assesment_type_name', 'c_a_type_marks_allocations.assessment_count')
                        ->get();
                    $getCourse = \App\Models\EduroleCourses::where('ID', $result->course_id)->first();
                    $courseCode = $getCourse->Name;
                    $totalMarks = \App\Models\CATypeMarksAllocation::where('course_id', $result->course_id)
                        ->where('study_id', $result->study_id)
                        ->where('delivery_mode', $result->delivery_mode)
                        ->when($result->course_components_id, function ($query, $component_id) {
                            return $query->where('component_id', $component_id);
                        })
                        ->sum('total_marks');
                @endphp
                <div class="modal-body">
                    <div class="mb-3">
                        <h5>
                            <span>{{$getCourse->CourseDescription}} - {{$getCourse->Name}} 
                                <b style="color: {{ $result->delivery_mode == 'Fulltime' ? 'blue' : ($result->delivery_mode == 'Distance' ? 'green' : 'black') }}">
                                    {{$result->delivery_mode}} 
                                </b>
                            </span>
                        </h5>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-1"></i>
                            Each assessment type has upload limits. The buttons below show current upload status.
                            Disabled buttons indicate that you've reached the maximum uploads allowed.
                        </div>
                    </div>
                    <div class="container">
                        <div class="d-flex flex-column"> <!-- Flex container with vertical spacing -->
                            <!-- First Block -->
                            @if($courseAssessmenetTypes->count() > 0)
                                @if( $totalMarks == 40)
                                    @foreach ($courseAssessmenetTypes as $courseAssessmenetType )
                                        @php
                                            // Get academic year from settings
                                            $academicYear = \App\Models\Setting::where('key', 'current_academic_year')->first()->value ?? date('Y');
                                            
                                            // Count existing uploads for this assessment type
                                            $currentUploads = \App\Models\CourseAssessment::where('course_id', $result->course_id)
                                                ->where('delivery_mode', $result->delivery_mode)
                                                ->where('study_id', $result->study_id)
                                                ->where('component_id', $result->course_components_id)
                                                ->where('ca_type', $courseAssessmenetType->id)
                                                ->where('academic_year', $academicYear)
                                                ->count();
                                                
                                            $maxAllowed = $courseAssessmenetType->assessment_count ?? 1;
                                            $canUpload = $currentUploads < $maxAllowed;
                                        @endphp
                                        <form method="GET" action="{{ route('coordinator.uploadCa', ['statusId' => encrypt($courseAssessmenetType->id), 'courseIdValue' => encrypt($result->course_id),'basicInformationId' => encrypt($basicInformationId)]) }}">
                                            <input type="hidden" name="delivery" value="{{ $result->delivery_mode }}">
                                            <input type="hidden" name="studyId" value="{{$result->study_id}}">
                                            <input type="hidden" name="componentId" value="{{ ($result->course_components_id) }}">
                                            <input type="hidden" name="hasComponents" value="{{ ($result->component_name) }}">
                                            <button type="submit" class="btn btn-light shadow-sm text-center mb-3" style="border: 2px solid {{ $canUpload ? 'green' : 'red' }};" {{ !$canUpload ? 'disabled' : '' }}>
                                                <div class="p-3 text-dark">
                                                    {{ $courseAssessmenetType->assesment_type_name }}
                                                    <div class="small {{ $canUpload ? 'text-success' : 'text-danger' }}">
                                                        {{ $currentUploads }} of {{ $maxAllowed }} uploads used
                                                        @if(!$canUpload)
                                                            <br><strong>Maximum uploads reached</strong>
                                                        @endif
                                                    </div>
                                                </div>
                                            </button>
                                            @if(!$canUpload)
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('coordinator.courseCASettings', [
                                                            'courseIdValue' => encrypt($result->course_id),
                                                            'basicInformationId' => encrypt($basicInformationId),
                                                            'delivery' => encrypt($result->delivery_mode)
                                                        ]) }}?studyId={{ $result->study_id }}&componentId={{ $result->course_components_id }}&hasComponents={{ $result->component_name }}" class="btn btn-sm btn-outline-secondary me-2">
                                                            Change limit
                                                        </a>
                                                        <a href="{{ route('coordinator.viewAllCaInCourse', [
                                                            'statusId' => encrypt($courseAssessmenetType->id),
                                                            'courseIdValue' => encrypt($result->course_id),
                                                            'basicInformationId' => encrypt($basicInformationId),
                                                            'delivery' => encrypt($result->delivery_mode)
                                                        ]) }}?studyId={{ $result->study_id }}&componentId={{ $result->course_components_id }}" class="btn btn-sm btn-outline-primary">
                                                            Update existing
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </form>
                                    @endforeach
                                @else
                                    <form action="{{ route('coordinator.courseCASettings', [
                                        'courseIdValue' => encrypt($result->course_id),
                                        'basicInformationId' => encrypt($basicInformationId),
                                        'delivery' => encrypt($result->delivery_mode)
                                    ]) }}" method="GET">
                                        <input type="hidden" name="studyId" value="{{ ($result->study_id) }}">
                                        <input type="hidden" name="componentId" value="{{ ($result->course_components_id) }}">                                  
                                        <input type="hidden" name="hasComponents" value="{{ ($result->component_name) }}">                                   <button type="submit" style="background:none;border:none;padding:0;">
                                            <div class="alert alert-warning" role="alert">
                                                The distribution of the total marks is incomplete. Please click here to allocate the remaining 40 marks.
                                            </div>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <form action="{{ route('coordinator.courseCASettings', [
                                    'courseIdValue' => encrypt($result->course_id),
                                    'basicInformationId' => encrypt($basicInformationId),
                                    'delivery' => encrypt($result->delivery_mode)
                                ]) }}" method="GET">
                                    <input type="hidden" name="studyId" value="{{ ($result->study_id) }}">
                                    <input type="hidden" name="componentId" value="{{ ($result->course_components_id) }}">
                                    <input type="hidden" name="hasComponents" value="{{ ($result->component_name) }}">
                                    <button type="submit" style="background:none;border:none;padding:0;">
                                        <div class="alert alert-danger" role="alert">
                                            No Assessment Type Found, click here to set up Assessment Types.
                                        </div>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <!-- Course details go here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>