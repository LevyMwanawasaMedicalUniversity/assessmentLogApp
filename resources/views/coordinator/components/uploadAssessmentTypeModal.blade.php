<div class="container">
    <div class="modal fade" id="uploadCourseModal{{ $result->ID }}{{ $result->Delivery }}" tabindex="-1" role="dialog" aria-labelledby="uploadCourseModalLabel" aria-hidden="true">
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
                        ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                        ->select('assessment_types.id','assessment_types.assesment_type_name')
                        ->get();

                    $totalMarks = \App\Models\CATypeMarksAllocation::where('course_id', $result->ID)
                        ->where('delivery_mode', $result->Delivery)
                        ->sum('total_marks');
                @endphp
                <div class="modal-body">
                    <b>
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
                                        <form method="GET" action="{{ route('coordinator.uploadCa', ['statusId' => encrypt($courseAssessmenetType->id), 'courseIdValue' => encrypt($result->ID),'basicInformationId' => encrypt($result->basicInformationId)]) }}">
                                            <input type="hidden" name="delivery" value="{{ $result->Delivery }}">
                                            <button type="submit" class="btn btn-light shadow-sm text-center mb-3" style="border: 2px solid green;">
                                                <div class="p-3 text-dark">
                                                    {{ $courseAssessmenetType->assesment_type_name }}
                                                </div>
                                            </button>
                                        </form>
                                    @endforeach
                                @else
                                    <a href="{{ route('coordinator.courseCASettings', ['courseIdValue' => encrypt($result->ID),'basicInformationId' => encrypt($result->basicInformationId),'delivery' =>encrypt($result->Delivery)]) }}" >
                                        <div class="alert alert-warning" role="alert">
                                            The distribution of the total marks is incomplete. Please click here to allocate the remaining 40 marks.   
                                        </div>
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('coordinator.courseCASettings', ['courseIdValue' => encrypt($result->ID),'basicInformationId' => encrypt($result->basicInformationId),'delivery' =>encrypt($result->Delivery)]) }}" >
                                    <div class="alert alert-danger" role="alert">
                                        No Assessment Type Found , click here to set up Assessment Types.
                                    </div>
                                </a>
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