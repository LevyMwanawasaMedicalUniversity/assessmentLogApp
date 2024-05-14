<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Deans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="table-auto w-full mt-4">
                        <thead>
                            <form method="post" action="{{ route('admin.importDeans')}}">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-r-none">
                                    Import
                                </button>
                            </form>
                            <tr>
                                <th class="px-4 py-2">Firstname</th>
                                <th class="px-4 py-2">Lastname</th>
                                <th class="px-4 py-2">School</th>
                                <th class="px-4 py-2">Number Of Programmes</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                            <tr class="border-t border-b hover:bg-gray-100">
                                <td class="px-4 py-2">{{$result->FirstName}}</td>
                                <td class="px-4 py-2">{{$result->Surname}}</td>
                                <td class="px-4 py-2">{{$result->SchoolName}}</td>
                                <td class="px-4 py-2">{{$counts[$result->ID]}}</td>
                                <td class="px-4 py-2">
                                    <form method="GET" action="{{ route('admin.viewCoordinatorsUnderDean', ['schoolId' => encrypt($result->ParentID)]) }}">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            View
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