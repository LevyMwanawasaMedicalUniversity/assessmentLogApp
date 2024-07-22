<x-app-layout>
    <main id="main" class="main">

    <div class="pagetitle">
        <h1>Users</h1>
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
                            <h5 class="card-title">Audit Trails</h5>
                            
                        </div>
                        <!-- Table with hoverable rows -->
                        <div style="overflow-x:auto;">
                            <table id="myTable" class="table table-hover">
                                {{-- <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"> --}}
                                <thead>
                                    <tr>
                                        <th scope="col">Audit ID</th>
                                        <th scope="col">Auditable Type</th>
                                        <th scope="col">Audit Event</th>
                                        <th scope="col">User Name</th>
                                        <th scope="col">Email Address</th>
                                        <th scope="col">Date and Time</th>
                                        <th scope="col">IP Address</th>
                                        <th scope="col">Old Values</th>
                                        <th class="text-end">New Values</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($audits as $audit)
                                        <tr>
                                            <td>{{ $audit->id }}</td>
                                            <td>@if(isset($audit->auditable_type)){{ $audit->auditable_type }}@endif</td>
                                            <td>@if(isset($audit->event)){{ $audit->event }}@endif</td>
                                            <td>@if(isset($audit->user->name)){{ $audit->user->name }}@endif</td>
                                            <td>@if(isset($audit->user->email)){{ $audit->user->email }}@endif</td>
                                            <td>{{ $audit->created_at }}</td>
                                            <td>@if(isset($audit->ip_address)){{ $audit->ip_address }}@endif</td>
                                            <td>@if(isset($audit->old_values)){{ json_encode($audit->old_values) }}@endif</td>
                                            <td class="text-end">@if(isset($audit->new_values)){{ json_encode($audit->new_values) }}@endif</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $audits->links('pagination::bootstrap-4') }}
                        <!-- End Table with hoverable rows -->

                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->
    

    

</x-app-layout>