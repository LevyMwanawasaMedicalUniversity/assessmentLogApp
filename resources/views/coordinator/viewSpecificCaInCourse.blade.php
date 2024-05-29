<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{$assessmentType }}s for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}}
        </h2>
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
                            <h5 class="card-title">{{$assessmentType }}s</h5>
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by student number.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Student Number</th>
                                    <th class="px-4 py-2">FirstName</th>
                                    <th class="px-4 py-2">LastName</th> 
                                    <th class="px-4 py-2">Programme</th>
                                    <th class="px-4 py-2">School</th>                               
                                    <th class="px-4 py-2">Academic Year</th>
                                    <th class="px-4 py-2">Mark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                    @if($result->basic_information)
                                        <tr class="border-t border-b hover:bg-gray-100">
                                            <td class="px-4 py-2">{{$result->basic_information->ID }}</td>
                                            <td class="px-4 py-2">{{$result->basic_information->FirstName}}</td>
                                            <td class="px-4 py-2">{{$result->basic_information->Surname}}</td>
                                            <td class="px-4 py-2">{{$result->basic_information->Programme}}</td>
                                            <td class="px-4 py-2">{{$result->basic_information->School}}</td>                                            
                                            <td class="px-4 py-2">{{$result->academic_year}}</td>
                                            <td class="px-4 py-2">{{$result->cas_score}}</td>
                                        </tr>
                                    @endif
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
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0]; // Change the index based on the column you want to filter
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
</x-app-layout>