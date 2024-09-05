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
                                    <div class="card-header">
                                        <h4 class="card-title">Continuous Assessment Components for {{$courseName}} - {{$courseCode}}</h4>
                                        
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="text-primary">
                                                    <tr>
                                                        <th>Course Component</th>
                                                        <th>CA OUT OF <span class="badge bg-secondary">40</span></th> 
                                                        <th class="text-end">Actions</th>   
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($results as $result)
                                                        @php
                                                            $marks = App\Models\StudentsContinousAssessment::where('students_continous_assessments.course_id', $result->course_id)
                                                                ->where('students_continous_assessments.delivery_mode', $result->delivery_mode)
                                                                ->where('students_continous_assessments.study_id', $result->study_id)
                                                                ->where('students_continous_assessments.component_id', $result->course_component_id)
                                                                ->where('students_continous_assessments.student_id', $studentNumber)
                                                                ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
                                                                ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
                                                                ->groupBy('students_continous_assessments.student_id')
                                                                ->first();
                                                        @endphp

                                                        @if (!$marks)
                                                            @continue
                                                        @endif

                                                        <tr>
                                                            <td>{{$result->component_name}}</td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ number_format($marks->total_marks, 2) }}</span> <b>/</b>
                                                                <span class="badge bg-secondary">40</span>
                                                            </td>
                                                            <td class="text-end">
                                                                <form action="{{ route('docket.viewCaComponents', encrypt($result->course_id)) }}" method="GET">
                                                                    <input type="hidden" name="study_id" value="{{ encrypt($result->study_id) }}">
                                                                    <input type="hidden" name="delivery_mode" value="{{ encrypt($result->delivery_mode) }}">
                                                                    <input type="hidden" name="course_id" value="{{ encrypt($result->course_id) }}">
                                                                    <input type="hidden" name="student_id" value="{{ encrypt($studentNumber) }}">
                                                                    <input type="hidden" name="course_component_id" value="{{ encrypt($result->course_component_id) }}">
                                                                    <input type="hidden" name="component_name" value="{{ encrypt($result->component_name) }}">
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
