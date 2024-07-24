<x-app-layout>
    <main id="main" class="main">

    <div class="pagetitle">
        <h1>Roles</h1>
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
                            <h5 class="card-title">Roles</h5>
                            <a class="btn btn-primary font-weight-bold" href="{{ route('roles.create') }}">Add Role</a>
                        </div>
                        <!-- Table with hoverable rows -->
                        <div style="overflow-x:auto;">
                            <table id="myTable" class="table table-hover">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col" class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($roles ) > 0)
                                        @foreach ($roles as $key => $role)
                                        <tr class="border-top border-bottom hover">
                                            <td>{{ $key + 1 }}</td>
                                            <td class="px-4 py-2">{{ $role->name }}</td>
                                            <td class="px-4 py-2">
                                                <div class="btn-group float-end" role="group" aria-label="Button group">
                                                    {{-- <a href="{{ route('roles.show', $role->id) }}" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                                        Show
                                                    </a> --}}
                                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary font-weight-bold">
                                                        Edit
                                                    </a>
                                                    {{-- <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger font-weight-bold" onclick="return confirm('Are you sure you want to delete this item?')">
                                                            Delete
                                                        </button>
                                                    </form> --}}
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center">
                                                <h3>No Roles.</h3>
                                            </td>
                                        </tr>
                                    @endif
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
