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
            <div class="col-md-12">
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
                    @php
                        $course = App\Models\EduroleCourses::where('ID', $results[0]->course_id)->first();
                        $courseName = $course->CourseDescription;
                        $courseCode = $course->Name;
                    @endphp

                    <div class="card-header">
                        <h4 class="card-title">
                            Continuous Assessment Components for {{$courseName}} - {{$courseCode}} {{$componentName}}
                        </h4>
                        <div class="col-md-12">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
        
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if (session('warning'))
                                <div class="alert alert-warning">
                                    {{ session('warning') }}
                                </div>
                            @endif
                        </div>

                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-primary">
                                    <tr>
                                        <th>CA Component</th>
                                        <th>Marks / Total Marks <span class="badge bg-secondary">40</span></th>
                                        <th class="text-end">Actions</th>   
                                    </tr>
                                </thead>
                                <tbody> 
                                    @foreach($results as $result)
                                        @php
                                            $course = App\Models\EduroleCourses::where('ID', $result->course_id)->first();
                                            $courseName = $course->CourseDescription;
                                            $courseCode = $course->Name;

                                            $totalMarks = \App\Models\AssessmentTypes::join('c_a_type_marks_allocations', 'c_a_type_marks_allocations.assessment_type_id', '=', 'assessment_types.id')
                                                ->join('students_continous_assessments','students_continous_assessments.ca_type', '=', 'assessment_types.id')
                                                ->where('students_continous_assessments.students_continous_assessment_id', $result->students_continous_assessment_id)
                                                ->where('students_continous_assessments.student_id', $studentNumber)
                                                ->where('students_continous_assessments.ca_type', $result->ca_type)
                                                ->where('c_a_type_marks_allocations.delivery_mode', $result->delivery_mode)
                                                ->where('c_a_type_marks_allocations.study_id', $result->study_id)
                                                ->where('c_a_type_marks_allocations.course_id', $result->course_id)
                                                ->where('c_a_type_marks_allocations.component_id', $result->component_id)
                                                ->select('c_a_type_marks_allocations.total_marks')
                                                ->first();

                                            $totalMarks = $totalMarks->total_marks;
                                        @endphp
                                        <tr>
                                            <td>{{$result->assesment_type_name}}</td>
                                            <td>
                                                <span class="badge bg-primary">{{$result->total_marks}}</span> 
                                                <b>/</b> 
                                                <span class="badge bg-secondary">{{$totalMarks}}</span>
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('docket.viewInSpecificCaComponent', ['courseId' => $result->course_id, 'caType' => $result->ca_type]) }}" method="GET">
                                                    <input type="hidden" name="component_name" value="{{encrypt($componentName)}}">
                                                    <input type="hidden" name="component_id" value="{{encrypt($result->component_id)}}">
                                                    <input type="hidden" name="student_id" value="{{encrypt($studentNumber)}}">
                                                    <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">CLICK HERE</button>
                                                </form>
                                            </td>
                                        </tr>
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
