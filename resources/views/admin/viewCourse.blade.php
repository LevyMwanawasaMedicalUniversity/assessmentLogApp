<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $results->CourseDescription }} {{ $results->CourseName }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mx-auto px-4">
                        <div class="flex flex-wrap -mx-4">
                            <div class="w-full px-4">
                                {{-- <div class="bg-white shadow rounded-lg"> --}}
                                    {{-- <div class="inline-flex">
                                        <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-l">
                                            Prev
                                        </button>
                                        <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-r">
                                            Next
                                        </button>
                                    </div> --}}
                                    @if(strpos(strtoupper($results->CourseName), 'OSCE') !== 0)
                                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                            <div class="grid grid-cols-3 gap-6"> <!-- Grid with 3 equal-width columns -->
                                                <!-- First Block -->
                                                <form method="POST" action="{{ route('coordinator.uploadCa') }}">
                                                    @csrf
                                                    <input type="hidden" name="statusId" value="1">
                                                    <input type="hidden" name="courseId" value="{{ $results->ID }}">
                                                    <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid red;">
                                                        <div class="p-6 text-gray-900">
                                                            {{ __("Assignment") }}
                                                        </div>
                                                    </button>
                                                </form>

                                                <!-- Second Block -->
                                                <form method="POST" action="{{ route('coordinator.uploadCa') }}">
                                                    @csrf
                                                    <input type="hidden" name="statusId" value="2">
                                                    <input type="hidden" name="courseId" value="{{ $results->ID }}">
                                                    <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid green;">
                                                        <div class="p-6 text-gray-900">
                                                            {{ __("Test") }}
                                                        </div>
                                                    </button>
                                                </form>

                                                <!-- Third Block -->
                                                <form method="POST" action="{{ route('coordinator.uploadCa') }}">
                                                    @csrf
                                                    <input type="hidden" name="statusId" value="3">
                                                    <input type="hidden" name="courseId" value="{{ $results->ID }}">
                                                    <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid blue;">
                                                        <div class="p-6 text-gray-900">
                                                            {{ __("Mock") }}
                                                        </div>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        @include('coordinator.components.excelSheetorm')
                                    @endif
                            </div>
                        </div>
                    </div>                   
                </div>
            </div>
        </div>
    </div>
</x-app-layout>