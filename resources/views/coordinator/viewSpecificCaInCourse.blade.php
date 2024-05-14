<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{$assessmentType }}s for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}}
        </h2>
    </x-slot>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table id="myTable" class="table-auto w-full mt-4">
                    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by student number.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Student Number</th>
                                <th class="px-4 py-2">Course Code</th>                                
                                <th class="px-4 py-2">Academic Year</th>
                                <th class="px-4 py-2">Mark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)                                
                                <tr class="border-t border-b hover:bg-gray-100">
                                    <td class="px-4 py-2">{{$result->student_id }}</td>
                                    <td class="px-4 py-2">{{$result->course_code}}</td>                                    
                                    <td class="px-4 py-2">{{$result->academic_year}}</td> 
                                    <td class="px-4 py-2">{{$result->cas_score}}</td>                                                                                                        
                                </tr>
                            @endforeach
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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