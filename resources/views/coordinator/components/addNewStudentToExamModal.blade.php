<div class="container">
    <div class="modal fade" id="addNewStudentResults" tabindex="-1" role="dialog" aria-labelledby="viewCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="addNewStudentResults">
                        <b>Add a new student Result
                        </b>
                    </h3>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Edit Details</h5>
                                        <form class="row align-items-center" action="{{ route('coordinator.importStudentCA') }}" method="POST" enctype="multipart/form-data"> <!-- Added align-items-center for better vertical alignment -->
                                            @csrf
                                            <div class="row">
                                                {{-- <input type="hidden" name="hasComponents" value="{{($hasComponents) }}"> --}}
                                                {{-- <input type="hidden" name="oldStudentNumber" value="{{($result->student_id)}}"> --}}
                                                {{-- <input type="hidden" name="component_id" value="{{($componentId)}}"> --}}
                                                {{-- <input type="hidden" name="course_assessment_id" value="{{($courseAssessmentId)}}"> --}}
                                                <input type="hidden" name="course_id" value="{{($courseId)}}">
                                                {{-- <input type="hidden" name="course_code" value="{{ $courseCode}}"> --}}
                                                <input type="hidden" name="academicYear" value="2024">
                                                <input type="hidden" name="basicInformationId" value="{{($basicInformationId)}}">
                                                <input type="hidden" name="study_id" value="{{($studyId)}}">
                                                <input type="hidden" name="delivery" value="{{($delivery)}}">
                                                <input type="hidden" name="course_assessment_scores_id" value="{{($results->first()->course_assessment_scores_id)}}">
                                                
                                                <div class="col-md-9">
                                                    <label for="studentNumber" class="form-label">Student Number</label>
                                                    <input name="studentNumber" value="" type="number" class="form-control" placeholder="Enter student number" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="mark" class="form-label">Marks</label>
                                                    <input name="mark" value="" type="number" class="form-control" placeholder="The Mark" max="100" step="0.01" required oninput="validateMaxValue(this)">
                                                </div>

                                                <script>
                                                    function validateMaxValue(input) {
                                                        if (input.value > 100) {
                                                            input.value = 100;
                                                        }
                                                    }
                                                </script>
                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">SAVE CHANGES</button>
                                                    {{-- <button type="reset" class="btn btn-secondary">Reset</button> --}}
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
