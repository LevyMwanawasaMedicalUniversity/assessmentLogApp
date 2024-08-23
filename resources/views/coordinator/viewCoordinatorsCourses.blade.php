<x-app-layout>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>My Courses</h1>
        @include('layouts.alerts')
        <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i>
                Please ensure that you make your upload under the correct delivery mode (<span style="color:blue"><b>Fulltime</b></span> or <span style="color:green"><b>Distance</b></span>) for each course.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
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
                            <h5 class="card-title">Your Courses</h5>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-1 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-9">
                        </div>

                        <!-- Table with hoverable rows -->
                        <div style="overflow-x:auto;">
                            <table id="myTable" class="table table-hover">                        
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Course Name</th>
                                    <th scope="col">Course Code</th>
                                    <th scope="col">Programme Name</th>
                                    <th scope="col">Delivery Mode</th>
                                    @if(!$results[0]->StudyID == 165)
                                        <th scope="col">Year Of Study</th>
                                    @endif
                                    <th scope="col">Number Of Uploads</th>
                                    <th scope="col" class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        @include('coordinator.components.uploadAssessmentTypeModal')
                                        @include('coordinator.components.viewAssessmentTypeModal')
                                        @php
                                        $course_components_id = null;
                                        $assessmentDetails = \App\Models\CourseAssessment::select(
                                                'course_assessments.basic_information_id',
                                                'assessment_types.assesment_type_name',
                                                'assessment_types.id',
                                                'course_assessments.delivery_mode',
                                                DB::raw('count(course_assessments.course_assessments_id) as total')
                                            )
                                            ->where('course_assessments.course_id', $result->ID)
                                            ->where('course_assessments.delivery_mode', $result->Delivery)
                                            ->where('course_assessments.study_id', $result->StudyID)
                                            //->where('course_assessments.component_id', $course_components_id)
                                            ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
                                            ->groupBy('assessment_types.id','course_assessments.basic_information_id', 'assessment_types.assesment_type_name','course_assessments.delivery_mode')
                                            ->get();
                                        $totalAssessments = $assessmentDetails->sum('total');

                                        @endphp
                                        <tr>
                                            {{-- <th scope="row">1</th> --}}
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$result->CourseDescription}}</td>
                                            <td>{{$result->CourseName}}</td>
                                            <td>{{$result->Name}}</td>
                                            <td style="color: {{ $result->Delivery == 'Fulltime' ? 'blue' : ($result->Delivery == 'Distance' ? 'green' : 'black') }}">
                                                <b>{{$result->Delivery}}</b>
                                            </td>
                                            @if(!$result->StudyID == 165)
                                                <td>Year {{$result->YearOfStudy}}</td>
                                            @endif
                                            <td>
                                                <form action="{{ route('coordinator.showCaWithin', encrypt($result->ID)) }}" method="GET">
                                                    <input type="hidden" name="studyId" value="{{ $result->StudyID }}">
                                                    <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;">
                                                        {{ $totalAssessments ? $totalAssessments : 0 }} assessments
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-right">
                                                @if( $result->StudyID == 165)
                                                    <div class="btn-group float-end" role="group" aria-label="Button group">
                                                        <form action="{{ route('coordinator.viewCourseWithComponents', ['courseIdValue' => encrypt($result->ID), 'basicInformationId' => encrypt($result->basicInformationId), 'delivery' => encrypt($result->Delivery)]) }}" method="GET" class="d-inline">
                                                            <input type="hidden" name="studyId" value="{{ $result->StudyID }}">
                                                            <input type="hidden" name="isSettings" value="0">
                                                            <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                                                View
                                                            </button>
                                                        </form>                                            
                                                        <form action="{{ route('coordinator.viewCourseWithComponents', ['courseIdValue' => encrypt($result->ID), 'basicInformationId' => encrypt($result->basicInformationId), 'delivery' => encrypt($result->Delivery)]) }}" method="GET" class="d-inline">
                                                            <input type="hidden" name="studyId" value="{{ ($result->StudyID) }}">
                                                            <input type="hidden" name="isSettings" value="1">
                                                            <button type="submit" class="btn btn-warning font-weight-bold py-2 px-4 rounded-0">
                                                                Settings
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="btn-group float-end" role="group" aria-label="Button group">
                                                        @if(auth()->user()->hasPermissionTo('Coordinator'))
                                                            <button type="button" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0" data-bs-toggle="modal" data-bs-target="#uploadCourseModal{{ $result->ID }}{{ $result->Delivery }}{{$result->StudyID}}" data-courseid="{{ $result->ID }}" data-delivery="{{ $result->Delivery }}">
                                                                Upload
                                                            </button>
                                                        @endif
                                                            <button type="button" class="btn btn-success font-weight-bold py-2 px-4 rounded-0" data-bs-toggle="modal" data-bs-target="#viewCourseModal{{ $result->ID }}{{ $result->Delivery }}{{$result->StudyID}}" data-courseid="{{ $result->ID }}" data-delivery="{{ $result->Delivery }}">
                                                                View
                                                            </button>
                                                        
                                                        
                                                        <form action="{{ route('coordinator.courseCASettings', ['courseIdValue' => encrypt($result->ID), 'basicInformationId' => encrypt($result->basicInformationId), 'delivery' => encrypt($result->Delivery)]) }}" method="GET" class="d-inline">
                                                            <input type="hidden" name="studyId" value="{{ ($result->StudyID) }}">
                                                            <input type="hidden" name="hasComponents" value="0">
                                                            <input type="hidden" name="componentName" value="">
                                                            <button type="submit" class="btn btn-warning font-weight-bold py-2 px-4 rounded-0">
                                                                Settings
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
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
    function myFunction() {
        var input, filter, table, tr, td, i, txtValue1, txtValue2;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            if (td1) {
            txtValue1 = td1.textContent || td1.innerText;
            txtValue2 = td2.textContent || td2.innerText;
            if (txtValue1.toUpperCase().indexOf(filter) > -1 || txtValue2.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
            }       
        }
    }
</script>
</x-app-layout>