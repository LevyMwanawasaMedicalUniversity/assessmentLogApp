<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1>Permissions</h1>
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
                            <h5 class="card-title">Permissions</h5>
                            <a class="btn btn-primary font-weight-bold" href="{{ route('permissions.create') }}">Add Permissions</a>
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Auth Guard</th>
                                    <th scope="col" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($permissions) > 0)
                                    @foreach($permissions as $permission)
                                        <tr class="border-top border-bottom hover">
                                            <td>{{ $permission->name }}</td>
                                            <td>{{ $permission->guard_name }}</td>
                                            <td>
                                                <div class="btn-group float-end" role="group" aria-label="Button group">
                                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary font-weight-bold">
                                                        Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('permissions.destroy', $permission->id) }}" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger font-weight-bold" onclick="return confirm('Are you sure you want to delete this item?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <h3>No Permissions.</h3>
                                        </td>
                                    </tr>
                                @endif
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
