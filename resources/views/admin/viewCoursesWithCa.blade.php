<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1></h1>
        @include('layouts.alerts')
        <nav>
            
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Courses With Continous Assessments {{$results->count()}}</h5>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by course code." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="px-4 py-2">Course Code</th>
                                    <th class="px-4 py-2">Coordinator</th>
                                    <th class="px-4 py-2">Delivery Mode</th>
                                    {{-- <th class="px-4 py-2">Email</th> --}}
                                    <th class="px-4 py-2">Programme Name</th>
                                    <th class="px-4 py-2">Course Name</th>
                                    <th class="px-4 py-2">School</th>
                                    <th class="px-4 py-2">Count</th>
                                    <th class="px-4 py-2 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                @php
                                        $academicYear = \App\Models\Setting::getCurrentAcademicYear();
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
                                            ->where('course_assessments.academic_year', $academicYear)
                                            ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
                                            ->groupBy('assessment_types.id','course_assessments.basic_information_id', 'assessment_types.assesment_type_name','course_assessments.delivery_mode')
                                            ->get();
                                        $totalAssessments = $assessmentDetails->sum('total');

                                        @endphp
                                    <tr class="border-t border-b hover:bg-gray-100">
                                        <td class="px-4 py-2">{{$loop->iteration}}</td>
                                        <td class="px-4 py-2">
                                            {{ $result->CourseName }}                                                
                                        </td>
                                        <td class="px-4 py-2">{{$result->Firstname}} {{$result->Surname}}</td>
                                        <td style="color: {{ $result->Delivery == 'Fulltime' ? 'blue' : ($result->Delivery == 'Distance' ? 'green' : 'black') }}">
                                            {{$result->Delivery}}
                                        </td>
                                        {{-- <td class="px-4 py-2">{{$result->PrivateEmail}}</td> --}}
                                        <td class="px-4 py-2">{{$result->Name}}</td>  
                                        <td class="px-4 py-2">{{$result->CourseDescription}}</td>
                                        <td class="px-4 py-2">{{$result->SchoolName}}</td> 
                                        <td class="px-4 py-2">{{$totalAssessments}}</td>
                                        <td class="px-4 py-2 text-end">
                                            <form action="{{ route('coordinator.showCaWithin', encrypt($result->ID)) }}" method="GET">
                                                <input type="hidden" name="studyId" value="{{ $result->StudyID }}">
                                                <input type="hidden" name="delivery" value="{{ $result->Delivery }}">   
                                                {{-- <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;"> --}}
                                                <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                                    View
                                                </button>
                                            </form>
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