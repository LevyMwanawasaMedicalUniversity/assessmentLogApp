<x-app-layout>
        <main id="main" class="main">
    <div class="pagetitle">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Total CAs for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}} for {{$results->count()}}
            <span style="color: {{ $delivery == 'Fulltime' ? 'blue' : ($delivery == 'Distance' ? 'green' : 'black') }}">{{$delivery}}</span> @if($hasComponents){{$hasComponents}}@endif students
        @php
            $tableName = 'Total CAs for ' . $courseDetails->CourseDescription . ' - ' . $courseDetails->Name . ' for '. $results->count() . ' ' . $delivery . ' ' . ($hasComponents ? 'in ' . $hasComponents : '') . ' students';
        @endphp
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
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Total CAs out of 40</h5>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by student number.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            
                            <div class=""> 
                                <button class="btn btn-info font-weight-bold py-2 px-4 rounded-0" id="exportBtn">Export to Excel</button>
                            </div>
                            @if (auth()->user()->hasPermissionTo('Dean'))
                                <a href="" class="btn btn-primary">PUBLISH CA</a>
                            @endif


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
                                        {{-- <th class="px-4 py-2">Academic Year</th> --}}
                                        <th class="px-4 py-2">Mark (40)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
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
                                                {{$result->total_marks}}
                                            </td>
                                            <td class="px-4 py-2 text-right">                                                
                                                <div class="btn-group float-end" role="group" aria-label="Button group">
                                                    

                                                    <form action="{{route('docket.studentsCAResults')}}" method="GET" class="d-inline">
                                                        @csrf
                                                        {{-- <input type="hidden" name="statusId" value="{{ encrypt($statusId) }}"> --}}
                                                        <input type="hidden" name="studentId" value="{{ $result->student_id }}">
                                                        {{-- <input type="hidden" name="courseIdValue" value="{{ encrypt($result->course_assessments_id) }}">
                                                        <input type="hidden" name="assessmentNumber" value="{{ encrypt($loop->iteration) }}">
                                                        <input type="hidden" name="hasComponents" value="{{($hasComponents) }}"> --}}
                                                        <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                                            View 
                                                        </button>
                                                    </form>
                                                
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