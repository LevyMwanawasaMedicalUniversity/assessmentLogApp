<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Coordinators') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="table-auto w-full mt-4">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Firstname</th>
                                <th class="px-4 py-2">Lastname</th>
                                <th class="px-4 py-2">Programme Coordinated</th>
                                <th class="px-4 py-2">Number Of Courses</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                            <tr class="border-t border-b hover:bg-gray-100">
                                <td class="px-4 py-2">{{$result->Firstname}}</td>
                                <td class="px-4 py-2">{{$result->Surname}}</td>
                                <td class="px-4 py-2">{{$result->Name}}</td>
                                <td class="px-4 py-2">{{$counts[$result->ID]}}</td>
                                <td class="px-4 py-2">
                                    <form method="GET" action="{{ route('admin.viewCoordinatorsCourses', ['basicInformationId' => encrypt($result->ID)]) }}">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            View User
                                        </button>
                                    </form>
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
</x-app-layout>