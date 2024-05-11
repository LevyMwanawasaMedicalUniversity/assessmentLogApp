<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table id="myTable" class="table-auto w-full mt-4">
                    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-2/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Course Name</th>
                                <th class="px-4 py-2">Course Code</th>
                                <th class="px-4 py-2">Programme Name</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                @include('coordinator.components.assessmentTypeModal')
                                <tr>
                                    <td class="border px-4 py-2">{{$result->CourseDescription}}</td>
                                    <td class="border px-4 py-2">{{$result->CourseName}}</td>
                                    <td class="border px-4 py-2">{{$result->Name}}</td>
                                    @if(strpos(strtoupper($result->CourseName), 'OSCE') !== 0)
                                    <td class="border px-4 py-2">
                                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" data-toggle="modal" data-target="#viewCourseModal{{ $result->ID }}" data-courseid="{{ $result->ID }}">
                                            View
                                        </button>
                                    </td>
                                    @else
                                    <td class="border px-4 py-2">
                                        <form method="GET" action="{{ route('coordinator.uploadCa', ['statusId' => 4, 'courseIdValue' => $result->ID]) }}">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" data-toggle="modal">
                                                View
                                            </button>
                                        </form>
                                    </td>
                                    
                                    @endif                                    
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