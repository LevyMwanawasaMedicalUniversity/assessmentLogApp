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
                                    @php
                                        $course = App\Models\EduroleCourses::where('ID', $results[0]->course_id)->first();
                                        $courseName = $course->CourseDescription;
                                        $courseCode = $course->Name;
                                    @endphp

                                    <div class="card-header">
                                        <h4 class="card-title">
                                            Continuous Assessment Components for {{$courseName}} - {{$courseCode}} {{$componentName}}
                                        </h4>
                                        

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
