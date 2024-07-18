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
                            <h5 class="card-title">Users</h5>
                            <form action="{{ route('users.index') }}" method="GET" class="flex space-x-4 items-end">
                                @csrf
                                <div class="flex items-center">
                                    <input type="text" name="name" id="name" class="shadow appearance-none border rounded-l w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline flex-grow h-10" placeholder="Enter email address">
                                    <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-end" style="height: 2.5rem;">Search</button>                                
                                </div>                             
                            </form>
                            <a class="d-inline-block btn btn-primary font-weight-bold py-2 px-4 rounded" href="{{ route('users.create') }}">Add user</a>                            
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                        {{-- <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"> --}}
                        <thead>
                            <tr>
                            
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Creation date</th>
                            <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            
                            <tr>
                                {{-- <th scope="row">1</th> --}}
                                <td>{{$loop->iteration}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->created_at}}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Button group">
                                        <form method="POST" action="{{ route('users.resetUserPassword', $user->id) }}" class="d-inline-block">
                                            @csrf
                                            <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                                Reset
                                            </button>
                                        </form>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                            Edit
                                        </a>
                                    </div>
                                </td> 
                            </tr>                            
                            @endforeach
                            
                        </tbody>
                        </table>
                        {!! $users->links('pagination::bootstrap-4') !!}
                        <!-- End Table with hoverable rows -->

                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->
    

    

</x-app-layout>