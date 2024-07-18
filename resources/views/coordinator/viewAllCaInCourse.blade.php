<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1>{{$assessmentType }}s for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}}</h1>
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
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by date uploaded.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Upload Number</th>
                                    <th class="px-4 py-2">Time Created</th>
                                    <th class="px-4 py-2">Time Updated</th>
                                    <th class="px-4 py-2">Academic Year</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                    <tr class="border-t border-b hover:bg-gray-100">
                                        <td class="px-4 py-2">{{$assessmentType }} {{ $loop->iteration }}</td>
                                        <td class="px-4 py-2">{{$result->updated_at}}</td>
                                        <td class="px-4 py-2">{{$result->created_at}}</td>
                                        <td class="px-4 py-2">{{$result->academic_year}}</td>
                                        <td class="px-4 py-2">
                                            <div class="btn-group" role="group" aria-label="Button group">
                                                <a href="{{ route('coordinator.viewSpecificCaInCourse', ['statusId' => encrypt($statusId), 'courseIdValue' => encrypt($result->course_assessments_id), 'assessmentNumber' => encrypt($loop->iteration)]) }}" class="btn btn-success font-weight-bold py-2 px-4 rounded-start">
                                                    View
                                                </a>
                                                @if (auth()->user()->hasPermissionTo('Dean'))
                                                <a href="{{ route('coordinator.editCaInCourse', ['courseAssessmenId' => encrypt($result->course_assessments_id), 'courseId' => encrypt($courseId), 'basicInformationId' => encrypt($basicInformationId)]) }}" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                                    Edit
                                                </a> 
                                                <form method="POST" action="{{ route('coordinator.deleteCaInCourse', ['courseAssessmenId' => encrypt($result->course_assessments_id), 'courseId' => encrypt($courseId)]) }}" onsubmit="return confirm('Are you sure you want to delete this?');">
                                                    {{ method_field('DELETE') }}
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="academicYear" value={{$result->academic_year}}>
                                                    <input type="hidden" name="ca_type" value={{$statusId}}>
                                                    <button type="submit" class="btn btn-danger font-weight-bold py-2 px-4 rounded-end">
                                                        Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>                                                                     
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
</script>
</x-app-layout>