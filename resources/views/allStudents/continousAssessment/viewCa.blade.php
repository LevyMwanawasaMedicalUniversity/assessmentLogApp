<x-app-layout>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>{{$studentDetails->ID}} Results Details</h1>
            @include('layouts.alerts')
            <nav>
                {{-- {{ Breadcrumbs::render() }} --}}
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Student Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-4 col-form-label">Student Name</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="{{$studentDetails->FirstName}} {{$studentDetails->Surname}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="studentNumber" class="col-sm-4 col-form-label">Student Number</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="{{$studentDetails->ID}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="studyMode" class="col-sm-4 col-form-label">Mode Of Study</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="{{$studentDetails->StudyType}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="email" class="col-sm-4 col-form-label">Email</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="{{$studentDetails->PrivateEmail}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="programme" class="col-sm-4 col-form-label">Programme</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="{{$studentDetails->Name}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="school" class="col-sm-4 col-form-label">School</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="{{$studentDetails->Description}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>Course</th>
                                            <th>Overall CA <span class="badge bg-secondary">40</span></th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $result)
                                            @php
                                                $course = App\Models\EduroleCourses::where('ID', $result->course_id)->first();
                                                $courseName = $course->CourseDescription;
                                                $courseCode = $course->Name;
                                            @endphp

                                            @if(in_array($result->course_id, [1106, 1105]) || $result->study_id != 165)
                                                <tr>
                                                    <td>{{$courseName}}-{{$courseCode}}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{$result->total_marks}}</span> <b>/</b>
                                                        <span class="badge bg-secondary">40</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <form action="{{ route('docket.viewCaComponents', encrypt($result->course_id)) }}" method="GET">
                                                            <input type="hidden" name="student_id" value="{{encrypt($studentNumber)}}">
                                                            <input type="hidden" name="study_id" value="{{encrypt($result->study_id)}}">
                                                            <input type="hidden" name="delivery_mode" value="{{encrypt($result->delivery_mode)}}">
                                                            <input type="hidden" name="course_id" value="{{encrypt($result->course_id)}}">
                                                            <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">CLICK HERE</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>{{$courseName}}-{{$courseCode}}</td>
                                                    @php
                                                        $numberOfUniqueInstances = App\Models\StudentsContinousAssessment::where('students_continous_assessments.course_id', $result->course_id)
                                                            ->where('students_continous_assessments.delivery_mode', $result->delivery_mode)
                                                            ->where('students_continous_assessments.study_id', $result->study_id)
                                                            ->whereNotNull('students_continous_assessments.component_id')
                                                            ->distinct('students_continous_assessments.component_id')
                                                            ->count('students_continous_assessments.component_id');
                                                    @endphp
                                                    <td>
                                                        <span class="badge bg-primary">{{ number_format($result->total_marks / $numberOfUniqueInstances, 2) }}</span> <b>/</b>
                                                        <span class="badge bg-secondary">40</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <form action="{{ route('docket.viewCaComponentsWithComponent', encrypt($result->course_id)) }}" method="GET">
                                                            <input type="hidden" name="study_id" value="{{encrypt($result->study_id)}}">
                                                            <input type="hidden" name="student_id" value="{{encrypt($studentNumber)}}">
                                                            <input type="hidden" name="delivery_mode" value="{{encrypt($result->delivery_mode)}}">
                                                            <input type="hidden" name="course_id" value="{{encrypt($result->course_id)}}">
                                                            <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">CLICK HERE</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->

    <script>
        document.getElementById('exportBtn').addEventListener('click', function() {
            var table = document.getElementById('myTable');
            var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
            XLSX.writeFile(wb, "ALL COORDINATORS.xlsx");
        });
    </script>
</x-app-layout>
