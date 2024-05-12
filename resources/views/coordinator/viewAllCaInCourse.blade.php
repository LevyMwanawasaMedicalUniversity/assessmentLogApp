<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{$assessmentType }}s for {{$courseDetails->CourseDescription}} - {{$courseDetails->Name}}
        </h2>
    </x-slot>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table id="myTable" class="table-auto w-full mt-4">
                    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by date uploaded.." class="shadow appearance-none border rounded w-2/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Upload Number</th>
                                <th class="px-4 py-2">Time Craeted</th>
                                <th class="px-4 py-2">Time Updated</th>
                                <th class="px-4 py-2">Academic Year</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)                                
                                <tr class="border-t border-b hover:bg-gray-100">
                                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2">{{$result->updated_at}}</td>
                                    <td class="px-4 py-2">{{$result->created_at}}</td>
                                    <td class="px-4 py-2">{{$result->academic_year}}</td> 
                                    <td class="px-4 py-2">
                                            <div class="btn-group flex" role="group" aria-label="Button group">
                                                <a href="{{ route('coordinator.viewSpecificCaInCourse', ['statusId' => encrypt($statusId), 'courseIdValue' => encrypt($result->course_assessments_id)]) }}">
                                                    <button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                        View
                                                    </button>
                                                </a>
                                                <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-r-none">
                                                    Edit
                                                </button>                                                
                                                <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>                                                                     
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
            td = tr[i].getElementsByTagName("td")[1]; // Change the index based on the column you want to filter
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