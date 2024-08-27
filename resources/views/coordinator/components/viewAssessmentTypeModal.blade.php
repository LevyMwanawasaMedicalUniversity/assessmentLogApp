<div class="container">
    <div class="modal fade" id="viewCourseModal{{ $result->ID }}{{ $result->Delivery }}{{$result->StudyID}}" tabindex="-1" role="dialog" aria-labelledby="viewCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="viewCourseModalLabel">
                        <b>Select Assessment Type To View
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
                    ->where('study_id', $result->StudyID)
                    ->where('delivery_mode', $result->Delivery)
                    ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                    ->select('assessment_types.id','assessment_types.assesment_type_name')
                    ->get();
                $componentId = null;
                $component_name = null;

                $totalMarks = \App\Models\CATypeMarksAllocation::where('course_id', $result->ID)
                    ->where('study_id', $result->StudyID)
                    ->where('delivery_mode', $result->Delivery)
                    ->sum('total_marks');

                @endphp
                <div class="modal-body">
                    <b class="mb-3">
                        <span >{{$result->CourseDescription}} - {{$result->CourseName}} 
                            <b style="color: {{ $result->Delivery == 'Fulltime' ? 'blue' : ($result->Delivery == 'Distance' ? 'green' : 'black') }}">
                                {{$result->Delivery}}
                            </b>
                        </span>
                    </b>
                    {{-- {{$courseAssessmenetTypes}} --}}
                    <div class="container">
                        <div class="d-flex flex-column"> <!-- Flex container with vertical spacing -->
                            <!-- First Block -->
                            @if($courseAssessmenetTypes->count() > 0)
                                @if( $totalMarks == 40)
                                    @foreach ($courseAssessmenetTypes as $courseAssessmenetType )
                                        <form method="GET" action="{{ route('coordinator.viewAllCaInCourse', ['statusId' => encrypt($courseAssessmenetType->id), 'courseIdValue' => encrypt($result->ID),'basicInformationId' => encrypt($result->basicInformationId),'delivery'=>encrypt($result->Delivery)]) }}">
                                            <input type="hidden" name="delivery" value="{{ $result->Delivery }}">
                                            <input type="hidden" name="studyId" value="{{$result->StudyID}}">
                                            <input type="hidden" name="componentId" value="{{$componentId}}">
                                            <input type="hidden" name="hasComponents" value="{{ ($component_name) }}">
                                            <button type="submit" class="btn btn-light shadow-sm text-center mb-3" style="border: 2px solid green;">
                                                <div class="p-3 text-dark">
                                                    {{ $courseAssessmenetType->assesment_type_name }}
                                                </div>
                                            </button>
                                        </form>                                
                                    @endforeach
                                    <form method="GET" action="{{ route('coordinator.viewTotalCaInCourse', ['statusId' => encrypt($result->caType),'courseIdValue' => encrypt($result->ID),'basicInformationId' => encrypt($result->basicInformationId),'delivery'=>encrypt($result->Delivery)]) }}">
                                        <input type="hidden" name="componentId" value="{{$componentId}}">
                                        <input type="hidden" name="hasComponents" value="{{ ($component_name) }}">
                                        <button type="submit" class="btn btn-light shadow-sm text-center mb-3" style="border: 2px solid black;">
                                            <div class="p-3 text-dark">
                                                {{ __("Total CA") }}
                                            </div>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('coordinator.courseCASettings', [
                                        'courseIdValue' => encrypt($result->ID),
                                        'basicInformationId' => encrypt($result->basicInformationId),
                                        'delivery' => encrypt($result->Delivery)
                                    ]) }}" method="GET">
                                        <input type="hidden" name="studyId" value="{{ ($result->StudyID) }}">
                                        <input type="hidden" name="componentId" value="{{$componentId}}">
                                        <input type="hidden" name="hasComponents" value="{{ ($component_name) }}">
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
                                    <input type="hidden" name="hasComponents" value="{{ ($component_name) }}">
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