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
                            <h5 class="card-title">Number of CAs</h5>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by date uploaded.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Assessment Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assessmentDetails as $assessment)
                                <tr>
                                    <td><a href="{{ route('coordinator.viewAllCaInCourse', ['statusId' => encrypt($assessment->id),'courseIdValue' => encrypt($courseId),'basicInformationId' => encrypt($assessment->basic_information_id), 'delivery' => encrypt($assessment->delivery_mode)]) }}">{{ $assessment->assesment_type_name }}</a></td>
                                    <td>{{ $assessment->total }}</td>
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

</x-app-layout>