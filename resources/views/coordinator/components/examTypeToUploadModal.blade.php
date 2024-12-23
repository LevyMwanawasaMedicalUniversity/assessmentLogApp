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
                    /*$courseAssessmenetTypes = \App\Models\CATypeMarksAllocation::where('course_id', $result->ID)
                        ->where('delivery_mode', $result->Delivery)
                        ->where('study_id', $result->StudyID)
                        ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                        ->select('assessment_types.id','assessment_types.assesment_type_name')
                        ->get();
                    $componentId = null;

                    $totalMarks = \App\Models\CATypeMarksAllocation::where('course_id', $result->ID)
                        ->where('study_id', $result->StudyID)
                        ->where('delivery_mode', $result->Delivery)
                        ->where('component_id', $componentId)
                        ->sum('total_marks');*/
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
                            
                                <form method="GET" action="{{ route('coordinator.uploadCaAndFinalExamAtOnce', ['courseIdValue' => encrypt($result->ID),'basicInformationId' => encrypt($result->basicInformationId)]) }}">
                                    <input type="hidden" name="delivery" value="{{ $result->Delivery }}">
                                    <input type="hidden" name="studyId" value="{{$result->StudyID}}">
                                    <input type="hidden" name="typeOfExam" value="1">
                                    <button type="submit" class="btn btn-light shadow-sm text-center mb-3" style="border: 2px solid green;">
                                        <div class="p-3 text-dark">
                                            CA : 40%, Exam : 60%
                                        </div>
                                    </button>
                                </form>
                                <form method="GET" action="{{ route('coordinator.uploadCaAndFinalExamAtOnce', ['courseIdValue' => encrypt($result->ID),'basicInformationId' => encrypt($result->basicInformationId)]) }}">
                                    <input type="hidden" name="delivery" value="{{ $result->Delivery }}">
                                    <input type="hidden" name="studyId" value="{{$result->StudyID}}">
                                    <input type="hidden" name="typeOfExam" value="2">
                                    <button type="submit" class="btn btn-light shadow-sm text-center mb-3" style="border: 2px solid green;">
                                        <div class="p-3 text-dark">
                                            Exam Only 100%
                                        </div>
                                    </button>
                                </form>
                                
                            
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