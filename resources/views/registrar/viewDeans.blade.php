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
                                    <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                        Import
                                    </button>
                                </form>

                                <form method="post" action="{{ route('admin.refreshCAs') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary font-weight-bold py-2 px-4 rounded-0">
                                        Refresh CAs
                                    </button>
                                </form>
                            @endif
                        </div>
                        <!-- Table with hoverable rows -->
                        <div style="overflow-x:auto;">
                            <table id="myTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Firstname</th>
                                        <th scope="col">Lastname</th>
                                        <th scope="col">Last Login<BR>(Since 24-07-2024)</th>
                                        <th scope="col">School</th>
                                        <th scope="col" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        <tr class="border-top border-bottom hover">
                                            @php
                                                $user = \App\Models\User::where('basic_information_id', $result->basicInformationId)->first();
                                            @endphp
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $result->FirstName }}</td>
                                            <td>{{ $result->Surname }}</td>
                                            <td style="color: {{ $user && $user->last_login_at ? 'blue' : 'red' }};">
                                                {{ $user && $user->last_login_at ? $user->last_login_at : 'NEVER' }}
                                            </td>
                                            <td>{{ $result->SchoolName }}</td>
                                            <td class="text-end">
                                                <form method="GET" action="{{ route('admin.viewCoordinatorsUnderDean', ['schoolId' => encrypt($result->ParentID)]) }}">
                                                    <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
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
