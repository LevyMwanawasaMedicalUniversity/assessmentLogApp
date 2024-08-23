<x-app-layout>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Coordinators</h1>
        @include('layouts.alerts')
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
                            <h5 class="card-title">Coordinators @isset($schoolId) in {{$results->first()->SchoolName}} @else on Edurole @endif</h5>
                            <div class=""> 
                                <button class="btn btn-info" id="exportBtn">Export to Excel</button>
                            </div>
                            @if(auth()->user()->hasPermissionTo('Administrator'))
                                <form method="post" action="{{ route('admin.importCoordinators') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success font-weight-bold">
                                        Import
                                    </button>
                                </form>
                            @endif
                            
                        </div>
                        <!-- Table with hoverable rows -->
                        <div style="overflow-x:auto;">
                            <table id="myTable" class="table table-hover">
                                {{-- <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"> --}}
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Firstname</th>
                                        <th scope="col">Lastname</th>
                                        <th scope="col">Programme Coordinated</th>
                                        <th scope="col">School</th>
                                        <th scope="col">Last Login</th>
                                        <th scope="col">Courses in @isset($schoolId) {{$results->first()->SchoolName}} @else Edurole @endif<span class="text-primary"> {{ $totalCoursesCoordinated }} </span></th>
                                        <th scope="col">Courses With CA <span class="text-success"> {{$totalCoursesWithCA}} </span></th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        @include('coordinator.components.uploadAssessmentTypeModal')
                                        @include('coordinator.components.viewAssessmentTypeModal')
                                        <tr>
                                            @php
                                                $user = \App\Models\User::where('basic_information_id', $result->basicInformationId)->first();
                                            @endphp
                                            {{-- <th scope="row">1</th> --}}
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $result->Firstname }}</td>
                                            <td>{{ $result->Surname }}</td>
                                            <td>{{ $result->Name }}</td>
                                            <td>{{ $result->School }}</td>
                                            <td style="color: {{ $user && $user->last_login_at ? 'blue' : 'red' }};">
                                                {{ $user && $user->last_login_at ? $user->last_login_at : 'NEVER' }}
                                            </td>
                                            <td>{{ $counts[$result->StudyID] ?? '0' }} Courses</td>
                                            <td>
                                                <form action="{{ route('coordinator.viewOnlyProgrammesWithCaForCoordinator', $result->basicInformationId) }}" method="GET">
                                                    <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;">
                                                        {{ $withCa[$result->StudyID] ?? '0' }} Courses
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <form method="GET" action="{{ route('admin.viewCoordinatorsCourses', ['basicInformationId' => encrypt($result->basicInformationId)]) }}">
                                                    <button type="submit" class="btn btn-primary font-weight-bold">
                                                        View
                                                    </button>
                                                </form>
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
    document.getElementById('exportBtn').addEventListener('click', function() {
        var table = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
        XLSX.writeFile(wb, "ALL COORDINATORS.xlsx");
    });
</script>
</x-app-layout>

