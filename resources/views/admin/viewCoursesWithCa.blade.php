<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1></h1>
        <nav>
            
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Courses With Continous Assessments</h5>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by date uploaded.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Course Code</th>
                                    <th class="px-4 py-2">Firstname</th>
                                    <th class="px-4 py-2">Lastname</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Programme Name</th>
                                    <th class="px-4 py-2">Course Name</th>
                                    <th class="px-4 py-2">School</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                    <tr class="border-t border-b hover:bg-gray-100">
                                        <td class="px-4 py-2"><a href="{{route('coordinator.showCaWithin',encrypt($result->ID))}}">{{$result->CourseName}}</a></td>
                                        <td class="px-4 py-2">{{$result->Firstname}}</td>
                                        <td class="px-4 py-2">{{$result->Surname}}</td>
                                        <td class="px-4 py-2">{{$result->PrivateEmail}}</td>
                                        <td class="px-4 py-2">{{$result->Name}}</td>  
                                        <td class="px-4 py-2">{{$result->CourseDescription}}</td>
                                        <td class="px-4 py-2">{{$result->SchoolName}}</td>                                                                                                          
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