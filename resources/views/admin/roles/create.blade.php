<x-app-layout>
    <main id="main" class="main">

    <div class="pagetitle">
        <h1>Add Role</h1>
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
                        <h5 class="card-title">Add Role</h5>

                        <!-- Vertical Form -->
                        <form method="POST" action="{{ route('roles.store') }}" class="row g-3">
                            @csrf
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input value="{{ old('name') }}" 
                                    type="text" 
                                    class="form-control" 
                                    name="name" 
                                    placeholder="Name" required>
                            </div>

                            <label for="permissions" class="form-label">Assign Permissions</label>

                            <table class="table table-striped">
                                <thead>
                                    <th scope="col" width="1%"></th>
                                    <th scope="col" width="20%">Name</th>
                                    <th scope="col" width="1%">Guard</th> 
                                </thead>

                                @foreach($permissions as $permission)
                                    <tr>
                                        <td>
                                            <input type="checkbox" 
                                            name="permission[{{ $permission->name }}]"
                                            value="{{ $permission->name }}"
                                            class='permission'>
                                        </td>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->guard_name }}</td>
                                    </tr>
                                @endforeach
                            </table>                          

                            <div class="text-center">
                                <a href="{{ route('roles.index') }}"><button type="button" class="btn btn-secondary font-weight-bold py-2 px-4 rounded-0">Back</button></a>
                                <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">Save Role</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

</x-app-layout>