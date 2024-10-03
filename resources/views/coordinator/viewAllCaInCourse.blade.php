<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1>{{$assessmentType }}s for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}} 
            <span style="color: {{ $delivery == 'Distance' ? 'green' : ($delivery == 'Fulltime' ? 'blue' : 'black') }}">
            {{ $delivery }} @if($hasComponents) in {{$hasComponents}}@endif
            </span>
        </h1>
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
                            <h5 class="card-title">{{$assessmentType }}s</h5>
                            <div class=""> 
                                <button class="btn btn-info font-weight-bold py-2 px-4 rounded-0" id="exportBtn">Export to Excel</button>
                            </div>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by date uploaded.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <!-- Table with hoverable rows -->
                        <div style="overflow-x:auto;">
                            <table id="myTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="px-4 py-2">Upload Number</th>
                                        <th class="px-4 py-2">Description</th>
                                        
                                        <th class="px-4 py-2">Time Created</th>
                                        <th class="px-4 py-2">Time Updated</th>
                                        <th class="px-4 py-2">Academic Year</th>
                                        <th class="px-4 py-2 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        <tr class="border-t border-b hover:bg-gray-100">
                                            <td class="px-4 py-2">{{$loop->iteration}}</td>
                                            <td class="px-4 py-2">{{$assessmentType }} {{ $loop->iteration }}</td>
                                            <td class="px-4 py-2"><a href="{{ route('editCourseAssessmentDescription', ['courseAssessmentId' => encrypt($result->course_assessments_id), 'statusId' => encrypt($result->ca_type)]) }}">{{ $result->description ? $result->description : 'No Description' }}</a></td>
                                            
                                            <td class="px-4 py-2">{{$result->updated_at}}</td>
                                            <td class="px-4 py-2">{{$result->created_at}}</td>
                                            <td class="px-4 py-2">{{$result->academic_year}}</td>
                                            <td class="px-4 py-2 text-end">
                                                <div class="btn-group float-end" role="group" aria-label="Button group">
                                                    {{-- <a href="{{ route('coordinator.viewSpecificCaInCourse', ['statusId' => encrypt($statusId), 'courseIdValue' => encrypt($result->course_assessments_id), 'assessmentNumber' => encrypt($loop->iteration)]) }}" class="btn btn-success font-weight-bold py-2 px-4 rounded-start">
                                                        View
                                                    </a> --}}
                                                    <form action="{{ route('coordinator.viewSpecificCaInCourse',['statusId' => encrypt($statusId), 'courseIdValue' => encrypt($result->course_assessments_id), 'assessmentNumber' => encrypt($loop->iteration)]) }}" method="GET" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="statusId" value="{{ encrypt($statusId) }}">
                                                        <input type="hidden" name="caType" value="{{ encrypt($result->ca_type) }}">
                                                        <input type="hidden" name="courseIdValue" value="{{ encrypt($result->course_assessments_id) }}">
                                                        <input type="hidden" name="assessmentNumber" value="{{ encrypt($loop->iteration) }}">
                                                        <input type="hidden" name="componentId" value="{{($componentId)}}">
                                                        <input type="hidden" name="hasComponents" value="{{($hasComponents) }}">
                                                        <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                                            View
                                                        </button>
                                                    </form>
                                                    {{-- @if (auth()->user()->hasPermissionTo('Dean')) --}}
                                                    {{-- <a href="{{ route('coordinator.editCaInCourse', ['courseAssessmenId' => encrypt($result->course_assessments_id), 'courseId' => encrypt($courseId), 'basicInformationId' => encrypt($basicInformationId)]) }}" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                                        Edit
                                                    </a>  --}}
                                                    <form action="{{ route('coordinator.editCaInCourse', ['courseAssessmenId' => encrypt($result->course_assessments_id), 'courseId' => encrypt($courseId), 'basicInformationId' => encrypt($basicInformationId)]) }}" method="GET" class="d-inline">
                                                        <input type="hidden" name="hasComponents" value="{{($hasComponents) }}">
                                                        <input type="hidden" name="componentId" value="{{($componentId)}}">
                                                        <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                                            Update
                                                        </button>
                                                    </form>
                                                    {{-- <form method="POST" action="{{ route('coordinator.deleteCaInCourse', ['courseAssessmenId' => encrypt($result->course_assessments_id), 'courseId' => encrypt($courseId)]) }}" onsubmit="return confirm('Are you sure you want to delete this?');">
                                                        {{ method_field('DELETE') }}
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="academicYear" value={{$result->academic_year}}>
                                                        <input type="hidden" name="ca_type" value={{$result->ca_type}}>
                                                        <input type="hidden" name="course_id" value={{$courseId}}>
                                                        <input type="hidden" name="delivery" value={{$delivery}}>
                                                        <input type="hidden" name="study_id" value={{$studyId}}>
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
<script>
    function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[2]; // Change the index based on the column you want to filter
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
</script>
<script>
    document.getElementById('exportBtn').addEventListener('click', function() {
        var table = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
        XLSX.writeFile(wb, "ALL COORDINATORS.xlsx");
    });
</script>
</x-app-layout>