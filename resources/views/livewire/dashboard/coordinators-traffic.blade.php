<div class="card">
    <div class="card-body">
        <h5 class="card-title">Coordinators Traffic <span>| Number of unique coordinators per school</span></h5>

        <div wire:init="loadData">
            @if(!$readyToLoad)
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                        <h6>{{ $coordinatorsCount }}</h6>
                        <span class="text-success small pt-1 fw-bold">Total unique coordinators</span>
                    </div>
                </div>

                <!-- Traffic Chart -->
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
                                name: 'Coordinators per School',
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
                                data: (() => {
                                    const schools = @json($schoolNames);
                                    const counts = @json($userCounts);
                                    return schools.map((school, index) => {
                                        return {
                                            value: counts[index],
                                            name: school
                                        };
                                    });
                                })()
                            }]
                        });
                    });
                </script>
                <!-- End Traffic Chart -->
            @endif
        </div>
    </div>
</div>
