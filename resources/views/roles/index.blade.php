<x-app-layout>
    <main id="main" class="main">

    <div class="pagetitle">
        <h1>Roles</h1>
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
                            <a class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{ route('roles.create') }}">Add Role</a>
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                        <thead>
                            <tr>
                            <th scope="col">Name</th>
                            <th scope="col" class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($roles ) > 0)
                            @foreach ($roles as $key => $role)
                            <tr class="border-t border-b hover:bg-gray-100">
                                <td class="px-4 py-2">{{ $role->name }}</td>
                                <td class="px-4 py-2">
                                    <div class="btn-group flex justify-end" role="group" aria-label="Button group">
                                        {{-- <a type="button" href="{{ route('roles.show', $role->id) }}">
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Show
                                            </button>
                                        </a> --}}
                                        <a type="button" href="{{ route('roles.edit', $role->id) }}">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Edit
                                            </button>
                                        </a>
                                        <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display: inline">
                                            @csrf
                                            @method('DELETE')                                            
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-l-none" onclick="return confirm('Are you sure you want to delete this item?')">
                                                    Delete
                                                </button>                                              
                                        </form> 
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <h3 class="text-center">No Roles.</h3>
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