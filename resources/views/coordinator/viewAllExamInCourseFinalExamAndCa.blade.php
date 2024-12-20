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
        </div>

        @include('coordinator.components.addNewStudentToExamModal')

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="examResults-tab" data-bs-toggle="tab" href="#examResults" role="tab" aria-controls="examResults" aria-selected="true">Exam Results</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="gradeBreakdown-tab" data-bs-toggle="tab" href="#gradeBreakdown" role="tab" aria-controls="gradeBreakdown" aria-selected="false">Grade Breakdown</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Exam Results Tab -->
            <div class="tab-pane fade show active" id="examResults" role="tabpanel" aria-labelledby="examResults-tab">
                <section class="section">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="card-title">Final Exam</h5>
                                        <div class=""> 
                                            <button class="btn btn-info font-weight-bold py-2 px-4 rounded-0" id="exportBtn">Export to Excel</button>
                                        </div>
                                        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by student name.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    <!-- Your existing table code remains the same -->
                                    <div style="overflow-x:auto;">
                                        {{-- <div style="overflow-x:auto;"> --}}
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
                                        {{-- </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Grade Breakdown Tab -->
            <div class="tab-pane fade" id="gradeBreakdown" role="tabpanel" aria-labelledby="gradeBreakdown-tab">
                <section class="section">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Grade Distribution Analysis</h5>
                                    @php
                                        // Calculate grade statistics
                                        $gradeCounts = ['A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D+' => 0, 'D' => 0, 'F' => 0];
                                        $totalScores = 0;
                                        $highestScore = null;
                                        $lowestScore = null;
                                        $totalStudents = $results->count();

                                        foreach ($results as $result) {
                                            $totalMark = $result->type_of_exam == 1 ? 
                                                ($result->Ca + $result->FinalExam) : 
                                                $result->FinalExam;
                                            
                                            $totalScores += $totalMark;
                                            $highestScore = $highestScore === null ? $totalMark : max($highestScore, $totalMark);
                                            $lowestScore = $lowestScore === null ? $totalMark : min($lowestScore, $totalMark);

                                            if ($totalMark >= 90) $gradeCounts['A+']++;
                                            elseif ($totalMark >= 80) $gradeCounts['A']++;
                                            elseif ($totalMark >= 70) $gradeCounts['B+']++;
                                            elseif ($totalMark >= 60) $gradeCounts['B']++;
                                            elseif ($totalMark >= 55) $gradeCounts['C+']++;
                                            elseif ($totalMark >= 50) $gradeCounts['C']++;
                                            elseif ($totalMark >= 45) $gradeCounts['D+']++;
                                            elseif ($totalMark >= 40) $gradeCounts['D']++;
                                            else $gradeCounts['F']++;
                                        }

                                        $averageScore = $totalStudents > 0 ? round($totalScores / $totalStudents, 2) : 0;
                                        $passCount = array_sum(array_slice($gradeCounts, 0, -1)); // All except F
                                        $passRate = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 1) : 0;
                                    @endphp

                                    <div class="row">
                                        <!-- Grade Distribution Table -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Grade Distribution</h6>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Grade</th>
                                                        <th>Number of Students</th>
                                                        <th>Percentage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($gradeCounts as $grade => $count)
                                                        <tr>
                                                            <td>{{ $grade }}</td>
                                                            <td>{{ $count }}</td>
                                                            <td>{{ $totalStudents > 0 ? round(($count / $totalStudents) * 100, 1) : 0 }}%</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Statistics Summary -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Course Statistics</h6>
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th>Total Students</th>
                                                        <td>{{ $totalStudents }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Average Score</th>
                                                        <td>{{ $averageScore }}%</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Highest Score</th>
                                                        <td>{{ $highestScore }}%</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Lowest Score</th>
                                                        <td>{{ $lowestScore }}%</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Pass Rate</th>
                                                        <td>{{ $passRate }}%</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Your existing scripts -->
    <script>
        function myFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[2];
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
            XLSX.writeFile(wb, "Final Exam Results.xlsx");
        });
    </script>
</x-app-layout>