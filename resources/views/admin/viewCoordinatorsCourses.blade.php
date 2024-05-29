<x-app-layout>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>My Courses</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item active">Courses</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Your Courses</h5>
                        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-1 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-9">
                    </div>

                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                        
                        <thead>
                            <tr>
                            {{-- <th scope="col">#</th> --}}
                            <th scope="col">Course Name</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Programme Name</th>
                            <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($results as $result)
                            @include('coordinator.components.uploadAssessmentTypeModal')
                            @include('coordinator.components.viewAssessmentTypeModal')
                            <tr>
                                {{-- <th scope="row">1</th> --}}
                                <td>{{$result->CourseDescription}}</td>
                                <td>{{$result->CourseName}}</td>
                                <td>{{$result->Name}}</td>
                                @if(strpos(strtoupper($result->CourseName), 'OSCE') !== 0)
                                    <td>
                                        <div class="btn-group flex" role="group" aria-label="Button group">
                                            <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-r-none" data-toggle="modal" data-target="#uploadCourseModal{{ $result->ID }}" data-courseid="{{ $result->ID }}">
                                                Upload
                                            </button>
                                            <button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-l-none" data-toggle="modal" data-target="#viewCourseModal{{ $result->ID }}" data-courseid="{{ $result->ID }}">
                                                View
                                            </button>
                                        </div>
                                    </td>
                                @else
                                    <td>
                                        <div class="btn-group flex" role="group" aria-label="Button group">
                                            <a href="{{ route('coordinator.uploadCa', ['statusId' => encrypt(4), 'courseIdValue' => encrypt($result->ID)]) }}">
                                                <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-r-none">
                                                    Upload
                                                </button>
                                            </a>
                                            <a href="{{ route('coordinator.viewAllCaInCourse', ['statusId' => encrypt(4), 'courseIdValue' => encrypt($result->ID)]) }}">
                                                <button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                    View
                                                </button>
                                            </a>
                                        </div>
                                    </td>                                    
                                    @endif 
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
<script>
    function myFunction() {
        var input, filter, table, tr, td, i, txtValue1, txtValue2;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td1 = tr[i].getElementsByTagName("td")[0];
            td2 = tr[i].getElementsByTagName("td")[1];
            if (td1) {
            txtValue1 = td1.textContent || td1.innerText;
            txtValue2 = td2.textContent || td2.innerText;
            if (txtValue1.toUpperCase().indexOf(filter) > -1 || txtValue2.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
            }       
        }
    }
</script>
</x-app-layout>