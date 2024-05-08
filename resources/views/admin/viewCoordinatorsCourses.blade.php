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
                    <table class="table-auto w-full mt-4">
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
                            <tr>
                                <td class="border px-4 py-2">{{$result->CourseDescription}}</td>
                                <td class="border px-4 py-2">{{$result->CourseName}}</td>
                                <td class="border px-4 py-2">{{$result->Name}}</td>
                                <td class="border px-4 py-2">
                                    <form method="POST" action="{{ route('admin.viewCourse') }}">
                                        @csrf
                                        <input type="hidden" name="courseId" value="{{ $result->ID }}">
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