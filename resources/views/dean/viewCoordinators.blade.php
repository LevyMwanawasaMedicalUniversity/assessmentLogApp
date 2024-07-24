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
                            <h5 class="card-title">Coordinators</h5>
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
                                        <th scope="col">Last Login<BR>(Since 24-07-2024)</th>
                                        <th scope="col">Courses Coordinated <span class="text-primary"> {{ $totalCoursesCoordinated }} </span></th>
                                        <th scope="col">Courses With CA <span class="text-success"> {{$totalCoursesWithCA}} </span></th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        @include('coordinator.components.uploadAssessmentTypeModal')
                                        @include('coordinator.components.viewAssessmentTypeModal')
                                        <tr>
                                            {{-- <th scope="row">1</th> --}}
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $result->Firstname }}</td>
                                            <td>{{ $result->Surname }}</td>
                                            <td>{{ $result->Name }}</td>
                                            <td style="color: {{ $result->last_login_at ? 'blue' : 'red' }};">
                                                {{ $result->last_login_at ? $result->last_login_at : 'NEVER' }}
                                            </td>
                                            <td>{{ $counts[$result->ID] ?? '0' }} Courses</td>
                                            <td><a href="{{route('coordinator.viewOnlyProgrammesWithCaForCoordinator',$result->ID)}}">{{ $withCa[$result->ID] ?? '0' }} Courses</a></td>
                                            <td>
                                                <form method="GET" action="{{ route('admin.viewCoordinatorsCourses', ['basicInformationId' => encrypt($result->ID)]) }}">
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
</x-app-layout>
