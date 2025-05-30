<div class="container">
    <div class="modal fade" id="uploadCourseModal{{ $result->ID }}{{ $result->Delivery }}{{$result->StudyID}}" tabindex="-1" role="dialog" aria-labelledby="uploadCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="uploadCourseModalLabel">
                        <b>Select Assessment Type To Upload
                            <b style="color: {{ $result->Delivery == 'Fulltime' ? 'blue' : ($result->Delivery == 'Distance' ? 'green' : 'black') }}">
                                {{$result->Delivery}}
                            </b>
                        </b>
                    </h3>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @php
                    $courseAssessmenetTypes = \App\Models\CATypeMarksAllocation::where('course_id', $result->ID)
                        ->where('delivery_mode', $result->Delivery)
                        ->where('study_id', $result->StudyID)
                        ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                        ->select('assessment_types.id', 'assessment_types.assesment_type_name', 'c_a_type_marks_allocations.assessment_count')
                        ->get();
                    $componentId = null;

                    $totalMarks = \App\Models\CATypeMarksAllocation::where('course_id', $result->ID)
                        ->where('study_id', $result->StudyID)
                        ->where('delivery_mode', $result->Delivery)
                        ->where('component_id', $componentId)
                        ->sum('total_marks');

                    // Get current academic year from settings
                    $academicYear = \App\Models\Setting::where('key', 'current_academic_year')->first()->value ?? 2024;
                    
                    // For each assessment type, get the count of existing uploads
                    $existingUploads = [];
                    foreach ($courseAssessmenetTypes as $type) {
                        $existingUploads[$type->id] = \App\Models\CourseAssessment::where('course_id', $result->ID)
                            ->where('delivery_mode', $result->Delivery)
                            ->where('study_id', $result->StudyID)
                            ->where('component_id', $componentId)
                            ->where('ca_type', $type->id)
                            ->where('academic_year', $academicYear)
                            ->count();
                    }
                @endphp
                <div class="modal-body">
                    <div class="mb-3">
                        <h5>
                            <span>{{$result->CourseDescription}} - {{$result->CourseName}} 
                                <b style="color: {{ $result->Delivery == 'Fulltime' ? 'blue' : ($result->Delivery == 'Distance' ? 'green' : 'black') }}">
                                    {{$result->Delivery}}
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
                                    @foreach ($courseAssessmenetTypes as $courseAssessmenetType)
                                        @php
                                            $currentCount = $existingUploads[$courseAssessmenetType->id] ?? 0;
                                            $maxAllowed = $courseAssessmenetType->assessment_count ?? 1;
                                            $canUploadMore = $currentCount < $maxAllowed;
                                        @endphp
                                        
                                        <form method="GET" action="{{ route('coordinator.uploadCa', ['statusId' => encrypt($courseAssessmenetType->id), 'courseIdValue' => encrypt($result->ID),'basicInformationId' => encrypt($result->basicInformationId)]) }}">
                                            <input type="hidden" name="delivery" value="{{ $result->Delivery }}">
                                            <input type="hidden" name="studyId" value="{{$result->StudyID}}">
                                            <input type="hidden" name="componentId" value="{{$componentId}}">
                                            <button type="submit" class="btn btn-light shadow-sm text-center mb-3" style="border: 2px solid {{ $canUploadMore ? 'green' : 'red' }};" {{ !$canUploadMore ? 'disabled' : '' }}>
                                                <div class="p-3 text-dark">
                                                    {{ $courseAssessmenetType->assesment_type_name }}
                                                    <div class="small {{ $canUploadMore ? 'text-success' : 'text-danger' }}">
                                                        {{ $currentCount }} of {{ $maxAllowed }} uploads used
                                                        @if(!$canUploadMore)
                                                            <br><strong>Maximum uploads reached</strong>
                                                        @endif
                                                    </div>
                                                </div>
                                            </button>
                                            @if(!$canUploadMore)
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('coordinator.courseCASettings', [
                                                            'courseIdValue' => encrypt($result->ID),
                                                            'basicInformationId' => encrypt($result->basicInformationId),
                                                            'delivery' => encrypt($result->Delivery)
                                                        ]) }}?studyId={{ $result->StudyID }}&componentId={{$componentId}}" class="btn btn-sm btn-outline-secondary me-2">
                                                            Change limit
                                                        </a>
                                                        <a href="{{ route('coordinator.viewAllCaInCourse', [
                                                            'statusId' => encrypt($courseAssessmenetType->id),
                                                            'courseIdValue' => encrypt($result->ID),
                                                            'basicInformationId' => encrypt($result->basicInformationId),
                                                            'delivery' => encrypt($result->Delivery)
                                                        ]) }}?studyId={{ $result->StudyID }}&componentId={{$componentId}}" class="btn btn-sm btn-outline-primary">
                                                            Update existing
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </form>
                                    @endforeach
                                @else
                                    <form action="{{ route('coordinator.courseCASettings', [
                                        'courseIdValue' => encrypt($result->ID),
                                        'basicInformationId' => encrypt($result->basicInformationId),
                                        'delivery' => encrypt($result->Delivery)
                                    ]) }}" method="GET">
                                        <input type="hidden" name="studyId" value="{{ ($result->StudyID) }}">
                                        <input type="hidden" name="componentId" value="{{$componentId}}">
                                        <button type="submit" style="background:none;border:none;padding:0;">
                                            <div class="alert alert-warning" role="alert">
                                                The distribution of the total marks is incomplete. Please click here to allocate the remaining 40 marks.
                                            </div>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <form action="{{ route('coordinator.courseCASettings', [
                                    'courseIdValue' => encrypt($result->ID),
                                    'basicInformationId' => encrypt($result->basicInformationId),
                                    'delivery' => encrypt($result->Delivery)
                                ]) }}" method="GET">
                                    <input type="hidden" name="studyId" value="{{ ($result->StudyID) }}">
                                    <input type="hidden" name="componentId" value="{{$componentId}}">
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
                    <button type="button" class="btn btn-secondary font-weight-bold py-2 px-4 rounded-0" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>