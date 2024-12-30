<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{$assessmentType }} for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}} for {{$results->count()}} <span style="color: {{ $delivery == 'Fulltime' ? 'blue' : ($delivery == 'Distance' ? 'green' : 'black') }}">{{$delivery}}</span> students
            @if($hasComponents) in {{$hasComponents}}@endif
            
        </h2>
        @include('layouts.alerts')
        
        @php
            $mismatchedCount = $results->filter(function($result) use ($delivery) {
                // Check if basic_information is not null and if StudyType is set
                return isset($result->basic_information) && $result->basic_information->StudyType != $delivery;
            })->count();

            $nullBasicInformationCount = $results->filter(function($result) {
                // Check if basic_information is null
                return is_null($result->basic_information);
            })->count();

            $componentId = $results->first()->component_id;
            $courseAssessmentId = $results->first()->course_assessments_id;
            $courseId = $results->first()->course_id;
            $courseCode = $results->first()->course_code;
            $basicInformationId = $results->first()->basic_information_id;
            $studyId = $results->first()->study_id;
            $delivery = $results->first()->delivery_mode;
            $caType = $caTypeFromAssessment;
        @endphp   

        @if($mismatchedCount > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                    <b style="color:red">There are {{$mismatchedCount}} students who do not fall under the {{$delivery}} mode of study in Edurole. </b>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($nullBasicInformationCount > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                    <b style="color:red">There are {{$nullBasicInformationCount}} students numbers that do not have Edurole accounts. </b>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <nav>
            {{ Breadcrumbs::render() }}
        </nav>

    </div><!-- End Page Title -->
    @include('coordinator.components.addNewStudentResultsModal')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">{{$assessmentType }} for {{$results->count()}} students</h5>
                            <button type="button" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0" 
                                data-bs-toggle="modal" data-bs-target="#addNewStudentResults"
                                data-courseAssessmentsId="{{ $courseAssessmentId }}" 
                                data-componentId = "{{ $componentId }}"
                                data-basicInformationId = "{{ $basicInformationId }}"
                                data-courseId = "{{ $courseId }}"                                                            
                                >
                                Add Student
                            </button>
                            <div class=""> 
                                <button class="btn btn-info font-weight-bold py-2 px-4 rounded-0" id="exportBtn">Export to Excel</button>
                            </div>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by student number.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <!-- Table with hoverable rows -->
                        <div style="overflow-x:auto;">
                            <table id="myTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="px-4 py-2">Student Number</th>
                                        <th class="px-4 py-2">FirstName</th>
                                        <th class="px-4 py-2">LastName</th>
                                        <th class="px-4 py-2">Mode of Study</th> 
                                        <th class="px-4 py-2">Programme</th>
                                        <th class="px-4 py-2">School</th>
                                        <th class="px-4 py-2">Mark</th>
                                        <th class="px-4 py-2 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                    @include('coordinator.components.editStudentResultsModal')
                                        
                                            <tr class="border-t border-b hover:bg-gray-100">
                                                <td class="px-4 py-2" style="color: {{ is_null($result->basic_information) ? 'red' : 'black' }}; font-weight: {{ is_null($result->basic_information) ? 'bold' : 'normal' }};">
                                                    {{$loop->iteration}}
                                                </td>
                                                <td class="px-4 py-2" style="color: {{ is_null($result->basic_information) ? 'red' : 'black' }}; font-weight: {{ is_null($result->basic_information) ? 'bold' : 'normal' }};">
                                                    {{ $result->student_id }}
                                                </td>
                                                <td class="px-4 py-2" style="color: {{ isset($result->basic_information->FirstName) ? 'black' : 'red' }}; font-weight: {{ isset($result->basic_information->FirstName) ? 'normal' : 'bold' }};">
                                                    {{ $result->basic_information->FirstName ?? 'No Edurole' }}
                                                </td>
                                                <td class="px-4 py-2" style="color: {{ isset($result->basic_information->Surname) ? 'black' : 'red' }}; font-weight: {{ isset($result->basic_information->Surname) ? 'normal' : 'bold' }};">
                                                    {{$result->basic_information->Surname ?? 'account found'}}
                                                </td>
                                                <td class="px-4 py-2" style="color: {{ !isset($result->basic_information) || $result->basic_information->StudyType != $delivery ? 'red' : 'black' }}; font-weight: {{ !isset($result->basic_information) || $result->basic_information->StudyType != $delivery ? 'bold' : 'normal' }};">
                                                    {{ $result->basic_information->StudyType ?? 'for the' }}
                                                </td>
                                                <td class="px-4 py-2" style="color: {{ isset($result->basic_information->Programme) ? 'black' : 'red' }}; font-weight: {{ isset($result->basic_information->Programme) ? 'normal' : 'bold' }};">
                                                    {{$result->basic_information->Programme ?? 'student id'}}
                                                </td>                                                
                                                <td class="px-4 py-2" style="color: {{ isset($result->basic_information->School) ? 'black' : 'red' }}; font-weight: {{ isset($result->basic_information->School) ? 'normal' : 'bold' }};">
                                                    {{$result->basic_information->School ?? $result->student_id}}
                                                </td>                                             
                                                <td class="px-4 py-2" style="color: {{ is_null($result->basic_information) ? 'red' : 'black' }}; font-weight: {{ is_null($result->basic_information) ? 'bold' : 'normal' }};">
                                                    {{$result->cas_score}}
                                                </td>
                                                <td class="px-4 py-2 text-right">                                                
                                                    <div class="btn-group float-end" role="group" aria-label="Button group">
                                                        <form action="{{route('docket.studentsCAResults')}}" method="GET" class="d-inline">
                                                            @csrf
                                                            
                                                            <input type="hidden" name="studentId" value="{{ $result->student_id }}">
                                                            
                                                            <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                                                View 
                                                            </button>
                                                        </form>
                                                        {{-- @if (auth()->user()->hasPermissionTo('Dean')) --}}
                                                        {{-- <a href="{{ route('coordinator.editCaInCourse', ['courseAssessmenId' => encrypt($result->course_assessments_id), 'courseId' => encrypt($courseId), 'basicInformationId' => encrypt($basicInformationId)]) }}" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                                            Edit
                                                        </a>  --}}
                                                        {{-- <form action="{{route('coordinator.editAStudentsCaInCourse',['courseAssessmenId' => encrypt($result->course_assessments_id), 'courseId' => encrypt($courseId), 'basicInformationId' => encrypt($result->basic_information_id)])}}" method="GET" class="d-inline">
                                                            <input type="hidden" name="hasComponents" value="{{($hasComponents) }}">
                                                            <input type="hidden" name="studentId" value="{{($result->student_id)}}">
                                                            <input type="hidden" name="componentId" value="{{($result->component_id)}}">
                                                            <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                                                Edit
                                                            </button>
                                                        </form> --}}

                                                        {{-- <button type="button" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0" 
                                                            data-bs-toggle="modal" data-bs-target="#editStudentResults{{$result->student_id}}{{$result->course_assessments_id }}{{ $courseId }}{{$result->basic_information_id}}"
                                                            data-studentId="{{ $result->student_id }}" 
                                                            data-courseAssessmentsId="{{ $result->course_assessments_id }}" 
                                                            data-componentId = "{{ $result->component_id }}"
                                                            data-basicInformationId = "{{ $result->basic_information_id }}"
                                                            data-casScore = "{{ $result->cas_score }}"
                                                            data-courseId = "{{ $courseId }}"                                                            
                                                            >
                                                            Edit
                                                        </button> --}}
                                                        {{-- <form method="POST" action="{{ route('coordinator.deleteStudentCaInCourse') }}" onsubmit="return confirm('Are you sure you want to delete this?');">    
                                                            {{ method_field('DELETE') }}
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="courseAssessmentScoresId" value="{{ $result->course_assessment_scores_id }}">
                                                            <input type="hidden" name="caType" value="{{ $caType }}">
                                                            <input type="hidden" name="courseId" value="{{ $courseId }}">                                                           
                                                            <button type="submit" class="btn btn-danger font-weight-bold py-2 px-4 rounded-0">
                                                                Delete
                                                            </button>
                                                        </form> --}}
                                                        {{-- @endif --}}
                                                    </div>                                            
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@php
    $tableName = $assessmentType . ' for ' . $courseDetails->CourseDescription . ' - ' . $courseDetails->Name . ' for ' . $results->count() . ' ' . $delivery . ' students';
@endphp
<script>


    function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1]; // Change the index based on the column you want to filter
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
    var tableName = @json($tableName);
    document.getElementById('exportBtn').addEventListener('click', function() {
        var table = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
        XLSX.writeFile(wb, tableName + ".xlsx");
    });
</script>
</x-app-layout>