<x-app-layout>
    <main id="main" class="main">

        <div class="pagetitle">
        <h1>Dashboard</h1>
        @include('layouts.alerts')
        <nav>
            
            
            {{ Breadcrumbs::render() }}
            </ol>
        </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
            <div class="row">

                <!-- Sales Card -->
                <div class="col-xxl-4 col-md-6">
                <div class="card info-card sales-card">                   

                        <div class="card-body">
                            <h5 class="card-title">Students With CA <span>| Uploaded</span></h5>

                            @php
                            $uniqueStudentIds = \App\Models\CourseAssessmentScores::distinct()
                                ->pluck('student_id');
                            @endphp

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $uniqueStudentIds->count() }}</h6>
                                    <span class="text-success small pt-1 fw-bold">From</span> <span class="text-muted small pt-2 ps-1">Assessments System</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- End Sales Card -->

                <!-- Revenue Card -->
                <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">

                    

                    <div class="card-body">
                    <h5 class="card-title">Courses from Edurole <span>| Total</span></h5>

                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-calendar2-week-fill"></i>
                        </div>
                        <div class="ps-3">
                        {{-- <h6>{{ ceil($coursesFromEdurole->unique('ID')->count() / 3) }}</h6> --}}
                        <h6>{{ ($coursesFromEdurole->unique('ID','Delivery','StudyID')->count() ) }}</h6>
                        <span class="text-primary small pt-1 fw-bold">With Coordinators Assigned</span>

                        </div>
                    </div>
                    </div>

                </div>
                </div><!-- End Revenue Card -->

                <!-- Customers Card -->
                <div class="col-xxl-4 col-xl-12">

                <div class="card info-card customers-card">                    

                    <div class="card-body">
                    <h5 class="card-title">Courses From LM-MAX <span>| Total</span></h5>
                    @php
                    $totalCa = \App\Models\CourseAssessment::distinct(['course_id', 'delivery_mode','study_id'])
                                    ->count();
                    @endphp
                    @if (auth()->user()->hasPermissionTo('Registrar'))
                        <a href="{{ route('coordinator.viewOnlyProgrammesWithCa') }}">
                    @endif
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $totalCa }}</h6>
                                <span class="text-success small pt-1 fw-bold">With Continuous Assessments</span>
                            </div>
                        </div>
                    @if (auth()->user()->hasPermissionTo('Registrar'))
                        </a>
                    @endif


                    </div>
                </div>

                </div><!-- End Customers Card -->

                <!-- Reports -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Course With CA Per Programme</h5>
                            @php
                                // Ensure $coursesWithCA and $coursesFromEdurole are collections
                                $coursesWithCA = collect($coursesWithCA);
                                $coursesFromEdurole = collect($coursesFromEdurole);

                                // Extract unique ProgrammeCodes from coursesFromEdurole
                                $programmeCodes = $coursesFromEdurole->pluck('ProgrammeCode')->unique()->values();

                                // Initialize arrays to hold counts for each ProgrammeCode
                                $coursesWithCAProgrammeCountsArray = [];
                                $coursesFromEduroleProgrammeCountsArray = [];

                                

                                foreach ($programmeCodes as $code) {
                                    // Count courses with CA for the current ProgrammeCode
                                    $coursesWithCAProgrammeCountsArray[] = $coursesWithCA->where('ProgrammeCode', $code)->count();

                                    // Count courses from Edurole for the current ProgrammeCode
                                    if (in_array($code, ['BBS', 'NS'])) {
                                        $coursesFromEduroleProgrammeCountsArray[] = $coursesFromEdurole->where('ProgrammeCode', $code)->count();
                                    } else {
                                        // Clone the query for each iteration to avoid modifying the original query
                                        $coursesFromCourseElectives = clone $coursesFromCourseElectivesQuery;
                                        $coursesFromCourseElectives = $coursesFromCourseElectives
                                            ->where('study.ShortName', $code)
                                            ->distinct()
                                            ->pluck('course-electives.CourseID')
                                            ->toArray();

                                        $coursesFromEduroleProgrammeCountsArray[] = $resultsForCount->where('ProgrammeCode', $code)
                                            ->whereIn('ID', $coursesFromCourseElectives)
                                            ->count();
                                    }
                                }
                            @endphp
                            
                            <!-- Column Chart -->
                            <div id="columnChart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    // Ensure programmeCodes are correctly formatted as an array of strings
                                    const programmeCodes = @json($programmeCodes);

                                    // Log to check the data
                                    console.log('Programme Codes:', programmeCodes);
                                    console.log('Courses with CA Programme Counts:', @json($coursesWithCAProgrammeCountsArray));
                                    console.log('Courses from Edurole Programme Counts:', @json($coursesFromEduroleProgrammeCountsArray));

                                    new ApexCharts(document.querySelector("#columnChart"), {
                                        series: [{
                                            name: 'Courses with CA',
                                            data: @json($coursesWithCAProgrammeCountsArray)
                                        }, {
                                            name: 'Courses from Edurole',
                                            data: @json($coursesFromEduroleProgrammeCountsArray)
                                        }],
                                        chart: {
                                            type: 'bar',
                                            height: 350
                                        },
                                        plotOptions: {
                                            bar: {
                                                horizontal: false,
                                                columnWidth: '55%',
                                                endingShape: 'rounded'
                                            },
                                        },
                                        dataLabels: {
                                            enabled: false
                                        },
                                        stroke: {
                                            show: true,
                                            width: 2,
                                            colors: ['transparent']
                                        },
                                        xaxis: {
                                            categories: programmeCodes,
                                        },
                                        yaxis: {
                                            title: {
                                                text: 'Count'
                                            }
                                        },
                                        fill: {
                                            opacity: 1
                                        },
                                        tooltip: {
                                            y: {
                                                formatter: function(val) {
                                                    return val + " courses"
                                                }
                                            }
                                        }
                                    }).render();
                                });
                            </script>
                            <!-- End Column Chart -->
                        </div>
                    </div>
                </div><!-- End Reports -->




                <!-- Recent Sales -->
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">

                        

                        <div class="card-body">
                        <h5 class="card-title">Deans Per School </h5>

                        <table class="table table-borderless">
                            <thead>
                            <tr>
                                
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">School</th>
                                {{-- <th scope="col">Total Programmes</th> --}}
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($deansData as $dean) 
                                    <tr>
                                        
                                        <td>{{$dean->FirstName}}</td>
                                        <td>{{$dean->Surname}}</td>
                                        <td>{{$dean->SchoolName}}</td>
                                        {{-- <td>{{$counts[$dean->ID]}}</td> --}}
                                        <td>
                                            <a href="{{ route('admin.viewCoordinatorsUnderDean', ['schoolId' => encrypt($dean->ParentID)]) }}">
                                                <span class="badge bg-success">View</span>
                                            </a>
                                        </td>
                                    </tr>
                                    
                                @endforeach                                                   
                            </tbody>
                        </table>

                        </div>

                    </div>
                </div><!-- End Recent Sales -->

                <!-- Recent Orders -->

            </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">

            

            <!-- Budget Report -->
            <div class="card">
                <div class="card-body">
                <div class="justify-between d-flex justify-content-between">
                    <h5 class="card-title">CA PER SCHOOL</h5>
                    <button id="exportCSV" class="btn btn-primary mb-3 mt-3">Export to CSV</button>
                </div>
                    <!-- Vertical Bar Chart -->
                    <div id="verticalBarChart" style="min-height: 400px;" class="echart"></div>

                    @php
                        $schools = ['SOHS', 'SOPHES', 'SOMCS', 'DRGS', 'SON', 'IBBS'];

                        $coursesWithCACounts = [];
                        $coursesFromEduroleCounts = [];                    
                        
                    
                        foreach ($schools as $school) {
                            $getSchools = \App\Models\EduroleSchool::where('Description', $school)->first();
                            $schoolId = $getSchools->ID;
                            $schoolProgrammes = \App\Models\EduroleStudy::where('ParentID', $schoolId)->pluck('ID')->toArray();

                            
                            //$coursesWithCACounts[$school] = $coursesWithCA->where('SchoolName', $school)->unique('ID')->count();
                            $coursesWithCACounts[$school] = \App\Models\CourseAssessment::whereIn('study_id', $schoolProgrammes)
                                ->select('course_id', 'delivery_mode')
                                //->distinct()
                                ->groupBy('course_id', 'delivery_mode')
                                ->get()
                                ->count();
                            $coursesFromEduroleCounts[$school] = $coursesFromEdurole->where('SchoolName', $school)->unique('ID')->count();
                        }

                        /*$getCourdinatoresCourses = \App\Models\EduroleStudy::where('ProgrammesAvailable', $result->basicInformationId)->pluck('ID')->toArray();

                        $coursesWithCa = \App\Models\CourseAssessment::whereIn('study_id', $getCourdinatoresCourses)
                            ->select('course_id', 'delivery_mode')
                            //->distinct()
                            ->groupBy('course_id', 'delivery_mode')
                            ->get()
                            ->count();*/

                        // Convert arrays to indexed arrays for JavaScript
                        $schoolNames = array_keys($coursesWithCACounts);
                        $coursesWithCACountsArray = array_values($coursesWithCACounts);
                        $coursesFromEduroleCountsArray = array_values($coursesFromEduroleCounts);
                    @endphp

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            echarts.init(document.querySelector("#verticalBarChart")).setOption({
                                title: {
                                    text: ''
                                },
                                tooltip: {
                                    trigger: 'axis',
                                    axisPointer: {
                                        type: 'shadow'
                                    }
                                },
                                legend: {
                                    data: ['Courses with CA', 'Total Courses']
                                },
                                grid: {
                                    left: '3%',
                                    right: '4%',
                                    bottom: '3%',
                                    containLabel: true
                                },
                                xAxis: {
                                    type: 'value',
                                    boundaryGap: [0, 0.01]
                                },
                                yAxis: {
                                    type: 'category',
                                    data: @json($schoolNames)
                                },
                                series: [
                                    {
                                        name: 'Courses with CA',
                                        type: 'bar',
                                        data: @json($coursesWithCACountsArray)
                                    },
                                    {
                                        name: 'Total Courses',
                                        type: 'bar',
                                        data: @json($coursesFromEduroleCountsArray)
                                    }
                                ]
                            });

                            // Export to CSV functionality
                            document.querySelector("#exportCSV").addEventListener("click", function() {
                                let csvContent = "data:text/csv;charset=utf-8,";
                                csvContent += "School,Courses with CA,Total Courses\n";

                                const schoolNames = @json($schoolNames);
                                const coursesWithCACountsArray = @json($coursesWithCACountsArray);
                                const coursesFromEduroleCountsArray = @json($coursesFromEduroleCountsArray);

                                schoolNames.forEach((school, index) => {
                                    csvContent += `${school},${coursesWithCACountsArray[index]},${coursesFromEduroleCountsArray[index]}\n`;
                                });

                                const encodedUri = encodeURI(csvContent);
                                const link = document.createElement("a");
                                link.setAttribute("href", encodedUri);
                                link.setAttribute("download", "CA_per_School.csv");
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            });
                        });
                    </script>
                    <!-- End Vertical Bar Chart -->
                </div>
            </div>







            <!-- Website Traffic -->
            <div class="card">
                <div class="card-body pb-0">
                    <h5 class="card-title">Number Of Coordinators : {{$coursesFromEdurole->unique('username')->count()}}</h5>
                    <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

                    @php
                        // Aggregate the number of unique usernames per SchoolName
                        $userCountsPerSchool = $coursesFromEdurole->groupBy('SchoolName')->map(function ($group) {
                            return $group->unique('username')->count();
                        });

                        // Convert to arrays for JavaScript
                        $schoolNames = $userCountsPerSchool->keys()->toArray();
                        $userCounts = $userCountsPerSchool->values()->toArray();
                    @endphp

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const schoolNames = @json($schoolNames);
                            const userCounts = @json($userCounts);

                            const data = schoolNames.map((schoolName, index) => ({
                                value: userCounts[index],
                                name: schoolName
                            }));

                            echarts.init(document.querySelector("#trafficChart")).setOption({
                                tooltip: {
                                    trigger: 'item'
                                },
                                legend: {
                                    top: '5%',
                                    left: 'center'
                                },
                                series: [{
                                    name: 'Users Per School',
                                    type: 'pie',
                                    radius: ['40%', '70%'],
                                    avoidLabelOverlap: false,
                                    label: {
                                        show: false,
                                        position: 'center'
                                    },
                                    emphasis: {
                                        label: {
                                            show: true,
                                            fontSize: '18',
                                            fontWeight: 'bold'
                                        }
                                    },
                                    labelLine: {
                                        show: false
                                    },
                                    data: data
                                }]
                            });
                        });
                    </script>
                </div>
            </div>



            <!-- News & Updates Traffic -->
            

            </div><!-- End Right side columns -->

        </div>
        </section>

    </main><!-- End #main -->
</x-app-layout>
