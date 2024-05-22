<x-app-layout>
    <main id="main" class="main">

        <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            
            <li class="breadcrumb-item active">Dashboard</li>
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
                    <h5 class="card-title">Coordinators <span>| Total</span></h5>

                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-cart"></i>
                        </div>
                        <div class="ps-3">
                        <h6>{{$coursesFromEdurole->unique('username')->count()}}</h6>
                        <span class="text-success small pt-1 fw-bold">From</span> <span class="text-muted small pt-2 ps-1">Edurole</span>

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
                        <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ps-3">
                        <h6>{{$coursesFromEdurole->unique('CourseName')->count()}}</h6>
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

                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                        <h6>{{$coursesWithCA->unique('CourseName')->count()}}</h6>
                        <span class="text-success small pt-1 fw-bold">With Continous Assessments</span>

                        </div>
                    </div>

                    </div>
                </div>

                </div><!-- End Customers Card -->

                <!-- Reports -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Course With CA Per Programme</h5>
                            @php
                                $coursesWithCAProgrammes = $coursesWithCA->groupBy('ProgrammeCode');
                                $coursesFromEduroleProgrammes = $coursesFromEdurole->groupBy('ProgrammeCode');

                                $coursesWithCAProgrammeCounts = $coursesWithCAProgrammes->map->count();
                                $coursesFromEduroleProgrammeCounts = $coursesFromEduroleProgrammes->map->count();
                            
                            @endphp
                            <!-- Column Chart -->
                            <div id="columnChart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new ApexCharts(document.querySelector("#columnChart"), {
                                        series: [{
                                            name: 'Courses with CA',
                                            data: @json($coursesWithCAProgrammeCounts->values()->toArray())
                                        }, {
                                            name: 'Courses from Edurole',
                                            data: @json($coursesFromEduroleProgrammeCounts->values()->toArray())
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
                                            categories: @json($coursesWithCAProgrammeCounts->keys()->toArray()),
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

                    <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                        </li>

                        <li><a class="dropdown-item" href="#">Today</a></li>
                        <li><a class="dropdown-item" href="#">This Month</a></li>
                        <li><a class="dropdown-item" href="#">This Year</a></li>
                    </ul>
                    </div>

                    <div class="card-body">
                    <h5 class="card-title">Recent Sales <span>| Today</span></h5>

                    <table class="table table-borderless datatable">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row"><a href="#">#2457</a></th>
                            <td>Brandon Jacob</td>
                            <td><a href="#" class="text-primary">At praesentium minu</a></td>
                            <td>$64</td>
                            <td><span class="badge bg-success">Approved</span></td>
                        </tr>
                        <tr>
                            <th scope="row"><a href="#">#2147</a></th>
                            <td>Bridie Kessler</td>
                            <td><a href="#" class="text-primary">Blanditiis dolor omnis similique</a></td>
                            <td>$47</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                        </tr>
                        <tr>
                            <th scope="row"><a href="#">#2049</a></th>
                            <td>Ashleigh Langosh</td>
                            <td><a href="#" class="text-primary">At recusandae consectetur</a></td>
                            <td>$147</td>
                            <td><span class="badge bg-success">Approved</span></td>
                        </tr>
                        <tr>
                            <th scope="row"><a href="#">#2644</a></th>
                            <td>Angus Grady</td>
                            <td><a href="#" class="text-primar">Ut voluptatem id earum et</a></td>
                            <td>$67</td>
                            <td><span class="badge bg-danger">Rejected</span></td>
                        </tr>
                        <tr>
                            <th scope="row"><a href="#">#2644</a></th>
                            <td>Raheem Lehner</td>
                            <td><a href="#" class="text-primary">Sunt similique distinctio</a></td>
                            <td>$165</td>
                            <td><span class="badge bg-success">Approved</span></td>
                        </tr>
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
                <div class="card-body pb-0">
                    <h5 class="card-title">CA PER SCHOOL <span>| This Month</span></h5>

                    <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

                    @php
                        $schools = ['SOHS', 'SOPHES', 'SOMCS', 'DRGS', 'SON', 'IBBS'];
                        $coursesWithCACounts = [];
                        $coursesFromEduroleCounts = [];

                        foreach ($schools as $school) {
                            $coursesWithCACounts[$school] = $coursesWithCA->where('SchoolName', $school)->count();
                            $coursesFromEduroleCounts[$school] = $coursesFromEdurole->where('SchoolName', $school)->count();
                        }
                    @endphp

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            var budgetChart = echarts.init(document.querySelector("#budgetChart"));

                            var option = {
                                legend: {
                                    data: ['Courses with CA', 'Total Courses']
                                },
                                radar: {
                                    indicator: [
                                        { name: 'SOHS', max: @json($coursesFromEduroleCounts['SOHS']) },
                                        { name: 'SOPHES', max: @json($coursesFromEduroleCounts['SOPHES']) },
                                        { name: 'SOMCS', max: @json($coursesFromEduroleCounts['SOMCS']) },
                                        { name: 'DRGS', max: @json($coursesFromEduroleCounts['DRGS']) },
                                        { name: 'SON', max: @json($coursesFromEduroleCounts['SON']) },
                                        { name: 'IBBS', max: @json($coursesFromEduroleCounts['IBBS']) }
                                    ]
                                },
                                series: [{
                                    name: 'Courses with CA vs Total Courses',
                                    type: 'radar',
                                    data: [
                                        {
                                            value: @json(array_values($coursesWithCACounts)),
                                            name: 'Courses with CA'
                                        },
                                        {
                                            value: @json(array_values($coursesFromEduroleCounts)),
                                            name: 'Total Courses'
                                        }
                                    ]
                                }]
                            };

                            budgetChart.setOption(option);
                        });
                    </script>
                </div>
            </div>

            <!-- Website Traffic -->
            <div class="card">
                

                <div class="card-body pb-0">
                <h5 class="card-title">Website Traffic <span>| Today</span></h5>

                <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                    echarts.init(document.querySelector("#trafficChart")).setOption({
                        tooltip: {
                        trigger: 'item'
                        },
                        legend: {
                        top: '5%',
                        left: 'center'
                        },
                        series: [{
                        name: 'Access From',
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
                        data: [{
                            value: 1048,
                            name: 'Search Engine'
                            },
                            {
                            value: 735,
                            name: 'Direct'
                            },
                            {
                            value: 580,
                            name: 'Email'
                            },
                            {
                            value: 484,
                            name: 'Union Ads'
                            },
                            {
                            value: 300,
                            name: 'Video Ads'
                            }
                        ]
                        }]
                    });
                    });
                </script>

                </div>
            </div><!-- End Website Traffic -->

            <!-- News & Updates Traffic -->
            

            </div><!-- End Right side columns -->

        </div>
        </section>

    </main><!-- End #main -->
</x-app-layout>
