<x-app-layout>
        <main id="main" class="main">
    <div class="pagetitle">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Total CAs for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}} for {{$results->count()}} <span style="color: {{ $delivery == 'Fulltime' ? 'blue' : ($delivery == 'Distance' ? 'green' : 'black') }}">{{$delivery}}</span> students
        </h2>
        @include('layouts.alerts')
        

        @php
            $mismatchedCount = $results->filter(function($result) use ($delivery) {
                return $result->basic_information->StudyType != $delivery;
            })->count();
        @endphp
        @php
            $nullBasicInformationCount = $results->filter(function($result) {
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
                                            <td class="px-4 py-2">{{$loop->iteration}}</td>
                                            <td class="px-4 py-2">{{ $result->student_id }}</td>
                                            @if($result->basic_information)
                                                <td class="px-4 py-2">{{ $result->basic_information->FirstName }}</td>
                                                <td class="px-4 py-2">{{ $result->basic_information->Surname }}</td>
                                                <td class="px-4 py-2" style="color: {{ $result->basic_information->StudyType != $delivery ? 'red' : 'black' }};">
                                                    {{ $result->basic_information->StudyType }}
                                                </td>
                                                <td class="px-4 py-2">{{ $result->basic_information->Programme }}</td>
                                                <td class="px-4 py-2">{{ $result->basic_information->School }}</td>
                                            @else
                                                <td class="px-4 py-2" style="color:red" colspan="4">No Edurole account found for student id {{$result->student_id}}</td>
                                            @endif
                                            {{-- <td class="px-4 py-2">{{ $result->academic_year ?? 'N/A' }}</td> --}}
                                            <td class="px-4 py-2">{{ $result->total_marks }}</td>
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
</script>
</x-app-layout>