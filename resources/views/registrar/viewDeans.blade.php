<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1>Deans</h1>
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
                            <h5 class="card-title">Deans</h5>
                            @if(auth()->user()->hasPermissionTo('Administrator'))
                                <form method="post" action="{{ route('admin.importDeans') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success font-weight-bold">
                                        Import
                                    </button>
                                </form>
                            @endif
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Firstname</th>
                                    <th scope="col">Lastname</th>
                                    <th scope="col">School</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                    <tr class="border-top border-bottom hover">
                                        <td>{{ $result->FirstName }}</td>
                                        <td>{{ $result->Surname }}</td>
                                        <td>{{ $result->SchoolName }}</td>
                                        <td>
                                            <form method="GET" action="{{ route('admin.viewCoordinatorsUnderDean', ['schoolId' => encrypt($result->ParentID)]) }}">
                                                <button type="submit" class="btn btn-primary font-weight-bold">
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
</x-app-layout>
