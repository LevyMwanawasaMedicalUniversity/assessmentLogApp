<x-app-layout>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Coordinators</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item active">Coordinators</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Coordinators</h5>
                            <form method="post" action="{{ route('admin.importCoordinators')}}">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-md">
                                Import
                            </button>
                            </form>
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                        {{-- <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"> --}}
                        <thead>
                            <tr>
                            
                            {{-- <th scope="col">#</th> --}}
                            <th scope="col">Firstname</th>
                            <th scope="col">Lastname</th>
                            <th scope="col">Programme Coordinated</th>
                            <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($results as $result)
                            @include('coordinator.components.uploadAssessmentTypeModal')
                            @include('coordinator.components.viewAssessmentTypeModal')
                            <tr>
                                {{-- <th scope="row">1</th> --}}
                                <td>{{$result->Firstname}}</td>
                                <td>{{$result->Surname}}</td>
                                <td>{{$result->Name}}</td>
                                <td>
                                    <form method="GET" action="{{ route('admin.viewCoordinatorsCourses', ['basicInformationId' => encrypt($result->ID)]) }}">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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