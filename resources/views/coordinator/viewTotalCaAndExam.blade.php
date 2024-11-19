<x-app-layout>
    <main id="main" class="main">
        <div class="pagetitle">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Total CAs for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}} for {{$results->count()}}
                <span style="color: {{ $delivery == 'Fulltime' ? 'blue' : ($delivery == 'Distance' ? 'green' : 'black') }}">
                    {{$delivery}}
                </span> @if($hasComponents){{$hasComponents}}@endif students
            </h2>
            @include('layouts.alerts')

            @php
                $tableName = 'Total CAs for ' . $courseDetails->CourseDescription . ' - ' . $courseDetails->Name . ' for '. $results->count() . ' ' . $delivery . ' ' . ($hasComponents ? 'in ' . $hasComponents : '') . ' students';

                $mismatchedCount = $results->filter(function($result) use ($delivery) {
                    return isset($result->basic_information) && $result->basic_information->StudyType != $delivery;
                })->count();

                $nullBasicInformationCount = $results->filter(function($result) {
                    return is_null($result->basic_information);
                })->count();

                // Count grades and calculate average, highest, and lowest scores
                $gradeCounts = ['A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D+' => 0, 'D' => 0, 'F' => 0];
                $totalScores = 0;
                $highestScore = null;
                $lowestScore = null;

                foreach ($results as $result) {
                    $caAndExamMark = $result->total_marks + $result->TotalMarks;
                    $totalScores += $caAndExamMark;
                    $highestScore = $highestScore === null ? $caAndExamMark : max($highestScore, $caAndExamMark);
                    $lowestScore = $lowestScore === null ? $caAndExamMark : min($lowestScore, $caAndExamMark);

                    if ($caAndExamMark >= 90) $gradeCounts['A+']++;
                    elseif ($caAndExamMark >= 80) $gradeCounts['A']++;
                    elseif ($caAndExamMark >= 70) $gradeCounts['B+']++;
                    elseif ($caAndExamMark >= 60) $gradeCounts['B']++;
                    elseif ($caAndExamMark >= 55) $gradeCounts['C+']++;
                    elseif ($caAndExamMark >= 50) $gradeCounts['C']++;
                    elseif ($caAndExamMark >= 45) $gradeCounts['D+']++;
                    elseif ($caAndExamMark >= 40) $gradeCounts['D']++;
                    else $gradeCounts['F']++;
                }

                $averageScore = $results->count() > 0 ? round($totalScores / $results->count(), 2) : 0;
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
        </div>

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="totalCAs-tab" data-bs-toggle="tab" href="#totalCAs" role="tab" aria-controls="totalCAs" aria-selected="true">Total CAs</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="gradeBreakdown-tab" data-bs-toggle="tab" href="#gradeBreakdown" role="tab" aria-controls="gradeBreakdown" aria-selected="false">Grade Breakdown</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Total CAs Table Tab -->
            <div class="tab-pane fade show active" id="totalCAs" role="tabpanel" aria-labelledby="totalCAs-tab">
                <section class="section">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="card-title">Total CAs out of 40</h5>
                                        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by student number.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <button class="btn btn-info font-weight-bold py-2 px-4 rounded-0" id="exportBtn">Export to Excel</button>
                                        @if (auth()->user()->hasPermissionTo('Dean'))
                                            <a href="" class="btn btn-primary">PUBLISH CA</a>
                                        @endif
                                    </div>
                                    <div style="overflow-x:auto;">
                                        <table id="myTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Student Number</th>
                                                    <th>FirstName</th>
                                                    <th>LastName</th>
                                                    <th>Mode of Study</th>
                                                    <th>Programme</th>
                                                    <th>School</th>
                                                    <th>CA Mark (40)</th>
                                                    <th>Exam Mark (60)</th>
                                                    <th>Total Mark (100)</th>
                                                    <th>Grade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($results as $result)
                                                    @php
                                                        $caAndExamMark = $result->total_marks + $result->TotalMarks;
                                                        $grade = $caAndExamMark >= 90 ? 'A+' : ($caAndExamMark >= 80 ? 'A' : ($caAndExamMark >= 70 ? 'B+' : ($caAndExamMark >= 60 ? 'B' : ($caAndExamMark >= 55 ? 'C+' : ($caAndExamMark >= 50 ? 'C' : ($caAndExamMark >= 45 ? 'D+' : ($caAndExamMark >= 40 ? 'D' : 'F')))))));
                                                    @endphp
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{ $result->student_id }}</td>
                                                        <td>{{ $result->basic_information->FirstName ?? 'No Edurole' }}</td>
                                                        <td>{{ $result->basic_information->Surname ?? 'account found' }}</td>
                                                        <td>{{ $result->basic_information->StudyType ?? 'for the' }}</td>
                                                        <td>{{ $result->basic_information->Programme ?? 'student id' }}</td>
                                                        <td>{{ $result->basic_information->School ?? $result->student_id }}</td>
                                                        <td>{{ $result->total_marks }}</td>
                                                        <td>{{ $result->TotalMarks }}</td>
                                                        <td>{{ $caAndExamMark }}</td>
                                                        <td>{{ $grade }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Grade Breakdown Table Tab -->
            <div class="tab-pane fade" id="gradeBreakdown" role="tabpanel" aria-labelledby="gradeBreakdown-tab">
                <section class="section">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Grade Breakdown</h5>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>Number of Students</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($gradeCounts as $grade => $count)
                                                <tr>
                                                    <td>{{ $grade }}</td>
                                                    <td>{{ $count }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Average Score</th>
                                                <td>{{ $averageScore }}</td>
                                            </tr>
                                            <tr>
                                                <th>Highest Score</th>
                                                <td>{{ $highestScore }}</td>
                                            </tr>
                                            <tr>
                                                <th>Lowest Score</th>
                                                <td>{{ $lowestScore }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script>
        function myFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                }
            }
        }

        document.getElementById('exportBtn').addEventListener('click', function() {
            var table = document.getElementById('myTable');
            var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
            XLSX.writeFile(wb, {{ json_encode($tableName) }} + ".xlsx");
        });
    </script>
</x-app-layout>
