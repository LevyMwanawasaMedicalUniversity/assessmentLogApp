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
                        <div class="card-header">
                            
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Student Details</h5>
                                            <div class="row">
                                                <!-- Image Section -->
                                                <div class="col-12 text-center mb-3">
                                                    <div style="width: 180px; height: 200px; overflow: hidden; border: 2px solid black; margin: 0 auto;">
                                                        <img src="//edurole.lmmu.ac.zm/datastore/identities/pictures/{{ $studentDetails->ID }}.png" style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                </div>

                                                <!-- Student Details -->
                                                <div class="col-12 text-center mb-2">
                                                    <small class="text-muted">Student Name</small>
                                                    <p class="mb-1">{{$studentDetails->FirstName}} {{$studentDetails->Surname}}</p>
                                                </div>

                                                <div class="col-12 text-center mb-2">
                                                    <small class="text-muted">Student Number</small>
                                                    <p class="mb-1">{{$studentDetails->ID}}</p>
                                                </div>

                                                <div class="col-12 text-center mb-2">
                                                    <small class="text-muted">Mode of Study</small>
                                                    <p class="mb-1">{{$studentDetails->StudyType}}</p>
                                                </div>

                                                <div class="col-12 text-center mb-2">
                                                    <small class="text-muted">Email</small>
                                                    <p class="mb-1">{{$studentDetails->PrivateEmail}}</p>
                                                </div>

                                                <div class="col-12 text-center mb-2">
                                                    <small class="text-muted">Programme</small>
                                                    <p class="mb-1">{{$studentDetails->Name}}</p>
                                                </div>

                                                <div class="col-12 text-center mb-2">
                                                    <small class="text-muted">School</small>
                                                    <p class="mb-1">{{$studentDetails->Description}}</p>
                                                </div>
                                            </div>
                                                                                    </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Student Results</h5>
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
                                                                        <div class="btn-group" role="group">
                                                                            <form action="{{ route('docket.viewCaComponents', encrypt($result->course_id)) }}" method="GET">
                                                                                <input type="hidden" name="student_id" value="{{encrypt($studentNumber)}}">
                                                                                <input type="hidden" name="study_id" value="{{encrypt($result->study_id)}}">
                                                                                <input type="hidden" name="delivery_mode" value="{{encrypt($result->delivery_mode)}}">
                                                                                <input type="hidden" name="course_id" value="{{encrypt($result->course_id)}}">
                                                                                <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">VIEW</button>
                                                                            </form>
                                                                            <form action="{{ route('docket.deleteStudentCourseAssements', encrypt($result->course_id)) }}" method="POST" onsubmit="return confirmDelete()">
                                                                                @csrf
                                                                                <input type="hidden" name="study_id" value="{{encrypt($result->study_id)}}">
                                                                                <input type="hidden" name="student_id" value="{{encrypt($studentNumber)}}">
                                                                                <input type="hidden" name="delivery_mode" value="{{encrypt($result->delivery_mode)}}">
                                                                                <input type="hidden" name="course_id" value="{{encrypt($result->course_id)}}">
                                                                                <button type="submit" class="btn btn-danger font-weight-bold py-2 px-4 rounded-0">DELETE</button>
                                                                            </form>
                                                                        </div>
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
                                                                            @if ($numberOfUniqueInstances > 0)
                                                                                <span class="badge bg-primary">{{ number_format($result->total_marks / $numberOfUniqueInstances, 2) }}</span> <b>/</b>
                                                                                <span class="badge bg-secondary">40</span>
                                                                            @else
                                                                                <span class="badge bg-danger">No components available</span>
                                                                            @endif
                                                                        </td>

                                                                    <td class="text-end">
                                                                        <div class="btn-group" role="group">
                                                                            <form action="{{ route('docket.viewCaComponentsWithComponent', encrypt($result->course_id)) }}" method="GET">
                                                                                <input type="hidden" name="study_id" value="{{encrypt($result->study_id)}}">
                                                                                <input type="hidden" name="student_id" value="{{encrypt($studentNumber)}}">
                                                                                <input type="hidden" name="delivery_mode" value="{{encrypt($result->delivery_mode)}}">
                                                                                <input type="hidden" name="course_id" value="{{encrypt($result->course_id)}}">
                                                                                <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">VIEW</button>
                                                                            </form>
                                                                            <form action="{{ route('docket.deleteStudentCourseAssements', encrypt($result->course_id)) }}" method="POST" onsubmit="return confirmDelete()">
                                                                                @csrf
                                                                                <input type="hidden" name="study_id" value="{{encrypt($result->study_id)}}">
                                                                                <input type="hidden" name="student_id" value="{{encrypt($studentNumber)}}">
                                                                                <input type="hidden" name="delivery_mode" value="{{encrypt($result->delivery_mode)}}">
                                                                                <input type="hidden" name="course_id" value="{{encrypt($result->course_id)}}">
                                                                                <button type="submit" class="btn btn-danger font-weight-bold py-2 px-4 rounded-0">DELETE</button>
                                                                            </form>
                                                                        </div>
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
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->

    <script>

        function confirmDelete() {
            return confirm('Are you sure you want to delete?');
        }
        document.getElementById('exportBtn').addEventListener('click', function() {
            var table = document.getElementById('myTable');
            var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
            XLSX.writeFile(wb, "ALL COORDINATORS.xlsx");
        });
    </script>
</x-app-layout>
