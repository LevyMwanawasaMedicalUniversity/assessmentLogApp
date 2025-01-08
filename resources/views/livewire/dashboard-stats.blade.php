<div>
    <div class="pagetitle">
        <h1>Dashboard</h1>
        @include('layouts.alerts')
        <nav>
            {{ Breadcrumbs::render() }}
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">
                    <!-- Students Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <h5 class="card-title">Students With CA <span>| Uploaded</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $uniqueStudentCount }}</h6>
                                        <span class="text-success small pt-1 fw-bold">From</span> 
                                        <span class="text-muted small pt-2 ps-1">Assessments System</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">
                            <div class="card-body">
                                <h5 class="card-title">Courses from Edurole <span>| Total</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-calendar2-week-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $totalCoursesCoordinated }}</h6>
                                        <span class="text-primary small pt-1 fw-bold">With Coordinators Assigned</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LMMAX Courses Card -->
                    <div class="col-xxl-4 col-xl-12">
                        <div class="card info-card customers-card">
                            <div class="card-body">
                                <h5 class="card-title">Courses From LM-MAX <span>| Total</span></h5>
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
                    </div>

                    <!-- Programme Chart -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Course With CA Per Programme</h5>
                                <div id="columnChart"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Deans Table -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">
                            <div class="card-body">
                                <h5 class="card-title">Deans Per School</h5>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th scope="col">First Name</th>
                                            <th scope="col">Last Name</th>
                                            <th scope="col">School</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deansData as $dean)
                                            <tr>
                                                <td>{{$dean->FirstName}}</td>
                                                <td>{{$dean->Surname}}</td>
                                                <td>{{$dean->SchoolName}}</td>
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
                    </div>
                </div>
            </div>

            <!-- Right side columns -->
            <div class="col-lg-4">
                <!-- School Stats -->
                <div class="card">
                    <div class="card-body">
                        <div class="justify-between d-flex justify-content-between">
                            <h5 class="card-title">CA PER SCHOOL</h5>
                            <button wire:click="exportToCSV" class="btn btn-primary mb-3 mt-3">Export to CSV</button>
                        </div>
                        <div id="verticalBarChart" style="min-height: 400px;" class="echart"></div>
                    </div>
                </div>

                <!-- Coordinators Chart -->
                <div class="card">
                    <div class="card-body pb-0">
                        <h5 class="card-title">Number Of Coordinators: {{ $coursesFromEdurole->unique('username')->count() }}</h5>
                        <div id="trafficChart" style="min-height: 400px;" class="echart"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // Initialize charts when Livewire loads
        document.addEventListener('livewire:load', function () {
            initializeCharts();
        });

        // Re-initialize charts when Livewire updates
        Livewire.on('chartDataUpdated', function () {
            initializeCharts();
        });

        function initializeCharts() {
            // Column Chart
            new ApexCharts(document.querySelector("#columnChart"), {
                series: [{
                    name: 'Courses with CA',
                    data: @json($coursesWithCAProgrammeCounts)
                }, {
                    name: 'Courses from Edurole',
                    data: @json($coursesFromEduroleProgrammeCounts)
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
                    categories: @json($programmeCodes),
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

            // School Stats Chart
            echarts.init(document.querySelector("#verticalBarChart")).setOption({
                title: { text: '' },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' }
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
                    data: Object.keys(@json($schoolStats))
                },
                series: [
                    {
                        name: 'Courses with CA',
                        type: 'bar',
                        data: Object.values(@json($schoolStats)).map(s => s.coursesWithCA)
                    },
                    {
                        name: 'Total Courses',
                        type: 'bar',
                        data: Object.values(@json($schoolStats)).map(s => s.totalCourses)
                    }
                ]
            });

            // Traffic Chart
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
                    data: Object.entries(@json($userCountsPerSchool)).map(([name, value]) => ({
                        value,
                        name
                    }))
                }]
            });
        }

        // Watch for changes in data and refresh charts
        Livewire.on('refreshCharts', () => {
            initializeCharts();
        });
    </script>
    @endpush
</div>
