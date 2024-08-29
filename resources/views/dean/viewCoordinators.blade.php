<x-app-layout>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Coordinators</h1>
        @include('layouts.alerts')
        <nav>
            {{ Breadcrumbs::render() }}
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Coordinators @isset($schoolId) in {{$results->first()->SchoolName}} @else on Edurole @endif</h5>
                            <div class=""> 
                                <button class="btn btn-info" id="exportBtn">Export to Excel</button>
                            </div>
                            @if(auth()->user()->hasPermissionTo('Administrator'))
                                <form method="post" action="{{ route('admin.importCoordinators') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success font-weight-bold">
                                        Import
                                    </button>
                                </form>
                            @endif
                            
                        </div>
                        <!-- Table with hoverable rows -->
                        <div style="overflow-x:auto;">
                            <table id="myTable" class="table table-hover">
                            @php

                            if (isset($schoolId)) {
                                $studyId = \App\Models\EduroleStudy::select('ID')
                                    ->where('ParentID', '=', $schoolId)
                                    ->pluck('ID')
                                    ->toArray();

                                $totalCa = \App\Models\CourseAssessment::whereIn('study_id', $studyId)
                                    ->distinct(['course_id', 'delivery_mode',])
                                    ->count();
                            } else {
                                $totalCa = \App\Models\CourseAssessment::distinct(['course_id', 'delivery_mode','study_id'])
                                    ->count();
                            }


                            @endphp
                                {{-- <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"> --}}
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Firstname</th>
                                        <th scope="col">Lastname</th>
                                        <th scope="col">Programme Coordinated</th>
                                        <th scope="col">School</th>
                                        <th scope="col">Last Login</th>
                                        <th scope="col">Courses in @isset($schoolId) {{$results->first()->SchoolName}} @else Edurole @endif<span class="text-primary"> {{ $totalCoursesCoordinated }} </span></th>
                                        <th scope="col">Courses With CA <span class="text-success"> {{$totalCa}} </span></th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                @php
                                
                                @endphp
                                    @foreach($results as $result)
                                        @include('coordinator.components.uploadAssessmentTypeModal')
                                        @include('coordinator.components.viewAssessmentTypeModal')
                                        <tr>
                                            @php
                                                $user = \App\Models\User::where('basic_information_id', $result->basicInformationId)->first();
                                                $numberOfCourses = \App\Models\EduroleCourseElective::select('c.ID')
                                                    ->join('courses as c', 'c.ID', '=', 'course-electives.CourseID')
                                                    ->join('program-course-link as pcl', 'c.ID', '=', 'pcl.CourseID')
                                                    ->join('programmes as p', 'p.ID', '=', 'pcl.ProgramID')
                                                    ->join('student-study-link as ssl2', 'ssl2.StudentID', '=', 'course-electives.StudentID')
                                                    ->join('study as s', 's.ID', '=', 'ssl2.StudyID')                                                    
                                                    ->where('course-electives.Year', '=', '2024')
                                                    ->where('s.ShortName',$result->ProgrammeCode )
                                                    //->where('s.ID',$result->StudyID )
                                                    ->whereNotIn('c.Name',['MAT101', 'PHY101', 'CHM101', 'BIO101','BAB201', 'CAG201', 'CVS301', 'GIT301','GRA201','IHD201','MCT201','NER301','PEB201','REN301','RES301'])
                                                    ->distinct('c.ID')
                                                    ->count();

                                                /*$couresWithCa = \App\Models\CourseAssessment::select('course_id')
                                                    //->where('course_id', '=', $result->ID )
                                                    ->where('study_id', '=', $result->StudyID )
                                                    ->distinct('course_id', 'delivery_mode')
                                                    ->get()
                                                    ->count();
                                                */
                                                $getCourdinatoresCourses = \App\Models\EduroleStudy::where('ProgrammesAvailable', $result->basicInformationId)->pluck('ID')->toArray();

                                                $coursesWithCa = \App\Models\CourseAssessment::whereIn('study_id', $getCourdinatoresCourses)
                                                    ->select('course_id', 'delivery_mode')
                                                    //->distinct()
                                                    ->groupBy('course_id', 'delivery_mode')
                                                    ->get()
                                                    ->count();
                                                    
                                            @endphp
                                            {{-- <th scope="row">1</th> --}}
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $result->Firstname }}</td>
                                            <td>{{ $result->Surname }}</td>
                                            <td>{{ $result->Name }}</td>
                                            <td>{{ $result->School }}</td>
                                            <td style="color: {{ $user && $user->last_login_at ? 'blue' : 'red' }};">
                                                {{ $user && $user->last_login_at ? $user->last_login_at : 'NEVER' }}
                                            </td>
                                            @if(($result->StudyID == 163) || ($result->StudyID == 165))
                                                <td>{{ $counts[$result->StudyID] ?? '0' }} Courses</td>
                                            @else
                                                <td>{{$numberOfCourses}} Courses</td>
                                                {{-- <td>{{ $counts[$result->StudyID] ?? '0' }} Courses</td> --}}
                                            @endif
                                            <td>
                                                <form action="{{ route('coordinator.viewOnlyProgrammesWithCaForCoordinator', $result->basicInformationId) }}" method="GET">
                                                    <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;">
                                                        {{-- {{ $withCa[$result->StudyID] ?? '0' }} Courses --}}
                                                        {{$coursesWithCa}} Courses
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <form method="GET" action="{{ route('admin.viewCoordinatorsCourses', ['basicInformationId' => encrypt($result->basicInformationId)]) }}">
                                                    <button type="submit" class="btn btn-primary font-weight-bold">
                                                        View
                                                    </button>
                                                </form>
                                            </td>
                                            
                                        </tr>                            
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- End Table with hoverable rows -->

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

