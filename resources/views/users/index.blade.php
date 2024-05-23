<x-app-layout>
    <main id="main" class="main">

    <div class="pagetitle">
        <h1>Users</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item active">Users</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="justify-between d-flex justify-content-between">
                            <h5 class="card-title">Users</h5>
                            
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                        {{-- <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"> --}}
                        <thead>
                            <tr>
                            
                            {{-- <th scope="col">#</th> --}}
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
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->created_at}}</td>
                                <td>
                                    <div class="btn-group flex" role="group" aria-label="Button group">
                                        <form method="POST" action="{{ route('users.resetUserPassword', $user->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Reset
                                            </button>
                                        </form>
                                        <a href="{{ route('users.edit', $user->id) }}">
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Edit
                                            </button>
                                        </a>
                                    </div>
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
    

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table id="myTable" class="table-auto w-full mt-4">
                        <div class="flex justify-between items-center">
                            
                            <form action="{{ route('users.index') }}" method="GET" class="flex space-x-4 items-end">
                                @csrf
                                <div class="flex items-left space-x-4">
                                    
                                    <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline flex-grow" placeholder="Enter student number or user name">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Search</button>
                                </div>                              
                            </form>
                            <a class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{ route('users.create') }}">Add user</a>
                        </div>
                        <thead>
                            <tr>
                                {{-- <th class="px-4 py-2">Profile</th> --}}
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Creation date</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                {{-- <th class="px-4 py-2">Profile</th> --}}
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Creation date</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            
                        </tfoot>
                        <tbody>
                            @foreach ($users as $user)
                            <tr class="border-t border-b hover:bg-gray-100">
                                {{-- <td class="px-4 py-2">
                                <span class="avatar avatar-sm rounded-circle">
                                    <img src="{{asset('assets')}}/img/default-avatar.png" alt="" style="max-width: 80px; border-radius: 100px">
                                </span>
                                </td> --}}
                                <td class="px-4 py-2">{{ $user->name }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">{{$user->created_at}}</td>
                                <td class="px-4 py-2">
                                    <div class="btn-group flex" role="group" aria-label="Button group">
                                        <form method="POST" action="{{ route('users.resetUserPassword', $user->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Reset
                                            </button>
                                        </form>
                                        <a href="{{ route('users.edit', $user->id) }}">
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Edit
                                            </button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $users->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>