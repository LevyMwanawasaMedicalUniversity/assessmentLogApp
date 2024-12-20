<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1>Final Exam for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}} 
            <span style="color: {{ $delivery == 'Distance' ? 'green' : ($delivery == 'Fulltime' ? 'blue' : 'black') }}">
            {{ $delivery }} 
            </span>
        </h1>
        @include('layouts.alerts')
        @php
            
            $courseId = $results->first()->course_id;
            $courseCode = $results->first()->course_code;
            $basicInformationId = $results->first()->basic_information_id;
            $studyId = $results->first()->study_id;
            $delivery = $results->first()->delivery_mode;
            

        @endphp
        {{-- <nav>
            {{ Breadcrumbs::render() }}
        </nav> --}}
    </div><!-- End Page Title -->
    @include('coordinator.components.addNewStudentToExamModal')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Final Exam</5>
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
                                        <th class="px-4 py-2">Student Number</th>
                                        <th class="px-4 py-2">First Name</th>
                                        <th class="px-4 py-2">Last Name</th>
                                        <th class="px-4 py-2">Study Type</th>
                                        {{-- <th class="px-4 py-2">Programme</th> --}}
                                        <th class="px-4 py-2">School</th>
                                        {{-- <th class="px-4 py-2">Course Code</th>  --}}
                                        {{-- <th class="px-4 py-2">Date Uploaded</th>
                                        <th class="px-4 py-2">Date Updated</th> --}}
                                        <th class="px-4 py-2">Academic Year</th>  
                                        @if($results->first()->type_of_exam == 1)
                                        <th class="px-4 py-2">CAS Score</th>
                                        @endif                                  
                                        <th class="px-4 py-2">Final Exam</th>
                                        <th class="px-4 py-2">Grade</th>
                                        <th class="px-4 py-2 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                    @include('coordinator.components.editStudentExamResultsModal')
                                        <tr class="border-t border-b hover:bg-gray-100">
                                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-2">{{ $result->student_id }}</td>
                                            <td class="px-4 py-2" style="color: {{ isset($result->basic_information->FirstName) ? 'black' : 'red' }}; font-weight: {{ isset($result->basic_information->FirstName) ? 'normal' : 'bold' }};">
                                                {{ $result->basic_information->FirstName ?? 'No Edurole' }}
                                            </td>
                                            <td class="px-4 py-2" style="color: {{ isset($result->basic_information->Surname) ? 'black' : 'red' }}; font-weight: {{ isset($result->basic_information->Surname) ? 'normal' : 'bold' }};">
                                                {{$result->basic_information->Surname ?? 'account found'}}
                                            </td>
                                            <td class="px-4 py-2" style="color: {{ !isset($result->basic_information) || $result->basic_information->StudyType != $delivery ? 'red' : 'black' }}; font-weight: {{ !isset($result->basic_information) || $result->basic_information->StudyType != $delivery ? 'bold' : 'normal' }};">
                                                {{ $result->basic_information->StudyType ?? 'for the' }}
                                            </td>
                                            {{-- <td class="px-4 py-2" style="color: {{ isset($result->basic_information->Programme) ? 'black' : 'red' }}; font-weight: {{ isset($result->basic_information->Programme) ? 'normal' : 'bold' }};">
                                                {{$result->basic_information->Programme ?? 'student id'}}
                                            </td>                                                 --}}
                                            <td class="px-4 py-2" style="color: {{ isset($result->basic_information->School) ? 'black' : 'red' }}; font-weight: {{ isset($result->basic_information->School) ? 'normal' : 'bold' }};">
                                                {{$result->basic_information->School ?? $result->student_id}}
                                            </td> 
                                            <td class="px-4 py-2">{{ $result->academic_year }}</td>
                                            @if($result->type_of_exam == 1)
                                                <td class="px-4 py-2">{{$result->Ca}}</td>
                                                @php
                                                    $totalMark = $result->Ca + $result->FinalExam;
                                                @endphp
                                            @else
                                                @php
                                                    $totalMark = $result->FinalExam;
                                                @endphp
                                            @endif 
                                            <td class="px-4 py-2">{{ $result->FinalExam }}</td>
                                            <td class="px-4 py-2">
                                                @php
                                                    if ($totalMark >= 90) $grade = 'A+';
                                                    elseif ($totalMark >= 80) $grade = 'A';
                                                    elseif ($totalMark >= 70) $grade = 'B+';
                                                    elseif ($totalMark >= 60) $grade = 'B';
                                                    elseif ($totalMark >= 55) $grade = 'C+';
                                                    elseif ($totalMark >= 50) $grade = 'C';
                                                    elseif ($totalMark >= 45) $grade = 'D+';
                                                    elseif ($totalMark >= 40) $grade = 'D';
                                                    else $grade = 'F';

                                                    // Add color coding for grades
                                                    $gradeColor = match($grade) {
                                                        'A+', 'A' => 'green',
                                                        'B+', 'B' => 'blue',
                                                        'C+', 'C' => 'orange',
                                                        'D+', 'D' => '#DAA520', // goldenrod
                                                        'F' => 'red',
                                                        default => 'black'
                                                    };
                                                @endphp
                                                <span style="color: {{ $gradeColor }}; font-weight: bold;">{{ $grade }}</span>
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